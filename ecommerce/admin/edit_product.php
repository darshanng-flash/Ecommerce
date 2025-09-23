<?php
session_start();
require '../config.php';


if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: ../index.php");
    exit();
}


if (isset($_GET['id'])) {
    $product_id = $_GET['id'];
    

    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        echo "<script>alert('Product not found.'); window.location.href='manage_product.php';</script>";
        exit();
    }
} else {
    echo "<script>alert('No product ID provided.'); window.location.href='manage_product.php';</script>";
    exit();
}


$stmt = $pdo->query("SELECT id, name FROM categories");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $category_id = $_POST['category'];
    $price = $_POST['price'];
    $photo = $product['image_url']; 


    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        // Process the uploaded photo
        $targetDir = "../uploads/";
        $targetFile = $targetDir . basename($_FILES["photo"]["name"]);
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        
        if (getimagesize($_FILES["photo"]["tmp_name"])) {
            if (move_uploaded_file($_FILES["photo"]["tmp_name"], $targetFile)) {
                $photo = "uploads/" . basename($_FILES["photo"]["name"]);  // Update the photo URL
            } else {
                echo "<script>alert('Sorry, there was an error uploading your file.');</script>";
            }
        } else {
            echo "<script>alert('The file is not an image.');</script>";
        }
    }


    if (empty($name) || empty($category_id) || empty($price)) {
        echo "<script>alert('All fields are required.');</script>";
    } else {

        $stmt = $pdo->prepare("UPDATE products SET name = ?, category_id = ?, image_url = ?, price = ? WHERE id = ?");
        $stmt->execute([$name, $category_id, $photo, $price, $product_id]);

        if ($stmt->rowCount() > 0) {
            echo "<script>alert('Product updated successfully.'); window.location.href='manage_product.php';</script>";
        } else {
            echo "<script>alert('Product update failed.');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
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

        .product-form label {
            font-size: 14px;
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
    <h2>Edit Product</h2>
    <a href="../logout.php">🚪 Logout</a>
</div>

<div class="container">
    <h2>Edit Product Details</h2>

    <form method="POST" enctype="multipart/form-data" class="product-form">
        <input type="text" name="name" placeholder="Product Name" value="<?= htmlspecialchars($product['name']) ?>" required>
        

        <select name="category" required>
            <option value="" disabled>Select Category</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?= $category['id'] ?>" <?= $category['id'] == $product['category_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($category['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>


        <input type="number" name="price" placeholder="Product Price" value="<?= htmlspecialchars($product['price']) ?>" step="0.01" required>


        <label for="photo">Product Image (Leave blank to keep the current image)</label>
        <input type="file" name="photo" id="photo">

        <button type="submit">Update Product</button>
    </form>

  
    <?php if ($product['image_url']): ?>
        <div>
            <h3>Current Product Image:</h3>
            <img src="../<?= htmlspecialchars($product['image_url']) ?>" alt="Current Product Image" style="max-width: 300px;">
        </div>
    <?php endif; ?>
</div>

</body>
</html>
