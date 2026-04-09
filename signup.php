<?php
// 1. Include the database connection
require 'db.php';

$message = "";

// 2. Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // trim() removes accidental extra spaces
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];

    // 3. Simple Validation
    if (!empty($name) && !empty($email) && !empty($password)) {

        // 4. Encrypt the password (Your original code did this well)
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // 5. SECURE: Use Prepared Statements to prevent SQL Injection
        $stmt = $conn->prepare("INSERT INTO users (name, email, phone, password, role) VALUES (?, ?, ?, ?, 'customer')");
        // "ssss" means we are inserting 4 Strings
        $stmt->bind_param("ssss", $name, $email, $phone, $hashed_password);

        // Execute the statement safely
        if ($stmt->execute()) {
            $message = "<div class='success-msg'>Account created successfully! <a href='login.php'>Login Now</a></div>";
        } else {
            // If it fails, it is usually because the email is already taken
            $message = "<div class='error-msg'>Error: Could not create account. This email might already be registered.</div>";
        }
    } else {
        $message = "<div class='error-msg'>Please fill all fields.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - TechTown</title>
    <link rel="icon" href="assets/images/TechTown Logo1.png" type="image/png">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .success-msg {
            color: green;
            background: #d4edda;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }

        .error-msg {
            color: red;
            background: #f8d7da;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
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
        </nav>
    </header>

    <div class="auth-container">
        <div class="auth-box">
            <h2>Create an Account</h2>
            <p>Join TechTown today</p>

            <?php echo $message; ?>

            <form action="signup.php" method="POST">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="name" required placeholder="Ex: Sheikh Abdul Ahad">
                </div>

                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" required placeholder="name@example.com">
                </div>

                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="text" name="phone" placeholder="019xxxxxxxx">
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required placeholder="********">
                </div>

                <button type="submit" class="auth-btn">Sign Up</button>
            </form>

            <div class="auth-footer">
                Already have an account? <a href="login.php">Login here</a>
            </div>
        </div>
    </div>
</body>

</html>