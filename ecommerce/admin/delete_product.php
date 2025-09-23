<?php
session_start();
require '../config.php';


if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: ../index.php");
    exit();
}


if (isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];


    $stmt = $pdo->prepare("SELECT image_url FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch();

    if ($product) {
   
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
        if ($stmt->execute([$product_id])) {
       
            $image_path = '../' . $product['image_url'];
            if (file_exists($image_path)) {
                unlink($image_path);
            }
            $_SESSION['message'] = "Product deleted successfully.";
        } else {
            $_SESSION['error'] = "Failed to delete product.";
        }
    } else {
        $_SESSION['error'] = "Product not found.";
    }
}

header("Location: manage_product.php");
exit();
