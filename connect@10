<?php
session_start();
require 'db.php';
if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href='login.php';</script>";
    exit;
}
$user_id = $_SESSION['user_id'];
$connected_user_id = $_GET['user_id'];
$stmt = $conn->prepare("INSERT INTO connections (user_id, connected_user_id) VALUES (?, ?)");
$stmt->execute([$user_id, $connected_user_id]);
echo "<script>window.location.href='index.php';</script>";
?>
