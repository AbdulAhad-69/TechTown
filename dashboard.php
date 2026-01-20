<?php
session_start();
require 'db.php';

// 1. Security Check
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = "";

// ---------------------------------------------------------
// NEW: HANDLE PROFILE UPDATES (Make Settings Work)
// ---------------------------------------------------------
// ... inside the POST check ...
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    $new_name = $conn->real_escape_string($_POST['name']);
    $new_phone = $conn->real_escape_string($_POST['phone']);
    $new_address = $conn->real_escape_string($_POST['address']); // <--- NEW LINE
    $new_pass = $_POST['password'];

    // Update query
    if (!empty($new_pass)) {
        $hashed_pass = password_hash($new_pass, PASSWORD_DEFAULT);
        // Added address to SQL
        $sql_update = "UPDATE users SET name='$new_name', phone='$new_phone', address='$new_address', password='$hashed_pass' WHERE id='$user_id'";
    } else {
        // Added address to SQL
        $sql_update = "UPDATE users SET name='$new_name', phone='$new_phone', address='$new_address' WHERE id='$user_id'";
    }

    if ($conn->query($sql_update) === TRUE) {
        $_SESSION['user_name'] = $new_name;
        $_SESSION['user_phone'] = $new_phone;
        $message = "<div class='alert success'>Profile updated successfully!</div>";
    } else {
        $message = "<div class='alert error'>Error updating profile: " . $conn->error . "</div>";
    }
}

// ---------------------------------------------------------
// FETCH USER DATA & ORDERS
// ---------------------------------------------------------

// Refresh User Details from DB
$sql_user = "SELECT * FROM users WHERE id='$user_id'";
$result_user = $conn->query($sql_user);
$user = $result_user->fetch_assoc();

// Fetch Orders
$sql_orders = "SELECT * FROM orders WHERE user_id='$user_id' ORDER BY created_at DESC";
$result_orders = $conn->query($sql_orders);

$orders = [];
if ($result_orders->num_rows > 0) {
    while ($order = $result_orders->fetch_assoc()) {
        $order_id = $order['id'];
        $sql_items = "SELECT * FROM order_items WHERE order_id='$order_id'";
        $result_items = $conn->query($sql_items);
        $items = [];
        while ($item = $result_items->fetch_assoc()) {
            $items[] = $item;
        }
        $order['items'] = $items;
        $orders[] = $order;
    }
}

