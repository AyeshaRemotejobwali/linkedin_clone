<?php
session_start();
require 'db.php';
if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href='login.php';</script>";
    exit;
}
$job_id = $_GET['job_id'];
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("INSERT INTO job_applications (job_id, user_id) VALUES (?, ?)");
$stmt->execute([$job_id, $user_id]);
echo "<script>window.location.href='jobs.php';</script>";
?>
