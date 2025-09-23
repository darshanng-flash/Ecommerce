<?php
session_start();
require 'config.php';


if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "You must be logged in to modify the cart."]);
    exit();
}

$userId = $_SESSION['user_id'];
$productId = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
$action = $_POST['action'] ?? '';

if (!$productId || !in_array($action, ['add', 'remove'])) {
    echo json_encode(["error" => "Invalid request."]);
    exit();
}

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("SELECT id FROM products WHERE id = ?");
    $stmt->execute([$productId]);
    if (!$stmt->fetch()) {
        throw new Exception("Product not found.");
    }

    if ($action === 'add') {
        $stmt = $pdo->prepare("INSERT INTO cart (user_id, product_id, quantity) 
                               VALUES (?, ?, 1) 
                               ON DUPLICATE KEY UPDATE quantity = quantity + 1");
        $stmt->execute([$userId, $productId]);

    } elseif ($action === 'remove') {
        $stmt = $pdo->prepare("SELECT quantity FROM cart WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$userId, $productId]);
        $cartItem = $stmt->fetch();

        if (!$cartItem) {
            throw new Exception("Item not in cart.");
        }

        if ($cartItem['quantity'] > 1) {
            $stmt = $pdo->prepare("UPDATE cart SET quantity = quantity - 1 WHERE user_id = ? AND product_id = ?");
            $stmt->execute([$userId, $productId]);
        } else {
            $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?");
            $stmt->execute([$userId, $productId]);
        }
    }

    $pdo->commit();

    echo json_encode([
        "success" => "Cart updated!",
        "cart_count" => getCartCount($pdo, $userId),
        "item_quantity" => getItemQuantity($pdo, $userId, $productId),
        "total_price" => getCartTotal($pdo, $userId)
    ]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(["error" => $e->getMessage()]);
}

function getCartCount($pdo, $userId) {
    $stmt = $pdo->prepare("SELECT SUM(quantity) FROM cart WHERE user_id = ?");
    $stmt->execute([$userId]);
    return $stmt->fetchColumn() ?: 0;
}

function getItemQuantity($pdo, $userId, $productId) {
    $stmt = $pdo->prepare("SELECT quantity FROM cart WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$userId, $productId]);
    return $stmt->fetchColumn() ?: 0;
}

function getCartTotal($pdo, $userId) {
    $stmt = $pdo->prepare("SELECT SUM(p.price * c.quantity) FROM cart c 
                           JOIN products p ON c.product_id = p.id 
                           WHERE c.user_id = ?");
    $stmt->execute([$userId]);
    return number_format($stmt->fetchColumn() ?: 0, 2);
}
?>
