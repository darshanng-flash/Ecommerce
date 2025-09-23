<?php
require 'config.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (isset($_SESSION['logout_message'])) {
    echo "<script>alert('" . $_SESSION['logout_message'] . "');</script>";
    unset($_SESSION['logout_message']); 
}

$isLoggedIn = isset($_SESSION['user_id']);
$isAdmin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;



$cartCount = 0;
if ($isLoggedIn) {
    $stmt = $pdo->prepare("SELECT SUM(quantity) AS total_items FROM cart WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $cartCount = $stmt->fetchColumn() ?: 0;
}


$search = trim($_GET['search'] ?? '');
$category = $_GET['category'] ?? '';

$query = "SELECT p.id, p.name, p.price, p.image_url, c.name AS category_name 
          FROM products p 
          JOIN categories c ON p.category_id = c.id 
          WHERE p.name LIKE :search";

$params = [':search' => "%$search%"];

if (!empty($category)) {
    $query .= " AND c.id = :category";
    $params[':category'] = $category;
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll();


$categories = $pdo->query("SELECT * FROM categories")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>TechZone - NextGen Gadgets</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to right, #4b0082, #0000ff);
            color: white;
            margin: 0;
            padding: 0;
            text-align: center;
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
            color: white;
            cursor: pointer;
        }

        .right-links {
    display: flex;
    align-items: center;
    gap: 15px;
    flex-wrap: nowrap; 
}

.cart-button {
    display: flex;
    align-items: center;
    justify-content: center;
    white-space: nowrap; 
    padding: 10px 15px;
}

.cart-count {
    margin-left: 5px; 
    padding: 3px 8px; 
}


        .cart-button, .logout-btn, .search-container button, .add-to-cart {
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

        .cart-button:hover, .logout-btn:hover, .search-container button:hover, .add-to-cart:hover {
            background: #00bfa5;
        }

        .cart-count {
            background: red;
            color: white;
            border-radius: 50%;
            padding: 5px 10px;
            font-size: 14px;
            font-weight: bold;
        }

        .search-container {
            background: rgba(255, 255, 255, 0.2);
            padding: 15px;
            border-radius: 10px;
            margin: 20px auto;
            width: 60%;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            box-shadow: 0 0 15px rgba(255, 255, 255, 0.3);
        }

        .search-container input, .search-container select {
            padding: 12px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            flex: 1;
        }

        .product-grid {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 30px;
            padding: 20px;
        }

        .product-card {
            background: rgba(255, 255, 255, 0.1);
            padding: 15px;
            border-radius: 10px;
            text-align: center;
            width: 250px;
            min-height: 400px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
        }

        .product-card img {
            max-width: 100%;
            max-height: 180px;
            border-radius: 10px;
            object-fit: cover;
        }

        .price {
            font-size: 20px;
            font-weight: bold;
            color: #FFD700;
            margin: 10px 0;
        }

        .add-to-cart {
            display: block;
            width: 100%;
            padding: 12px;
            font-size: 16px;
            font-weight: bold;
            margin-top: 10px;
        }
        .login-btn {
            width: 100%;
            background: #00ffcc;
            color: black;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 18px;
            transition: 0.3s ease;
        }

        .login-btn:hover {
            background: #00bfa5;
        }
    </style>
</head>
<body>

<div class="navbar">
    <div class="brand" onclick="window.location.href='index.php'">TECHZONE</div>
    <div class="right-links">
        <?php if ($isAdmin): ?>
            <a href="admin/admin_dashboard.php" class="cart-button">🔧 Admin Panel</a>
        <?php endif; ?>
        <button class="cart-button" onclick="window.location.href='cart.php'">
            🛒 Cart <span class="cart-count"><?= $cartCount ?></span>
        </button>
        <?php if ($isLoggedIn): ?>
            <button class="logout-btn" onclick="window.location.href='logout.php'">
                🔓 Logout
            </button>
        <?php else: ?>
            <a href="login.php" class="login-btn">🔑 Login</a>
        <?php endif; ?>
    </div>
</div>



<form class="search-container" action="index.php" method="GET">
    <input type="text" name="search" placeholder="🔍 Search products..." value="<?= htmlspecialchars($search) ?>">
    <select name="category">
        <option value="">All Categories</option>
        <?php foreach ($categories as $cat): ?>
            <option value="<?= $cat['id'] ?>" <?= ($cat['id'] == $category) ? 'selected' : '' ?>>
                <?= htmlspecialchars($cat['name']) ?>
            </option>
        <?php endforeach; ?>
    </select>
    <button type="submit">🔎 Search</button>
</form>


<div class="product-grid">
    <?php foreach ($products as $product): ?>
        <div class="product-card">
            <img src="<?= htmlspecialchars($product['image_url']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
            <h3><?= htmlspecialchars($product['name']) ?></h3>
            <span class="price">₹<?= number_format($product['price'], 2) ?></span>
            <button class="add-to-cart" data-id="<?= $product['id'] ?>">🛍️ Add to Cart</button>
        </div>
    <?php endforeach; ?>
</div>

<script>
$(document).ready(function() {
    $(".add-to-cart").click(function() {
        var productId = $(this).data("id");

        $.ajax({
            url: "update_cart.php",
            type: "POST",
            data: { product_id: productId, action: "add" },
            success: function(response) {
                console.log(response);
                alert("Added to cart!"); 
            },
            error: function() {
                alert("Error adding to cart.");
            }
        });
    });
});
</script>

</body>
</html>
