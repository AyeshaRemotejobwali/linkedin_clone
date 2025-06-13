<?php
session_start();
require 'db.php';
if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href='login.php';</script>";
    exit;
}
$user_id = $_SESSION['user_id'];
$users = $conn->query("SELECT id, full_name FROM users WHERE id != $user_id")->fetchAll();
$messages = $conn->prepare("SELECT m.*, u.full_name FROM messages m JOIN users u ON m.sender_id = u.id WHERE m.receiver_id = ? OR m.sender_id = ? ORDER BY m.created_at");
$messages->execute([$user_id, $user_id]);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $receiver_id = $_POST['receiver_id'];
    $content = $_POST['content'];
    $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, content) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $receiver_id, $content]);
    echo "<script>window.location.href='messages.php';</script>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - LinkedIn Clone</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: Arial, sans-serif; }
        body { background: #f0f2f5; }
        .container { max-width: 800px; margin: 0 auto; padding: 20px; }
        .header { background: #0073b1; color: white; padding: 10px 20px; text-align: center; }
        .message-form { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .message-form select, .message-form textarea { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ccc; border-radius: 4px; }
        .message-form button { background: #0073b1; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        .message-form button:hover { background: #005f91; }
        .message { background: white; padding: 15px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 10px; }
        @media (max-width: 768px) { .container { padding: 10px; } }
    </style>
</head>
<body>
    <div class="header">
        <h1>Messages</h1>
    </div>
    <div class="container">
        <div class="message-form">
            <form method="POST">
                <select name="receiver_id" required>
                    <option value="">Select Recipient</option>
                    <?php foreach ($users as $user): ?>
                        <option value="<?php echo $user['id']; ?>"><?php echo htmlspecialchars($user['full_name']); ?></option>
                    <?php endforeach; ?>
                </select>
                <textarea name="content" placeholder="Type your message..." required></textarea>
                <button type="submit">Send</button>
            </form>
        </div>
        <?php while ($message = $messages->fetch()): ?>
            <div class="message">
                <h4>From: <?php echo htmlspecialchars($message['full_name']); ?></h4>
                <p><?php echo htmlspecialchars($message['content']); ?></p>
                <small><?php echo $message['created_at']; ?></small>
            </div>
        <?php endwhile; ?>
    </div>
</body>
</html>
