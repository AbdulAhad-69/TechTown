<?php
session_start();
require 'db.php';

// 1. Security Check: Must be logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $order_id = intval($_GET['id']); // Clean the ID
    $user_id = $_SESSION['user_id'];

    // 2. Verify the order belongs to this user (Crucial Security Step!)
    $check_sql = "SELECT id FROM orders WHERE id = '$order_id' AND user_id = '$user_id'";
    $result = $conn->query($check_sql);

    if ($result->num_rows > 0) {
        // 3. Delete Items first (Foreign Key Logic)
        $conn->query("DELETE FROM order_items WHERE order_id = '$order_id'");
        
        // 4. Delete the Order
        $conn->query("DELETE FROM orders WHERE id = '$order_id'");
        
        // Success
        header("Location: dashboard.php?msg=Order Cancelled Successfully");
    } else {
        // Hacking attempt or wrong ID
        header("Location: dashboard.php?err=Order not found or access denied");
    }
} else {
    header("Location: dashboard.php");
}
?>