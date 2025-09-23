<?php
require 'config.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    echo "<script>
            alert('Please log in to view your cart.');
            window.location.href = 'login.php'; // Redirect to login page
          </script>";
    exit();
}

$userId = $_SESSION['user_id'];


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_id = $_POST['product_id'] ?? null;
    $action = $_POST['action'] ?? null;

    if ($product_id && ctype_digit($product_id) && in_array($action, ['add', 'remove'])) {
        if ($action === 'add') {
            $stmt = $pdo->prepare("INSERT INTO cart (user_id, product_id, quantity) 
                                   VALUES (?, ?, 1) 
                                   ON DUPLICATE KEY UPDATE quantity = quantity + 1");
            $stmt->execute([$userId, $product_id]);

        } elseif ($action === 'remove') {

            $stmt = $pdo->prepare("SELECT quantity FROM cart WHERE user_id = ? AND product_id = ?");
            $stmt->execute([$userId, $product_id]);
            $cartItem = $stmt->fetch();

            if ($cartItem && $cartItem['quantity'] > 1) {

                $stmt = $pdo->prepare("UPDATE cart SET quantity = quantity - 1 WHERE user_id = ? AND product_id = ?");
                $stmt->execute([$userId, $product_id]);
            } else {

                $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?");
                $stmt->execute([$userId, $product_id]);
            }

        }
    }
}

$stmt = $pdo->prepare("SELECT p.id, p.name, p.price, p.image_url, c.quantity, (p.price * c.quantity) AS total 
                       FROM cart c 
                       JOIN products p ON c.product_id = p.id 
                       WHERE c.user_id = ?");
$stmt->execute([$userId]);
$cartItems = $stmt->fetchAll();

$total = array_sum(array_column($cartItems, 'total'));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Shopping Cart</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(to right, #4b0082, #0000ff);
            color: white;
            text-align: center;
            margin: 0;
            padding: 0;
        }

        .navbar {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .brand {
            flex-grow: 1;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            cursor: pointer;
        }

        .right-links {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .cart-button, .logout-btn {
            background: #00ffcc;
            border: none;
            padding: 10px 15px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: 0.3s;
            color: black;
        }

        .cart-button:hover, .logout-btn:hover {
            background: #00bfa5;
        }

        .cart-container {
            max-width: 700px;
            margin: auto;
            background: rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0px 0px 15px rgba(255, 255, 255, 0.3);
        }

        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        table th, table td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }

        .cart-item {
            animation: fadeIn 0.5s ease-in-out;
        }

        .cart-image {
            width: 50px;
            height: 50px;
            border-radius: 5px;
        }

        .btn {
            padding: 8px 14px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            font-weight: bold;
            transition: all 0.3s;
        }

        .add-btn { background: #00ffcc; color: black; }
        .add-btn:hover { background: #00bfa5; }

        .remove-btn { background: rgb(255, 0, 0); color: white; }
        .remove-btn:hover { background: rgb(200, 0, 0); }

        .checkout-btn {
            padding: 12px 18px;
            background: #00ffcc;
            color: black;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin-top: 20px;
        }

        .checkout-btn:hover { background: #00bfa5; }

        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

<div class="navbar">
    <div class="brand" onclick="window.location.href='index.php'">TECHZONE</div>
    <div class="right-links">
        <button class="cart-button" onclick="window.location.href='cart.php'">
            🛒 Cart
        </button>
        <button class="logout-btn" onclick="window.location.href='logout.php'">
            🔓 Logout
        </button>
    </div>
</div>

<div class="cart-container">
    <h2>🛒 Your Cart</h2>

    <?php if (empty($cartItems)): ?>
        <p>Your cart is empty!</p>
    <?php else: ?>
        <table>
            <tr>
                <th>Product</th>
                <th>Image</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Subtotal</th>
                <th>Action</th>
            </tr>
            <?php foreach ($cartItems as $item): ?>
                <tr class="cart-item">
                    <td><?= htmlspecialchars($item['name']) ?></td>
                    <td><img src="<?= htmlspecialchars($item['image_url']) ?>" class="cart-image" alt="Product"></td>
                    <td><?= $item['quantity'] ?></td>
                    <td>₹<?= number_format($item['price'], 2) ?></td>
                    <td>₹<?= number_format($item['total'], 2) ?></td>
                    <td>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                            <button type="submit" name="action" value="add" class="btn add-btn">➕</button>
                            <button type="submit" name="action" value="remove" class="btn remove-btn">➖</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>

        <h3>Total: ₹<?= number_format($total, 2) ?></h3>
        <a href="checkout.php" class="checkout-btn">Proceed to Checkout</a>
        <form method="POST" style="display:inline;">
        
        </form>
    <?php endif; ?>
</div>

</body>
</html>
