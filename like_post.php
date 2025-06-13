<?php
session_start();
require 'db.php';
if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href='login.php';</script>";
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_id = $_POST['post_id'];
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("INSERT INTO likes (post_id, user_id) VALUES (?, ?)");
    $stmt->execute([$post_id, $user_id]);
}
?>
