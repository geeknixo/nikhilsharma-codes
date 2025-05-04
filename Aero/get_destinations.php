<?php
include 'includes/db_connect.php';

$origin = $_GET['origin'] ?? '';

if (!$origin) {
    echo json_encode([]);
    exit;
}

$sql = "SELECT DISTINCT destination FROM flights WHERE origin = ? AND status = 'On Time' ORDER BY destination ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $origin);
$stmt->execute();
$result = $stmt->get_result();

$destinations = [];
while ($row = $result->fetch_assoc()) {
    $destinations[] = $row['destination'];
}

header('Content-Type: application/json');
echo json_encode($destinations);
?>
