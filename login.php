<?php
// 1. Start Session & Connect to DB
session_start();
require 'db.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    // 2. Check if user exists
    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // 3. Verify Password
        if (password_verify($password, $user['password'])) {
            // Success! Create a "Session"
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_phone'] = $user['phone'];
            $_SESSION['user_role'] = $user['role'];

            // Redirect to Dashboard
            header("Location: dashboard.php");
            exit();
        } else {
            $message = "<div class='error-msg'>Incorrect password.</div>";
        }
    } else {
        $message = "<div class='error-msg'>User not found. <a href='signup.php'>Sign Up</a></div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - TechTown</title>
    <link rel="icon" href="assets/images/TechTown Logo1.png" type="image/png">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
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
                <li><a href="products.html">Buy</a></li>
            </ul>
        </nav>
    </header>

    <div class="auth-container">
        <div class="auth-box">
            <h2>Welcome Back</h2>
            <p>Login to continue</p>

            <?php echo $message; ?>

            <form action="login.php" method="POST">
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" required placeholder="name@example.com">
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required placeholder="********">
                </div>

                <button type="submit" class="auth-btn">Login</button>
            </form>

            <div class="auth-footer">
                Don't have an account? <a href="signup.php">Sign up</a>
            </div>
        </div>
    </div>
</body>

</html>