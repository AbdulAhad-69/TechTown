<?php
session_start();
require 'db.php';

// Check Admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    // Safety fallback: Check DB role if session is missing
    if (isset($_SESSION['user_id'])) {
        $uid = $_SESSION['user_id'];
        $res = $conn->query("SELECT role FROM users WHERE id='$uid'");
        $u = $res->fetch_assoc();
        if ($u['role'] !== 'admin') {
            header("Location: dashboard.php");
            exit();
        }
    } else {
        header("Location: login.php");
        exit();
    }
}

$msg = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $category = $conn->real_escape_string($_POST['category']);
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $condition = $conn->real_escape_string($_POST['condition']);
    $desc = $conn->real_escape_string($_POST['desc']);

    // Image Upload Logic
    $target_dir = "assets/images/";
    $target_file = $target_dir . basename($_FILES["image"]["name"]);

    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        $sql = "INSERT INTO products (name, category, price, stock, condition_type, description, image) 
                VALUES ('$name', '$category', '$price', '$stock', '$condition', '$desc', '$target_file')";

        if ($conn->query($sql) === TRUE) {
            $msg = "<p style='color:green;'>Product Added Successfully!</p>";
        } else {
            $msg = "<p style='color:red;'>Database Error: " . $conn->error . "</p>";
        }
    } else {
        $msg = "<p style='color:red;'>Error uploading image.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Add Product - Admin</title>
    <link rel="icon" href="assets/images/TechTown Logo1.png" type="image/png">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .form-container {
            max-width: 600px;
            margin: 40px auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
        }

        input,
        select,
        textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        button {
            width: 100%;
            padding: 12px;
            background: var(--primary-orange);
            color: white;
            border: none;
            font-weight: bold;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <header>
        <nav class="navbar">
            <div class="logo"><a href="index.html">TechTown Admin</a></div>
            <div class="nav-icons"><a href="admin.php">Back to Dashboard</a></div>
        </nav>
    </header>

    <div class="form-container">
        <h2>Add New Product</h2>
        <?php echo $msg; ?>
        <form method="POST" enctype="multipart/form-data">
            <label>Product Name</label>
            <input type="text" name="name" required>

            <label>Category</label>
            <select name="category">
                <option value="Smartphones">Smartphones</option>
                <option value="Laptops">Laptops</option>
                <option value="Cameras">Cameras</option>
                <option value="Smart Watches">Smart Watches</option>
            </select>

            <label>Price (৳)</label>
            <input type="number" name="price" required>

            <label>Stock Quantity</label>
            <input type="number" name="stock" value="1" required>

            <label>Condition</label>
            <select name="condition">
                <option value="New">New</option>
                <option value="Used - Like New">Used - Like New</option>
                <option value="Used - Good">Used - Good</option>
            </select>

            <label>Description</label>
            <textarea name="desc" rows="4"></textarea>

            <label>Product Image</label>
            <input type="file" name="image" required>

            <button type="submit">Add Product</button>
        </form>
    </div>
</body>

</html>