// Calculate Total Spent
$total_spent = 0;
foreach ($orders as $o) {
    $total_spent += $o['total_amount'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Dashboard - TechTown</title>
    <link rel="icon" href="assets/images/TechTown Logo1.png" type="image/png">
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .dash-container {
            max-width: 1200px;
            margin: 40px auto;
            display: grid;
            grid-template-columns: 250px 1fr;
            gap: 30px;
            padding: 0 5%;
        }

        .dash-sidebar {
            background: white;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #eee;
            height: fit-content;
        }

        .dash-profile {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }

        .dash-profile i {
            font-size: 50px;
            color: #ddd;
            margin-bottom: 10px;
        }

        .dash-menu a {
            display: block;
            padding: 12px;
            color: #555;
            transition: 0.3s;
            border-radius: 5px;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .dash-menu a:hover,
        .dash-menu a.active {
            background: var(--primary-orange);
            color: white;
        }

        .dash-content {
            background: white;
            padding: 30px;
            border-radius: 8px;
            border: 1px solid #eee;
            min-height: 500px;
        }

        .dash-section {
            display: none;
        }

        .dash-section.active {
            display: block;
        }

        /* Stats & Orders */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #eee;
            text-align: center;
        }

        .stat-card h1 {
            font-size: 36px;
            color: var(--primary-orange);
            margin: 5px 0;
        }

        .order-card {
            border: 1px solid #eee;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            background: #fff;
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            border-bottom: 1px solid #f0f0f0;
            padding-bottom: 10px;
            margin-bottom: 15px;
            font-size: 14px;
            color: #555;
        }

        .order-item {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 10px;
        }

        .order-item img {
            width: 50px;
            height: 50px;
            object-fit: contain;
            border: 1px solid #eee;
            border-radius: 4px;
        }

        .status-badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }

        .Pending {
            background: #fff3cd;
            color: #856404;
        }

        .Delivered {
            background: #d4edda;
            color: #155724;
        }

        .Cancelled {
            background: #f8d7da;
            color: #721c24;
        }

        /* Settings Form Styles */
        .settings-form label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #555;
        }

        .settings-form input {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }

        .alert.success {
            background-color: #d4edda;
            color: #155724;
        }

        .alert.error {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>

<body>

    <header>
        <nav class="navbar">
            <div class="logo"><a href="index.html"><img src="assets/images/TechTown Logo1.png" alt="Logo"></a></div>
            <ul class="nav-links">
                <li><a href="index.html">Home</a></li>
                <li><a href="products.html">Shop</a></li>
            </ul>
            <div class="nav-icons">
                <a href="cart.html"><i class="fas fa-shopping-cart"><span id="cart-count"
                        style="font-size: 14px; font-weight: bold;">(0)</span></i></a>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </nav>
    </header>

    <div class="dash-container">
        <aside class="dash-sidebar">
            <div class="dash-profile">
                <i class="fas fa-user-circle"></i>
                <h3><?php echo htmlspecialchars($user['name']); ?></h3>
                <p><?php echo htmlspecialchars($user['email']); ?></p>
            </div>
            <ul class="dash-menu">
                <li><a href="#" onclick="switchTab('overview')" class="active"><i class="fas fa-chart-pie"></i> Overview</a></li>
                <li><a href="#" onclick="switchTab('orders')"><i class="fas fa-box"></i> My Orders</a></li>
                <li><a href="#" onclick="switchTab('settings')"><i class="fas fa-cog"></i> Settings</a></li>
                <li><a href="logout.php" style="color: #ff4d4d;"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </aside>

        <main class="dash-content">

            <?php echo $message; ?>

            <div id="overview" class="dash-section active">
                <h2>Overview</h2>
                <div class="stats-grid">
                    <div class="stat-card">
                        <h1><?php echo count($orders); ?></h1>
                        <p>Total Orders</p>
                    </div>
                    <div class="stat-card">
                        <h1 style="color: #333;">৳ <?php echo number_format($total_spent); ?></h1>
                        <p>Total Spent</p>
                    </div>
                </div>

                <h3>Recent Activity</h3>
                <?php if (count($orders) > 0): ?>
                    <p>Your last order was on <b><?php echo date("d M Y", strtotime($orders[0]['created_at'])); ?></b> for <b>৳ <?php echo number_format($orders[0]['total_amount']); ?></b>.</p>
                <?php else: ?>
                    <p style="color:#777;">No orders found.</p>
                <?php endif; ?>
            </div>

            <div id="orders" class="dash-section">
                <h2>Order History</h2>
                <?php if (count($orders) == 0): ?>
                    <p style="text-align:center; padding:30px; color:#888;">You haven't placed any orders yet.</p>
                <?php else: ?>
                    <?php foreach ($orders as $order): ?>
                        <div class="order-card">
                            <div class="order-header">
                                <div>
                                    <h3 style="display: flex; align-items: center; gap: 10px;">
                                        Order #<?php echo $order['id']; ?>

                                        <a href="order_details.php?id=<?php echo $order['id']; ?>"
                                            style="font-size: 12px; background: #eee; padding: 5px 10px; border-radius: 20px; color: #333; text-decoration: none; border: 1px solid #ddd;">
                                            <i class="fas fa-eye"></i> View Receipt
                                        </a>
                                    </h3>

                                    <span style="font-size: 13px; color: #777;">
                                        Placed on <?php echo date("d M Y", strtotime($order['created_at'])); ?>
                                    </span>
                                </div>
                                <div style="text-align:right;">
                                    <span class="status-badge <?php echo $order['status']; ?>"><?php echo $order['status']; ?></span> <br>
                                    <strong style="color:var(--primary-orange);">৳ <?php echo number_format($order['total_amount']); ?></strong>

                                    <?php if ($order['status'] == 'Pending'): ?>
                                        <br>
                                        <a href="order_cancel.php?id=<?php echo $order['id']; ?>"
                                            onclick="return confirm('Are you sure you want to cancel this order?');"
                                            style="color: red; font-size: 12px; text-decoration: underline; margin-top: 5px; display: inline-block;">
                                            Cancel Order
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="order-body">
                                <?php foreach ($order['items'] as $item): ?>
                                    <div class="order-item">
                                        <img src="<?php echo $item['image']; ?>" alt="Product">
                                        <div>
                                            <h4><?php echo htmlspecialchars($item['product_name']); ?></h4>
                                            <p>Qty: <?php echo $item['quantity']; ?> | Price: ৳ <?php echo number_format($item['price']); ?></p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div id="settings" class="dash-section">
                <h2>Account Settings</h2>
                <form method="POST" action="dashboard.php" class="settings-form">
                    <input type="hidden" name="update_profile" value="1">

                    <label>Full Name</label>
                    <input type="text" name="name" required value="<?php echo htmlspecialchars($user['name']); ?>">

                    <label>Phone Number</label>
                    <input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>">

                    <label>Shipping Address</label>
                    <textarea name="address" style="width:100%; padding:10px; margin-bottom:20px; border:1px solid #ddd; border-radius:5px;" rows="3"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>

                    <label>Email Address</label>
                    <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled style="background:#f9f9f9; color:#888;">

                    <label>New Password <span style="font-weight:normal; color:#888;">(Leave blank to keep current)</span></label>
                    <input type="password" name="password" placeholder="********">

                    <button type="submit" class="auth-btn">Save Changes</button>
                </form>
            </div>

        </main>
    </div>

    <script>
        // Check for success message in URL (from checkout)
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('order_success')) {
            localStorage.removeItem('cart');
            alert("Order placed successfully!");
            window.history.replaceState({}, document.title, "dashboard.php");
        }

        function switchTab(tabId) {
            document.querySelectorAll('.dash-section').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('.dash-menu a').forEach(el => el.classList.remove('active'));

            document.getElementById(tabId).classList.add('active');

            const links = document.querySelectorAll('.dash-menu a');
            links.forEach(link => {
                if (link.getAttribute('onclick').includes(tabId)) {
                    link.classList.add('active');
                }
            });
        }
        const dbUser = {
            name: "<?php echo htmlspecialchars($_SESSION['user_name']); ?>",
            email: "<?php echo htmlspecialchars($_SESSION['user_email']); ?>",
            phone: "<?php echo isset($_SESSION['user_phone']) ? htmlspecialchars($_SESSION['user_phone']) : ''; ?>"
        };
        localStorage.setItem('currentUser', JSON.stringify(dbUser));
    </script>

    <script src="assets/js/products.js"></script>
</body>

</html>