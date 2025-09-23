<?php
require '../config.php';


if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: ../login.php");
    exit();
}

$message = "";


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $price = trim($_POST['price']);
    $category = trim($_POST['category']);
    $image = trim($_POST['image_url']);

    if (!filter_var($image, FILTER_VALIDATE_URL)) {
        $message = "<p class='error'>Invalid image URL!</p>";
    } else {
        try {
            if (isset($_POST['edit_id'])) {
                $stmt = $pdo->prepare("UPDATE products SET name=?, price=?, category_id=?, image_url=? WHERE id=?");
                $stmt->execute([$name, $price, $category, $image, $_POST['edit_id']]);
                $message = "<p class='success'>Product updated successfully!</p>";
            } else {
                $stmt = $pdo->prepare("INSERT INTO products (name, price, category_id, image_url) VALUES (?,?,?,?)");
                $stmt->execute([$name, $price, $category, $image]);
                $message = "<p class='success'>Product added successfully!</p>";
            }
        } catch (PDOException $e) {
            $message = "<p class='error'>Error: " . $e->getMessage() . "</p>";
        }
    }
}


if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM products WHERE id=?");
    $stmt->execute([$_GET['delete']]);
    header("Location: admin_panel.php?deleted=true");
    exit();
}


$search = $_GET['search'] ?? '';
$query = "SELECT p.*, c.name AS category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.name LIKE ?";
$products = $pdo->prepare($query);
$products->execute(['%' . $search . '%']);
$products = $products->fetchAll();
$categories = $pdo->query("SELECT * FROM categories")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Panel</title>
    <link rel="stylesheet" href="../styles.css">
    <script>
        function confirmDelete(productId) {
            if (confirm("Are you sure you want to delete this product?")) {
                window.location.href = "?delete=" + productId;
            }
        }
    </script>
</head>
<body>
    <?php include '../header.php'; ?>

    <div class="admin-panel">
        <h2>📦 Product Management</h2>
        <?= $message; ?>

        <form method="GET">
            <input type="text" name="search" placeholder="Search products..." value="<?= htmlspecialchars($search) ?>">
            <button type="submit">🔍 Search</button>
        </form>

        <div class="product-form">
            <h3><?= isset($_GET['edit']) ? 'Edit' : 'Add' ?> Product</h3>
            <form method="POST">
                <?php if(isset($_GET['edit'])):
                    $editProduct = $pdo->prepare("SELECT * FROM products WHERE id=?");
                    $editProduct->execute([$_GET['edit']]);
                    $product = $editProduct->fetch();
                ?>
                    <input type="hidden" name="edit_id" value="<?= htmlspecialchars($product['id']) ?>">
                <?php endif; ?>
                
                <input type="text" name="name" placeholder="Product Name" required 
                       value="<?= htmlspecialchars($product['name'] ?? '') ?>">
                <input type="number" step="0.01" name="price" placeholder="Price" required
                       value="<?= htmlspecialchars($product['price'] ?? '') ?>">
                <select name="category" required>
                    <?php foreach($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= isset($product) && $product['category_id'] == $cat['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['name']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                <input type="url" name="image_url" placeholder="Image URL" required
                       value="<?= htmlspecialchars($product['image_url'] ?? '') ?>">
                <button type="submit">Save Product</button>
            </form>
        </div>

        <div class="product-list">
            <?php foreach($products as $product): ?>
            <div class="product-item">
                <img src="<?= htmlspecialchars($product['image_url']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" loading="lazy">
                <div>
                    <h3><?= htmlspecialchars($product['name']) ?></h3>
                    <p>₹<?= number_format($product['price'], 2) ?> | <?= htmlspecialchars($product['category_name']) ?></p>
                    <div class="actions">
                        <a href="?edit=<?= $product['id'] ?>">✏️ Edit</a>
                        <button onclick="confirmDelete(<?= $product['id'] ?>)">❌ Delete</button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>