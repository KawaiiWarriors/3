<?php
require_once 'includes/db.php';

header('Content-Type: application/json');

if (!isset($_GET['date'])) {
    echo json_encode([]);
    exit();
}

$date = $_GET['date'];
$stmt = $pdo->prepare("SELECT appointment_time FROM appointments WHERE appointment_date = ? AND status != 'canceled'");
$stmt->execute([$date]);
$times = $stmt->fetchAll(PDO::FETCH_COLUMN);

echo json_encode($times);
?>