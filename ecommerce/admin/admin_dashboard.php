<?php
session_start(); 
require '../config.php';


if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: ../index.php"); 
    exit();
}


$admin_username = $_SESSION['username'] ?? "Admin";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - E-Commerce</title>
    <link rel="stylesheet" href="../styles.css">
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

        .navbar a {
            text-decoration: none;
            color: white;
            font-size: 18px;
            font-weight: bold;
            padding: 10px 20px;
            border-radius: 5px;
            transition: 0.3s;
        }

        .navbar a:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .container {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 30px;
            border-radius: 10px;
            text-align: center;
            width: 90%;
            max-width: 800px;
            margin: 20px auto;
            box-shadow: 0px 0px 10px rgba(255, 255, 255, 0.2);
            animation: fadeIn 1s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        h2 {
            margin-bottom: 20px;
        }

        .dashboard-links {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-top: 20px;
        }

        .dashboard-links a {
            display: block;
            background: #00ffcc;
            padding: 15px;
            color: black;
            font-size: 18px;
            font-weight: bold;
            border-radius: 5px;
            text-decoration: none;
            transition: 0.3s ease-in-out;
        }

        .dashboard-links a:hover {
            background: #00bfa5;
        }
    </style>
</head>
<body>


<div class="navbar">
    <a href="../index.php">🏠 Home</a>
    <h2>Admin Panel</h2>
    <a href="../logout.php">🚪 Logout</a>
</div>

<div class="container">
    <h2>Welcome, <?= htmlspecialchars($admin_username) ?>!</h2>
    <p>Manage your e-commerce store from here.</p>

    <div class="dashboard-links">
        <a href="manage_product.php">📦 Manage Products</a>
        <a href="manage_orders.php">📜 Manage Orders</a>
        <a href="manage_users.php">👥 View Users</a>
    </div>
</div>

</body>
</html>
