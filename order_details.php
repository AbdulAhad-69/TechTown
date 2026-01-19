<?php
session_start();
require 'db.php';

// 1. Security: Must be logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// 2. Security: Fetch Order BUT ensure it belongs to this user!
$sql = "SELECT * FROM orders WHERE id = '$order_id' AND user_id = '$user_id'";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    die("<div style='text-align:center; padding:50px;'><h2>Order not found or access denied.</h2><a href='dashboard.php'>Go Back</a></div>");
}

$order = $result->fetch_assoc();

// 3. Fetch Items
$items_res = $conn->query("SELECT * FROM order_items WHERE order_id = '$order_id'");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Order #<?php echo $order_id; ?> Details</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .receipt-container {
            max-width: 800px;
            margin: 40px auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        .receipt-header {
            border-bottom: 2px solid #eee;
            padding-bottom: 20px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .receipt-header h2 {
            margin: 0;
            color: #333;
        }

        .receipt-date {
            color: #777;
            font-size: 14px;
        }

        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-Pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-Delivered {
            background: #d4edda;
            color: #155724;
        }

        .item-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid #eee;
            padding: 15px 0;
        }

        .item-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .item-info img {
            width: 60px;
            height: 60px;
            object-fit: contain;
            border: 1px solid #eee;
            border-radius: 5px;
        }

        .summary-box {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 5px;
            margin-top: 20px;
            text-align: right;
        }

        .summary-row {
            display: flex;
            justify-content: flex-end;
            gap: 40px;
            margin-bottom: 8px;
            font-size: 14px;
            color: #555;
        }

        .grand-total {
            font-size: 18px;
            font-weight: bold;
            color: var(--primary-orange);
            border-top: 1px solid #ddd;
            padding-top: 10px;
            margin-top: 10px;
        }

        .btn-back {
            text-decoration: none;
            color: #555;
            font-weight: bold;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            margin-bottom: 20px;
        }

        .btn-back:hover {
            color: var(--primary-orange);
        }
    </style>
</head>

<body>

    <header>
        <nav class="navbar">
            <div class="logo"><a href="index.html"><img src="assets/images/TechTown Logo1.png" alt="Logo"></a></div>
            <div class="nav-icons">
                <a href="dashboard.php">My Account</a>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i></a>
            </div>
        </nav>
    </header>

    <div class="receipt-container">
        <a href="dashboard.php" class="btn-back"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>

        <div class="receipt-header">
            <div>
                <h2>Order #<?php echo $order['id']; ?></h2>
                <span class="receipt-date">Placed on <?php echo date("d M Y, h:i A", strtotime($order['created_at'])); ?></span>
            </div>
            <span class="status-badge status-<?php echo $order['status']; ?>"><?php echo $order['status']; ?></span>
        </div>

        <div style="margin-bottom: 30px;">
            <h4 style="color:#555; margin-bottom:10px;">Shipping Address</h4>
            <div style="background:#f4f4f4; padding:15px; border-radius:5px; font-size:14px; line-height:1.6;">
                <?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?>
                <br>
                <strong>Payment:</strong> <?php echo $order['payment_method']; ?>
            </div>
        </div>

        <h4 style="color:#555; border-bottom:2px solid #eee; padding-bottom:10px;">Items Ordered</h4>

        <div>
            <?php while ($item = $items_res->fetch_assoc()): ?>
                <div class="item-row">
                    <div class="item-info">
                        <img src="<?php echo $item['image']; ?>" onerror="this.src='assets/images/placeholder.png'">
                        <div>
                            <strong><?php echo htmlspecialchars($item['product_name']); ?></strong><br>
                            <small>Qty: <?php echo $item['quantity']; ?> x ৳ <?php echo number_format($item['price']); ?></small>
                        </div>
                    </div>
                    <div style="font-weight:bold;">
                        ৳ <?php echo number_format($item['price'] * $item['quantity']); ?>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

        <div class="summary-box">
            <div class="summary-row">
                <span>Subtotal:</span>
                <span>৳ <?php echo number_format($order['total_amount'] - 120); ?></span>
            </div>
            <div class="summary-row">
                <span>Delivery Fee:</span>
                <span>৳ 120</span>
            </div>
            <div class="summary-row grand-total">
                <span>Total Paid:</span>
                <span>৳ <?php echo number_format($order['total_amount']); ?></span>
            </div>
        </div>

    </div>

</body>

</html>