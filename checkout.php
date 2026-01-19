<?php
session_start();
require 'db.php';

// 1. Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$message = "";

// 2. Handle Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];

    // Combine City + Address for the database
    $full_address = $conn->real_escape_string($_POST['address'] . ", " . $_POST['city']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $payment = "Cash on Delivery"; // For now, since others are disabled

    // Get Cart Data from Hidden Input
    $cart_json = $_POST['cart_data'];
    $cart_items = json_decode($cart_json, true);

    // Calculate Total
    $total_amount = 0;
    if (is_array($cart_items)) {
        foreach ($cart_items as $item) {
            $total_amount += ($item['price'] * $item['quantity']);
        }
    }

    // Add Delivery Fee (Fixed 120 as per your design)
    $total_amount += 120;

    if ($total_amount > 120) {
        // A. Insert into ORDERS table
        $sql = "INSERT INTO orders (user_id, total_amount, shipping_address, phone, payment_method) 
                VALUES ('$user_id', '$total_amount', '$full_address', '$phone', '$payment')";

        if ($conn->query($sql) === TRUE) {
            $order_id = $conn->insert_id;

            // B. Insert into ORDER_ITEMS table
            foreach ($cart_items as $item) {
                $p_name = $conn->real_escape_string($item['name']);
                $p_price = $item['price'];
                $p_qty = $item['quantity'];
                $p_img = $conn->real_escape_string($item['image']);

                $sql_item = "INSERT INTO order_items (order_id, product_name, price, quantity, image) 
                             VALUES ('$order_id', '$p_name', '$p_price', '$p_qty', '$p_img')";
                $conn->query($sql_item);
                $p_id = $item['id'];
                $conn->query("UPDATE products SET stock = stock - $p_qty WHERE id = '$p_id'");
            }
            // Clear Cart (Client-Side)
            echo "<script>localStorage.removeItem('cart');</script>";
            // C. Redirect to Dashboard with Success Flag
            header("Location: dashboard.php?order_success=1");
            exit();
        } else {
            $message = "<div style='color:red; background:#ffd1d1; padding:10px; margin-bottom:15px; border-radius:5px;'>Error: " . $conn->error . "</div>";
        }
    } else {
        $message = "<div style='color:red; background:#ffd1d1; padding:10px; margin-bottom:15px; border-radius:5px;'>Your cart is empty.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - TechTown</title>
    <link rel="icon" href="assets/images/TechTown Logo1.png" type="image/png">
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body>

    <header>
        <nav class="navbar">
            <div class="logo">
                <a href="index.html">
                    <img src="assets/images/TechTown Logo1.png" alt="TechTown Logo">
                </a>
            </div>

            <div class="search-container">
                <form action="products.html" method="GET" style="display: flex; width: 100%; position: relative;">
                    <input type="text" name="search" class="search-bar" placeholder="Search devices..." autocomplete="off">
                    <button type="submit" style="background: none; border: none; position: absolute; right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #888;">
                        <i class="fas fa-search"></i>
                    </button>
                    <div class="search-suggestions"></div>
                </form>
            </div>

            <ul class="nav-links">
                <li><a href="index.html">Home</a></li>
                <li><a href="products.html">Shop</a></li>
            </ul>

            <div class="nav-icons">
                <a href="cart.html" class="fas fa-shopping-cart"><span id="cart-count" style="font-size: 14px; font-weight: bold;">(0)</span></a>
                <a href="dashboard.php" class="fas fa-user"></a>
            </div>
        </nav>
    </header>

    <div class="section-container">
        <h2 class="section-title">Checkout</h2>

        <?php echo $message; ?>

        <form id="checkoutForm" class="checkout-container" action="checkout.php" method="POST">

            <input type="hidden" name="cart_data" id="cartDataInput">

            <div class="shipping-details">
                <h3><i class="fas fa-map-marker-alt"></i> Shipping Address</h3>

                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="name" value="<?php echo $_SESSION['user_name']; ?>" readonly style="background-color:#f2f2f2; cursor:not-allowed;">
                </div>

                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="tel" name="phone" value="<?php echo $_SESSION['user_phone']; ?>" required>
                </div>

                <div class="form-group">
                    <label>City</label>
                    <select name="city" id="shipCity" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px;">
                        <option value="Dhaka">Dhaka</option>
                        <option value="Dhaka (Uttara Village)">Dhaka (Uttara Village)</option>
                        <option value="Gazipur">Gazipur</option>
                        <option value="Mymensingh">Mymensingh</option>
                        <option value="Savar">Savar</option>
                        <option value="Tangail">Tangail</option>
                        <option value="Netrokona">Netrokona</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Full Address</label>
                    <textarea name="address" id="shipAddress" rows="3" placeholder="House #, Road #, Area" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px;" required></textarea>
                </div>

                <div class="form-group" style="margin-top: 30px;">
                    <label>Payment Method</label>
                    <div class="payment-options">

                        <div class="payment-card active">
                            <input type="radio" name="payment" id="cod" value="Cash on Delivery" checked>
                            <label for="cod">
                                <img src="assets/images/cod.png" alt="Cash on Delivery">
                                <span>Cash on Delivery</span>
                            </label>
                        </div>

                        <div class="payment-card disabled">
                            <input type="radio" name="payment" disabled>
                            <label>
                                <img src="assets/images/bKash.png" alt="bKash">
                                <span>bKash</span>
                                <small class="coming-soon">Coming Soon</small>
                            </label>
                        </div>

                        <div class="payment-card disabled">
                            <input type="radio" name="payment" disabled>
                            <label>
                                <img src="assets/images/Nagad.png" alt="Nagad">
                                <span>Nagad</span>
                                <small class="coming-soon">Coming Soon</small>
                            </label>
                        </div>

                        <div class="payment-card disabled">
                            <input type="radio" name="payment" disabled>
                            <label>
                                <img src="assets/images/Rocket.png" alt="Rocket">
                                <span>Rocket</span>
                                <small class="coming-soon">Coming Soon</small>
                            </label>
                        </div>

                    </div>
                </div>
            </div>

            <div class="order-summary-box">
                <h3>Order Summary</h3>
                <div id="checkout-items">
                </div>

                <hr style="margin: 15px 0; border: 0; border-top: 1px solid #eee;">

                <div class="summary-item">
                    <span>Subtotal</span>
                    <span id="checkout-subtotal">৳ 0</span>
                </div>
                <div class="summary-item">
                    <span>Delivery Fee</span>
                    <span>৳ 120</span>
                </div>
                <div class="summary-item total" style="margin-top: 10px; font-size: 18px; font-weight: bold; color: var(--primary-orange);">
                    <span>Total to Pay</span>
                    <span id="checkout-total">৳ 0</span>
                </div>

                <button type="submit" class="auth-btn" style="margin-top: 20px;">Confirm Order</button>
            </div>

        </form>
    </div>

    <footer>
        <div class="footer-grid">
            <div class="footer-col">
                <h4>About Us</h4>
                <ul>
                    <li><a href="about.html">Why TechTown?</a></li>
                    <li><a href="privacy.html">Privacy Policy</a></li>
                    <li><a href="terms.html">Terms & Conditions</a></li>
                    <li><a href="refund.html">Refund Policy</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Customer Service</h4>
                <ul>
                    <li><a href="faq.html">FAQ</a></li>
                    <li><a href="contact.html">Contact Us</a></li>
                    <li><a href="stores.html">Store Locator</a></li>
                    <li><a href="tracking.html">Track Order</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Contact</h4>
                <ul>
                    <li><a href="mailto:info@techtown.com.bd">info@techtown.com.bd</a></li>
                    <li><a href="tel:+8801969067909">+880 1969 067 909</a></li>
                    <li>Mirpur, Dhaka-1216, Bangladesh</li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Follow Us</h4>
                <ul>
                    <li><a href="https://www.facebook.com/techtown">Facebook</a></li>
                    <li><a href="https://www.instagram.com/techtown">Instagram</a></li>
                    <li><a href="https://www.linkedin.com/company/techtown">LinkedIn</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2026 TechTown BD Ltd. No Rights Reserved.</p>
        </div>
    </footer>

    <script>
        // 1. Get Cart
        const cart = JSON.parse(localStorage.getItem('cart')) || [];

        // 2. Put Cart JSON into Hidden Input
        document.getElementById('cartDataInput').value = JSON.stringify(cart);

        // 3. Render Visual Summary
        const container = document.getElementById('checkout-items');
        const subtotalEl = document.getElementById('checkout-subtotal');
        const totalEl = document.getElementById('checkout-total');
        const deliveryFee = 120;

        if (cart.length === 0) {
            container.innerHTML = "<p>Your cart is empty.</p>";
            document.querySelector('.auth-btn').disabled = true;
            document.querySelector('.auth-btn').style.background = '#ccc';
        } else {
            let subtotal = 0;
            container.innerHTML = cart.map(item => {
                subtotal += item.price * item.quantity;
                return `
                    <div class="summary-item" style="display:flex; justify-content:space-between; margin-bottom:10px;">
                        <span>${item.name} <small>(x${item.quantity})</small></span>
                        <span>৳ ${(item.price * item.quantity).toLocaleString()}</span>
                    </div>
                `;
            }).join('');

            subtotalEl.innerText = "৳ " + subtotal.toLocaleString();
            totalEl.innerText = "৳ " + (subtotal + deliveryFee).toLocaleString();
        }
    </script>
</body>

</html>