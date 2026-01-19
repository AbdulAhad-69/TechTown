<?php
session_start();
require 'db.php';

// 1. Security & Admin Check
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$user_id = $_SESSION['user_id'];
$check = $conn->query("SELECT role FROM users WHERE id='$user_id'");
$curr = $check->fetch_assoc();
if ($curr['role'] !== 'admin') {
    die("Access Denied");
}

// 2. Get Order ID
if (!isset($_GET['id'])) {
    die("Invalid Request");
}
$order_id = intval($_GET['id']);

// 3. Fetch Order Info
$sql_order = "SELECT orders.*, users.name, users.email 
              FROM orders 
              JOIN users ON orders.user_id = users.id 
              WHERE orders.id = '$order_id'";
$order_res = $conn->query($sql_order);
if ($order_res->num_rows == 0) {
    die("Order not found");
}
$order = $order_res->fetch_assoc();

// 4. Fetch Order Items
$sql_items = "SELECT * FROM order_items WHERE order_id = '$order_id'";
$items_res = $conn->query($sql_items);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Order #<?php echo $order_id; ?> - Admin</title>
    <link rel="icon" href="assets/images/TechTown Logo1.png" type="image/png">
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
        }

        /* Reusing Admin Layout */
        .admin-container {
            max-width: 1000px;
            margin: 40px auto;
            padding: 20px;
        }

        /* Back Button */
        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            color: #555;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .btn-back:hover {
            color: var(--primary-orange);
        }

        /* Detail Card */
        .order-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        /* Header Section */
        .card-header {
            padding: 25px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #fff;
        }

        .card-header h2 {
            margin: 0;
            font-size: 24px;
            color: #333;
        }

        /* Status Badges */
        .status-badge {
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 14px;
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

        /* Info Grid (Customer & Shipping) */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0;
            border-bottom: 1px solid #eee;
        }

        .info-col {
            padding: 25px;
        }

        .info-col:first-child {
            border-right: 1px solid #eee;
        }

        .info-col h3 {
            font-size: 16px;
            color: #888;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .info-col p {
            margin: 5px 0;
            color: #333;
            font-size: 15px;
        }

        /* Items Table */
        .items-section {
            padding: 25px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .items-table th {
            text-align: left;
            color: #888;
            font-weight: normal;
            padding-bottom: 10px;
            border-bottom: 2px solid #eee;
        }

        .items-table td {
            padding: 15px 0;
            border-bottom: 1px solid #eee;
            vertical-align: middle;
        }

        .items-table img {
            width: 60px;
            height: 60px;
            object-fit: contain;
            border: 1px solid #eee;
            border-radius: 5px;
            margin-right: 15px;
        }

        .product-name {
            font-weight: bold;
            color: #333;
            font-size: 15px;
        }

        /* Total Section */
        .total-section {
            display: flex;
            justify-content: flex-end;
            padding: 20px 25px;
            background: #fcfcfc;
        }

        .total-box {
            text-align: right;
            width: 300px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 15px;
            color: #555;
        }

        .grand-total {
            font-size: 20px;
            font-weight: bold;
            color: var(--primary-orange);
            border-top: 2px solid #eee;
            padding-top: 10px;
            margin-top: 10px;
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
                <a href="admin.php">Admin Panel</a>
                <a href="dashboard.php">My Dashboard</a>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </nav>
    </header>

    <div class="admin-container">

        <a href="admin.php" class="btn-back"><i class="fas fa-arrow-left"></i> Back to Orders</a>

        <div class="order-card">

            <div class="card-header">
                <h2>Order #<?php echo $order['id']; ?></h2>
                <span class="status-badge status-<?php echo $order['status']; ?>">
                    <?php echo $order['status']; ?>
                </span>
            </div>

            <div class="info-grid">
                <div class="info-col">
                    <h3>Customer Details</h3>
                    <p><i class="fas fa-user" style="width:20px; color:#ccc;"></i> <strong><?php echo htmlspecialchars($order['name']); ?></strong></p>
                    <p><i class="fas fa-envelope" style="width:20px; color:#ccc;"></i> <?php echo htmlspecialchars($order['email']); ?></p>
                    <p><i class="fas fa-phone" style="width:20px; color:#ccc;"></i> <?php echo htmlspecialchars($order['phone']); ?></p>
                </div>
                <div class="info-col">
                    <h3>Shipping Address</h3>
                    <p><i class="fas fa-map-marker-alt" style="width:20px; color:#ccc;"></i> <?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?></p>
                    <p><i class="fas fa-money-bill" style="width:20px; color:#ccc;"></i> <?php echo $order['payment_method']; ?></p>
                </div>
            </div>

            <div class="items-section">
                <h3>Items Ordered</h3>
                <table class="items-table">
                    <thead>
                        <tr>
                            <th width="50%">Product</th>
                            <th width="20%">Price</th>
                            <th width="10%">Qty</th>
                            <th width="20%" style="text-align:right;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $subtotal = 0;
                        while ($item = $items_res->fetch_assoc()):
                            $line_total = $item['price'] * $item['quantity'];
                            $subtotal += $line_total;
                        ?>
                            <tr>
                                <td style="display:flex; align-items:center;">
                                    <img src="<?php echo $item['image']; ?>" onerror="this.src='assets/images/placeholder.png'">
                                    <span class="product-name"><?php echo htmlspecialchars($item['product_name']); ?></span>
                                </td>
                                <td>৳ <?php echo number_format($item['price']); ?></td>
                                <td>x <?php echo $item['quantity']; ?></td>
                                <td style="text-align:right; font-weight:bold;">৳ <?php echo number_format($line_total); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <div class="total-section">
                <div class="total-box">
                    <div class="total-row">
                        <span>Subtotal:</span>
                        <span>৳ <?php echo number_format($subtotal); ?></span>
                    </div>
                    <div class="total-row">
                        <span>Delivery Fee:</span>
                        <span>৳ 120</span>
                    </div>
                    <div class="total-row grand-total">
                        <span>Total Paid:</span>
                        <span>৳ <?php echo number_format($order['total_amount']); ?></span>
                    </div>
                </div>
            </div>

        </div>
    </div>

</body>

</html>