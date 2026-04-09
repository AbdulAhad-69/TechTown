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

    // Combine City + Address for the database (Secured)
    $full_address = $_POST['address'] . ", " . $_POST['city'];
    $phone = $_POST['phone'];
    $payment = "Cash on Delivery"; // For now, since others are disabled

    // Get Cart Data from Hidden Input
    $cart_json = $_POST['cart_data'];
    $cart_items = json_decode($cart_json, true);

    if (is_array($cart_items) && count($cart_items) > 0) {
        $total_amount = 0;
        $delivery_fee = 120;
        $valid_order = true;
        $validated_items = [];

        // SECURITY CHECK: Verify real prices and check stock using Prepared Statements
        foreach ($cart_items as $item) {
            $p_id = (int)$item['id'];
            $p_qty = (int)$item['quantity'];

            // Fetch real data from DB, ignoring whatever the browser sent
            $stmt = $conn->prepare("SELECT name, price, stock, image FROM products WHERE id = ?");
            $stmt->bind_param("i", $p_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $real_product = $result->fetch_assoc();

            if ($real_product) {
                if ($real_product['stock'] < $p_qty) {
                    $valid_order = false;
                    $message = "<div style='color:red; background:#ffd1d1; padding:10px; margin-bottom:15px; border-radius:5px;'>Sorry, '{$real_product['name']}' does not have enough stock!</div>";
                    break; // Stop checking, order is invalid
                }
                
                // Calculate total using REAL database price
                $total_amount += ($real_product['price'] * $p_qty);
                
                // Store validated data for insertion
                $validated_items[] = [
                    'id' => $p_id,
                    'name' => $real_product['name'],
                    'price' => $real_product['price'],
                    'qty' => $p_qty,
                    'image' => $real_product['image']
                ];
            }
        }

        if ($valid_order) {
            $total_amount += $delivery_fee;

            // A. Insert into ORDERS table (Prepared Statement)
            $order_stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, shipping_address, phone, payment_method) VALUES (?, ?, ?, ?, ?)");
            $order_stmt->bind_param("idsss", $user_id, $total_amount, $full_address, $phone, $payment);
            
            if ($order_stmt->execute()) {
                $order_id = $order_stmt->insert_id;

                // B. Insert into ORDER_ITEMS and update stock (Prepared Statements)
                $item_stmt = $conn->prepare("INSERT INTO order_items (order_id, product_name, price, quantity, image) VALUES (?, ?, ?, ?, ?)");
                $stock_stmt = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");

                foreach ($validated_items as $v_item) {
                    // Insert items
                    $item_stmt->bind_param("isdis", $order_id, $v_item['name'], $v_item['price'], $v_item['qty'], $v_item['image']);
                    $item_stmt->execute();

                    // Deduct stock safely
                    $stock_stmt->bind_param("ii", $v_item['qty'], $v_item['id']);
                    $stock_stmt->execute();
                }

                // C. Clear Cart (Client-Side) and Redirect
                echo "<script>localStorage.removeItem('cart');</script>";
                header("Location: dashboard.php?order_success=1");
                exit();
            } else {
                $message = "<div style='color:red; background:#ffd1d1; padding:10px; margin-bottom:15px; border-radius:5px;'>Database Error.</div>";
            }
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
                <div id="checkout-items"></div>
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
        const cart = JSON.parse(localStorage.getItem('cart')) || [];
        document.getElementById('cartDataInput').value = JSON.stringify(cart);

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