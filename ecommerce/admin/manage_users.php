<?php
session_start();
require '../config.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: ../index.php");
    exit();
}


$stmt = $pdo->query("SELECT id, username, email, is_admin FROM users ORDER BY id ASC");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manage Users</title>
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
            text-align: center;
            width: 90%;
            max-width: 1000px;
            margin: 20px auto;
            box-shadow: 0px 0px 10px rgba(255, 255, 255, 0.2);
            animation: fadeIn 1s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        h2 { margin-bottom: 20px; }

        table {
            width: 100%;
            margin: 20px auto;
            border-collapse: collapse;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            overflow: hidden;
            text-align: center;
            color: white;
        }

        th, td {
            padding: 15px;
            border: 1px solid white;
            text-align: center;
        }

        th {
            background: rgba(255, 255, 255, 0.3);
            font-size: 16px;
        }

        td {
            background: rgba(255, 255, 255, 0.1);
            font-size: 14px;
        }

        .role {
            font-weight: bold;
            padding: 5px;
            border-radius: 5px;
        }

        .admin { color: gold; }
        .user { color: cyan; }
    </style>
</head>
<body>


<div class="navbar">
    <a href="admin_dashboard.php">🏠 Dashboard</a>
    <h2>Admin Panel - View Users</h2>
    <a href="../logout.php">🚪 Logout</a>
</div>

<div class="container">
    <h2>User Management</h2>


    <table>
        <tr>
            <th>User ID</th>
            <th>Username</th>
            <th>Email</th>
            <th>Role</th>
        </tr>
        <?php if (!empty($users)): ?>
            <?php foreach ($users as $user): ?>
            <tr>
                <td>#<?= $user['id'] ?></td>
                <td><?= htmlspecialchars($user['username']) ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td>
                    <span class="role <?= $user['is_admin'] ? 'admin' : 'user' ?>">
                        <?= $user['is_admin'] ? 'Admin' : 'User' ?>
                    </span>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="4">No users found.</td>
            </tr>
        <?php endif; ?>
    </table>
</div>

</body>
</html>
