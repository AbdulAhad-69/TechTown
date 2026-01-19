<?php
header('Content-Type: application/json');
require 'db.php';

$sql = "SELECT * FROM products ORDER BY id ASC";
$result = $conn->query($sql);

$products = [];
while($row = $result->fetch_assoc()) {
    // Convert specs from JSON string back to object if it exists
    if(!empty($row['specs'])) {
        $row['specs'] = json_decode($row['specs']);
    }
    $products[] = $row;
}

echo json_encode($products);
?>