<?php
include 'includes/db_connect.php';

header('Content-Type: application/json');

$origin = isset($_GET['origin']) ? trim($_GET['origin']) : '';
$destination = isset($_GET['destination']) ? trim($_GET['destination']) : '';

if (!$origin || !$destination) {
    echo json_encode(['price' => null, 'error' => 'Missing origin or destination']);
    exit;
}

// Use case-insensitive comparison for origin and destination
$sql = "SELECT price FROM flights WHERE LOWER(TRIM(origin)) = LOWER(?) AND LOWER(TRIM(destination)) = LOWER(?) AND status = 'On Time' LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $origin, $destination);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode(['price' => floatval($row['price'])]);
} else {
    echo json_encode(['price' => null, 'error' => 'No matching flight found']);
}
?>
