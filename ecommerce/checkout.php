<?php
require 'config.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];


$stmt = $pdo->prepare("SELECT username, email FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();


$stmt = $pdo->prepare("SELECT p.id, p.name, p.price, c.quantity 
                      FROM cart c 
                      JOIN products p ON c.product_id = p.id 
                      WHERE c.user_id = ?");
$stmt->execute([$user_id]);
$cartItems = $stmt->fetchAll();

if (empty($cartItems)) {
    $_SESSION['error'] = "Your cart is empty!";
    header("Location: cart.php");
    exit();
}


$total = array_sum(array_map(fn($i) => $i['price'] * $i['quantity'], $cartItems));

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // CSRF Protection
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error'] = "Invalid request!";
        header("Location: checkout.php");
        exit();
    }

    $username = htmlspecialchars(trim($_POST['username']));
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $phone = htmlspecialchars(trim($_POST['phone']));
    $address = htmlspecialchars(trim($_POST['address']));
    $pincode = htmlspecialchars(trim($_POST['pincode']));

    if (empty($username) || empty($email) || empty($phone) || empty($address) || empty($pincode)) {
        $_SESSION['error'] = "All fields are required!";
        header("Location: checkout.php");
        exit();
    }

    try {
        $pdo->beginTransaction();


        $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_price, username, email, address, phone, pincode, status) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, 'Pending')");
        $stmt->execute([$user_id, $total, $username, $email, $address, $phone, $pincode]);

    
        $order_id = $pdo->lastInsertId();
        $_SESSION['order_id'] = $order_id; 

        $pdo->commit();


        header("Location: payment.php");
        exit();
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['error'] = "Checkout failed: " . $e->getMessage();
        header("Location: checkout.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Checkout</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to right, #4b0082, #0000ff);
            color: white;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .checkout-container {
            width: 40%;
            padding: 20px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            box-shadow: 0px 0px 15px rgba(255, 255, 255, 0.3);
            border-radius: 12px;
            text-align: center;
            animation: fadeIn 1s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        h2 {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 15px;
            color: #FFD700;
        }

        input, textarea {
            width: 90%;
            padding: 12px;
            margin: 10px 0;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            transition: 0.3s;
            background: rgba(255, 255, 255, 0.8);
            color: black;
            text-align: center;
        }

        input:focus, textarea:focus {
            background: #ffffff;
            transform: scale(1.05);
            box-shadow: 0px 0px 10px rgba(255, 255, 255, 0.5);
            outline: none;
        }

        button {
            padding: 12px 20px;
            background: #00ffcc;
            color: black;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 18px;
            font-weight: bold;
            transition: 0.3s;
        }

        button:hover {
            background: #00bfa5;
            transform: scale(1.1);
        }

        .error {
            color: red;
            font-weight: bold;
            margin-bottom: 10px;
        }

        /* Responsive for Mobile */
        @media (max-width: 768px) {
            .checkout-container {
                width: 90%;
            }
        }
    </style>
</head>
<body>

    <div class="checkout-container">
        <h2>📦 Shipping Details</h2>

        <?php if (isset($_SESSION['error'])): ?>
            <p class="error"><?= $_SESSION['error']; ?></p>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <form method="POST">
            <?php $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); ?>
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">

            <input type="text" name="username" value="<?= htmlspecialchars($user['username'] ?? '') ?>" placeholder="Username" required>
            <input type="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" placeholder="Email" required>
            <input type="text" name="phone" placeholder="Phone Number" minlength="10" maxlength="10" required>
            <textarea name="address" placeholder="Shipping Address" required></textarea>
            <input type="text" name="pincode" placeholder="Pincode" required>
            <button type="submit">Place Order 💳</button>
        </form>
    </div>

</body>
</html>
