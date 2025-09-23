<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require 'config.php';

function getCartCount() {
    global $pdo;
    if (!isset($_SESSION['user_id'])) return 0;

    $stmt = $pdo->prepare("SELECT SUM(quantity) FROM cart WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetchColumn() ?: 0;
}
?>

<header>
    <nav class="navbar">
        <a href="index.php" class="logo">TechZone🔥</a>

        <div class="nav-links">
            <form class="search-form" action="index.php" method="GET">
                <input type="text" name="search" placeholder="🔍 Search..." required>
                <button type="submit">Go</button>
            </form>

            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="cart.php">🛒 Cart (<span id="cart-count"><?= getCartCount(); ?></span>)</a>
                <?php if (!empty($_SESSION['is_admin'])): ?>
                    <a href="admin/products.php">Admin Panel</a>
                <?php endif; ?>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>
            <?php endif; ?>
        </div>
    </nav>
</header>

<script>
    function updateCartCount() {
        fetch('get_cart_count.php')
            .then(response => response.json())
            .then(data => {
                if (data.cart_count !== undefined) {
                    document.getElementById('cart-count').innerText = data.cart_count;
                }
            })
            .catch(error => console.error('Error fetching cart count:', error));
    }
    updateCartCount();
</script>

<style>
    .navbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 20px;
        background: linear-gradient(to right, #4b0082, #0000ff);
        color: white;
    }

    .nav-links a {
        margin: 0 10px;
        color: white;
        text-decoration: none;
        font-weight: bold;
    }

    .search-form input {
        padding: 6px;
        border-radius: 5px;
        border: 1px solid #ccc;
    }

    .search-form button {
        padding: 6px 12px;
        background: white;
        border: none;
        cursor: pointer;
        font-weight: bold;
    }
</style>
