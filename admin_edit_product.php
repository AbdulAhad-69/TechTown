<?php
session_start();
require 'db.php';

// Security Check
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: admin_products.php");
    exit();
}
$id = intval($_GET['id']);

// Handle Update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $desc = $conn->real_escape_string($_POST['desc']);

    // Only update image if a new one is selected
    if (!empty($_FILES['image']['name'])) {
        $target = "assets/images/" . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $target);
        $sql = "UPDATE products SET name='$name', price='$price', stock='$stock', description='$desc', image='$target' WHERE id=$id";
    } else {
        $sql = "UPDATE products SET name='$name', price='$price', stock='$stock', description='$desc' WHERE id=$id";
    }

    if ($conn->query($sql)) {
        header("Location: admin_products.php");
        exit();
    }
}

// Fetch Current Data
$result = $conn->query("SELECT * FROM products WHERE id=$id");
$product = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Product</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .form-container {
            max-width: 600px;
            margin: 40px auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        input,
        textarea,
        select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .current-img {
            width: 100px;
            display: block;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            padding: 5px;
        }
    </style>
</head>

<body>
    <div class="form-container">
        <h2>Edit Product</h2>
        <form method="POST" enctype="multipart/form-data">
            <label>Product Name</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>

            <label>Price (৳)</label>
            <input type="number" name="price" value="<?php echo $product['price']; ?>" required>

            <label>Stock Quantity</label>
            <input type="number" name="stock" value="<?php echo $product['stock']; ?>" required>

            <label>Description</label>
            <textarea name="desc" rows="5"><?php echo htmlspecialchars($product['description']); ?></textarea>

            <label>Change Image (Optional)</label>
            <img src="<?php echo $product['image']; ?>" class="current-img">
            <input type="file" name="image">

            <button type="submit" class="auth-btn">Update Product</button>
            <a href="admin_products.php" style="display:block; text-align:center; margin-top:15px; color:#555;">Cancel</a>
        </form>
    </div>
</body>

</html>