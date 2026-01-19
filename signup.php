<?php
// 1. Include the database connection
require 'db.php';

$message = "";

// 2. Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];

    // 3. Simple Validation
    if (!empty($name) && !empty($email) && !empty($password)) {

        // 4. Secure the data (Prevent SQL Injection)
        $name = $conn->real_escape_string($name);
        $email = $conn->real_escape_string($email);
        $phone = $conn->real_escape_string($phone);

        // 5. Encrypt the password (Security Best Practice)
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // 6. SQL Query to insert user
        $sql = "INSERT INTO users (name, email, phone, password, role) VALUES ('$name', '$email', '$phone', '$hashed_password', 'customer')";

        if ($conn->query($sql) === TRUE) {
            $message = "<div class='success-msg'>Account created successfully! <a href='login.php'>Login Now</a></div>";
        } else {
            $message = "<div class='error-msg'>Error: " . $conn->error . "</div>";
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