<?php
session_start();
require '../config.php';


if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: ../index.php");
    exit();
}


if (isset($_GET['completed'])) {
    $order_id = $_GET['completed'];
    $stmt = $pdo->prepare("UPDATE orders SET status = 'Completed' WHERE id = ? AND status = 'Pending'");
    $stmt->execute([$order_id]);

    if ($stmt->rowCount() > 0) {
        header("Location: manage_orders.php");
        exit();
    } else {
        echo "<script>alert('Order status could not be updated.');</script>";
    }
}

if (isset($_GET['cancelled'])) {
    $order_id = $_GET['cancelled'];
    $stmt = $pdo->prepare("UPDATE orders SET status = 'Cancelled' WHERE id = ?");
    $stmt->execute([$order_id]);

    if ($stmt->rowCount() > 0) {
        header("Location: manage_orders.php");
        exit();
    } else {
        echo "<script>alert('Order status could not be updated.');</script>";
    }
}


$searchQuery = "";
$queryParams = [];
$sql = "
    SELECT orders.id, users.username, orders.total_price, orders.status, orders.quantity, 
           COALESCE(products.name, 'Unknown Product') AS product_name
    FROM orders
    JOIN users ON orders.user_id = users.id
    LEFT JOIN products ON orders.product_id = products.id";

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $searchQuery = '%' . $_GET['search'] . '%';
    $sql .= " WHERE orders.id LIKE ?";
    $queryParams[] = $searchQuery;
}

$sql .= " ORDER BY orders.id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($queryParams);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manage Orders</title>
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
            padding: 20px;
            border-radius: 10px;
            width: 90%;
            max-width: 1000px;
            margin: 20px auto;
            box-shadow: 0px 0px 10px rgba(255, 255, 255, 0.2);
        }

        h2 { margin-bottom: 20px; }

        .search-bar {
            margin-bottom: 15px;
        }

        input[type="text"] {
            padding: 10px;
            width: 250px;
            border-radius: 5px;
            border: none;
            margin-right: 10px;
        }

        button {
            padding: 10px 15px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            background: #008CBA;
            color: white;
            font-size: 16px;
        }

        button:hover {
            opacity: 0.8;
        }

        table {
            width: 90%;
            margin: 20px auto;
            border-collapse: collapse;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            overflow: hidden;
            text-align: center;
        }

        th, td {
            padding: 20px;
            border: 1px solid white;
            text-align: center;
            font-size: 18px;
        }

        th {
            background: rgba(255, 255, 255, 0.3);
        }

        .status {
            font-weight: bold;
            padding: 8px;
            border-radius: 5px;
            font-size: 18px;
        }

        .pending { color: orange; }
        .completed { color: green; }
        .cancelled { color: red; }

        .btn {
            padding: 12px 20px;
            border-radius: 8px;
            text-decoration: none;
            color: white;
            font-size: 16px;
            display: inline-block;
            margin: 5px;
            transition: 0.3s ease-in-out;
        }

        .btn-completed { background: green; }
        .btn-cancelled { background: red; }

        .btn:hover { opacity: 0.8; }
    </style>
</head>
<body>

<div class="navbar">
    <a href="admin_dashboard.php">🏠 Dashboard</a>
    <h2>Admin Panel - Manage Orders</h2>
    <a href="../logout.php">🚪 Logout</a>
</div>

<div class="container">
    <h2>Customer Orders</h2>

    <form method="GET" class="search-bar">
        <input type="text" name="search" placeholder="Search Order ID" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
        <button type="submit">Search</button>
    </form>


    <table>
        <tr>
            <th>Order ID</th>
            <th>Customer</th>
            <th>Product</th>
            <th>Quantity</th>
            <th>Total Price</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($orders as $order): ?>
        <tr>
            <td>#<?= $order['id'] ?></td>
            <td><?= htmlspecialchars($order['username']) ?></td>
            <td><?= htmlspecialchars($order['product_name']) ?></td>
            <td><?= $order['quantity'] ?></td>
            <td>$<?= number_format($order['total_price'], 2) ?></td>
            <td>
                <span class="status <?= strtolower($order['status']) ?>">
                    <?= htmlspecialchars($order['status']) ?>
                </span>
            </td>
            <td>
                <?php if ($order['status'] == 'Pending'): ?>
                    <a href="manage_orders.php?completed=<?= $order['id'] ?>" class="btn btn-completed">✅ Completed</a>
                    <a href="manage_orders.php?cancelled=<?= $order['id'] ?>" class="btn btn-cancelled">❌ Cancelled</a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>

</body>
</html>
