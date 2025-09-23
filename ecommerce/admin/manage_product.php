<?php
session_start();
require '../config.php';


$stmt = $pdo->query("SELECT id, name FROM categories");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);


if (isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $category_id = $_POST['category'];
    $price = $_POST['price'];


    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image = basename($_FILES['image']['name']);
        $target_dir = "../uploads/";
        $target_file = $target_dir . $image;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $image_url = "uploads/" . $image;
            $stmt = $pdo->prepare("INSERT INTO products (name, price, category_id, image_url) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $price, $category_id, $image_url]);
        } else {
            echo "<script>alert('File upload failed.');</script>";
        }
    } else {
        echo "<script>alert('Invalid file upload.');</script>";
    }
}


if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$id]);
}


$stmt = $pdo->query("SELECT products.*, categories.name AS category_name FROM products 
                     JOIN categories ON products.category_id = categories.id");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manage Products</title>
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

        .product-form {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
        }

        .product-form input, .product-form select, .product-form button {
            padding: 10px;
            border: none;
            border-radius: 5px;
            font-size: 14px;
        }

        .product-form select {
            flex: 1;
            min-width: 150px;
        }

        .product-form button {
            background: #00ffcc;
            cursor: pointer;
            transition: 0.3s;
        }

        .product-form button:hover { background: #00bfa5; }

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
            padding: 10px;
            border: 1px solid white;
            text-align: center;
        }

        th {
            background: rgba(255, 255, 255, 0.3);
        }

        td img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 5px;
        }

        .btn {
            padding: 5px 10px;
            border-radius: 5px;
            text-decoration: none;
            color: white;
        }

        .btn-edit { background: #008CBA; }
        .btn-delete { background: #f44336; }

        .btn:hover { opacity: 0.8; }
    </style>
</head>
<body>


<div class="navbar">
    <a href="admin_dashboard.php">🏠 Dashboard</a>
    <h2>Admin Panel - Manage Products</h2>
    <a href="../logout.php">🚪 Logout</a>
</div>

<div class="container">
    <h2>Manage Products</h2>


    <form action="" method="POST" enctype="multipart/form-data" class="product-form">
        <input type="text" name="name" placeholder="Product Name" required>
        <select name="category" required>
            <option value="" disabled selected>Select Category</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
            <?php endforeach; ?>
        </select>
        <input type="number" name="price" placeholder="Price" step="0.01" required>
        <input type="file" name="image" accept="image/*" required>
        <button type="submit" name="add_product">Add</button>
    </form>

    <table>
        <tr>
            <th>Image</th>
            <th>Name</th>
            <th>Category</th>
            <th>Price</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($products as $row): ?>
        <tr>
            <td>
                <?php if (!empty($row['image_url']) && file_exists("../" . $row['image_url'])): ?>
                    <img src="../<?= htmlspecialchars($row['image_url']) ?>" alt="<?= htmlspecialchars($row['name']) ?>">
                <?php else: ?>
                    <span style="color:red;">Image Not Found</span>
                <?php endif; ?>
            </td>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= htmlspecialchars($row['category_name']) ?></td>
            <td>$<?= number_format($row['price'], 2) ?></td>
            <td>
            <a href="edit_product.php?id=<?= $row['id'] ?>" class="btn btn-edit">Edit</a>
<a href="delete_product.php?product_id=<?= $row['id'] ?>" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete this product?')">Delete</a>

            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>

</body>
</html>
