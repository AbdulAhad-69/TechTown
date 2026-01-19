<?php
session_start();
session_unset();
session_destroy();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="3;url=login.php">
    <title>Logging Out - TechTown</title>
    <link rel="icon" href="assets/images/TechTown Logo1.png" type="image/png">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .logout-message {
            text-align: center;
            margin-top: 100px;
            padding: 20px;
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

    <div class="logout-message">
        <h2>You have been logged out.</h2>
        <p>Redirecting you to login...</p>

        <p><a href="login.php" style="color: var(--primary-orange); text-decoration: underline;">Click here</a> if you are not redirected automatically.</p>
    </div>
</body>

</html>