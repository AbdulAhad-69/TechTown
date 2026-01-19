<?php
session_start();
require 'db.php';

// 1. Security Check
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// 2. Handle Delete Action
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM products WHERE id=$id");
    header("Location: admin_products.php");
    exit();
}

// 3. Fetch All Products
$result = $conn->query("SELECT * FROM products ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Manage Products - TechTown</title>
    
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="icon" href="assets/images/TechTownLogo1.png" type="image/png">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
        }

        .admin-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
        }

        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .product-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .product-table th,
        .product-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        .product-table th {
            background: #343a40;
            color: white;
        }

        .product-table img {
            width: 50px;
            height: 50px;
            object-fit: contain;
            border: 1px solid #eee;
            border-radius: 4px;
        }

        .btn-action {
            padding: 6px 12px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 12px;
            color: white;
            margin-right: 5px;
        }

        .btn-edit {
            background: #ffc107;
            color: #333;
            font-weight: bold;
        }

        .btn-delete {
            background: #dc3545;
        }

        .btn-add {
            background: var(--primary-orange);
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
        }
    </style>
</head>

<body>

    <header>
        <nav class="navbar">
            <div class="logo"><a href="index.html">TechTown Admin</a></div>
            <div class="nav-icons">
                <a href="admin.php">Orders</a>
                <a href="admin_products.php" style="color:var(--primary-orange);">Products</a>
                <a href="dashboard.php">Dashboard</a>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i></a>
            </div>
        </nav>
    </header>

    <div class="admin-container">
        <div class="admin-header">
            <h2>📦 Product Manager</h2>
            <a href="admin_add_product.php" class="btn-add">+ Add New Product</a>
        </div>

        <table class="product-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr style="<?php echo ($row['stock'] < 1) ? 'opacity:0.6; background:#fff5f5;' : ''; ?>">
                        <td><?php echo $row['id']; ?></td>
                        <td><img src="<?php echo $row['image']; ?>" alt="img"></td>
                        <td>
                            <strong><?php echo htmlspecialchars($row['name']); ?></strong>
                            <?php if ($row['stock'] < 1): ?>
                                <br><span style="color:red; font-size:11px; font-weight:bold;">OUT OF STOCK</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo $row['category']; ?></td>
                        <td>৳ <?php echo number_format($row['price']); ?></td>
                        <td><?php echo $row['stock']; ?></td>
                        <td>
                            <a href="admin_edit_product.php?id=<?php echo $row['id']; ?>" class="btn-action btn-edit"><i class="fas fa-edit"></i> Edit</a>
                            <a href="admin_products.php?delete=<?php echo $row['id']; ?>" class="btn-action btn-delete" onclick="return confirm('Are you sure? This cannot be undone.');"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

</body>

</html>