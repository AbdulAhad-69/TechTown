<?php
session_start();
require 'db.php';

// 1. Security: Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// 2. Security: Check if user is actually an ADMIN
$user_id = $_SESSION['user_id'];
$sql_user = "SELECT role FROM users WHERE id='$user_id'";
$result_user = $conn->query($sql_user);
$current_user = $result_user->fetch_assoc();

if ($current_user['role'] !== 'admin') {
    die("<h2 style='text-align:center; margin-top:50px; color:red;'>⛔ Access Denied! You are not an Admin.</h2>");
}

// 3. Handle Actions (Deliver / Cancel)
if (isset($_GET['action']) && isset($_GET['id'])) {
    $order_id = intval($_GET['id']);
    $action = $_GET['action'];

    if ($action == 'mark_delivered') {
        $conn->query("UPDATE orders SET status='Delivered' WHERE id='$order_id'");
    } elseif ($action == 'delete') {
        // Delete items first, then the order
        $conn->query("DELETE FROM order_items WHERE order_id='$order_id'");
        $conn->query("DELETE FROM orders WHERE id='$order_id'");
    }

    // Refresh page to show changes
    header("Location: admin.php");
    exit();
}

// 4. Fetch ALL Orders (Newest First)
$sql = "SELECT orders.*, users.name AS customer_name 
        FROM orders 
        JOIN users ON orders.user_id = users.id 
        ORDER BY created_at DESC";
$all_orders = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Panel - TechTown</title>
    <link rel="icon" href="assets/images/TechTown Logo1.png" type="image/png">
    <link rel="stylesheet" href="assets/css/style.css">
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

        .order-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .order-table th,
        .order-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        .order-table th {
            background: #343a40;
            color: white;
            font-weight: normal;
        }

        .order-table tr:hover {
            background: #f9f9f9;
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }

        .status-Pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-Delivered {
            background: #d4edda;
            color: #155724;
        }

        .btn-action {
            padding: 6px 12px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 12px;
            color: white;
            margin-right: 5px;
        }

        .btn-deliver {
            background: #28a745;
        }

        .btn-delete {
            background: #dc3545;
        }

        .btn-deliver:hover {
            background: #218838;
        }

        .btn-delete:hover {
            background: #c82333;
        }
    </style>
</head>

<body>

    <header>
        <nav class="navbar">
            <div class="logo">
                <a href="index.html">
                    <img src="assets/images/TechTown Logo1.png" alt="TechTown Logo">
                </a>
            </div>
            <div class="nav-icons">
                <a href="admin_products.php" style="color: var(--primary-orange); font-weight: bold; margin-right: 15px;">
                    <i class="fas fa-box"></i> Manage Products
                </a>
                <a href="dashboard.php">My Dashboard</a>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </nav>
    </header>

    <div class="admin-container">
        <div class="admin-header">
            <h2>🛍️ Shop Manager (Admin)</h2>
            <div style="background:white; padding:10px 20px; border-radius:50px;">
                Total Orders: <strong><?php echo $all_orders->num_rows; ?></strong>
            </div>
        </div>

        <table class="order-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($all_orders->num_rows > 0): ?>
                    <?php while ($order = $all_orders->fetch_assoc()): ?>
                        <tr>
                            <td>#<?php echo $order['id']; ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($order['customer_name']); ?></strong><br>
                                <small style="color:#777;"><?php echo htmlspecialchars($order['phone']); ?></small>
                            </td>
                            <td><?php echo date("d M Y", strtotime($order['created_at'])); ?></td>
                            <td>৳ <?php echo number_format($order['total_amount']); ?></td>
                            <td>
                                <span class="status-badge status-<?php echo $order['status']; ?>">
                                    <?php echo $order['status']; ?>
                                </span>
                            </td>
                            <td>
                                <a href="admin_order_details.php?id=<?php echo $order['id']; ?>" class="btn-action" style="background:#17a2b8;">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                <?php if ($order['status'] == 'Pending'): ?>
                                    <a href="admin.php?action=mark_delivered&id=<?php echo $order['id']; ?>" class="btn-action btn-deliver">
                                        <i class="fas fa-check"></i> Mark Delivered
                                    </a>
                                <?php else: ?>
                                    <span style="color:green; font-size:12px;">Completed</span>
                                <?php endif; ?>

                                <a href="admin.php?action=delete&id=<?php echo $order['id']; ?>" class="btn-action btn-delete" onclick="return confirm('Delete this order permanently?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align:center;">No orders found in the database.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</body>

</html>