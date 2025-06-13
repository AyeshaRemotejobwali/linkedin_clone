<?php
session_start();
require 'db.php';
if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href='login.php';</script>";
    exit;
}
$post_id = $_GET['post_id'];
$stmt = $conn->prepare("SELECT p.*, u.full_name FROM posts p JOIN users u ON p.user_id = u.id WHERE p.id = ?");
$stmt->execute([$post_id]);
$post = $stmt->fetch();
$comments = $conn->prepare("SELECT c.*, u.full_name FROM comments c JOIN users u ON c.user_id = u.id WHERE c.post_id = ?");
$comments->execute([$post_id]);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $content = $_POST['content'];
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("INSERT INTO comments (post_id, user_id, content) VALUES (?, ?, ?)");
    $stmt->execute([$post_id, $user_id, $content]);
    echo "<script>window.location.href='comment.php?post_id=$post_id';</script>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comments - LinkedIn Clone</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: Arial, sans-serif; }
        body { background: #f0f2f5; }
        .container { max-width: 800px; margin: 0 auto; padding: 20px; }
        .header { background: #0073b1; color: white; padding: 10px 20px; text-align: center; }
        .post { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-top: 20px; }
        .comment { border-top: 1px solid #e0e0e0; padding: 10px 0; }
        .comment-form { margin-top: 20px; }
        .comment-form textarea { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; }
        .comment-form button { background: #0073b1; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        .comment-form button:hover { background: #005f91; }
        @media (max-width: 768px) { .container { padding: 10px; } }
    </style>
</head>
<body>
    <div class="header">
        <h1>Comments</h1>
    </div>
    <div class="container">
        <div class="post">
            <h4><?php echo htmlspecialchars($post['full_name']); ?></h4>
            <p><?php echo htmlspecialchars($post['content']); ?></p>
            <small><?php echo $post['created_at']; ?></small>
        </div>
        <div class="comment-form">
            <form method="POST">
                <textarea name="content" placeholder="Add a comment..." required></textarea>
                <button type="submit">Comment</button>
            </form>
        </div>
        <div class="comments">
            <?php while ($comment = $comments->fetch()): ?>
                <div class="comment">
                    <h5><?php echo htmlspecialchars($comment['full_name']); ?></h5>
                    <p><?php echo htmlspecialchars($comment['content']); ?></p>
                    <small><?php echo $comment['created_at']; ?></small>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>
