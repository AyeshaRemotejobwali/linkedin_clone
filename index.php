<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href='login.php';</script>";
    exit;
}

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

$posts = $conn->query("SELECT p.*, u.full_name FROM posts p JOIN users u ON p.user_id = u.id ORDER BY p.created_at DESC")->fetchAll();
$jobs = $conn->query("SELECT * FROM jobs ORDER BY created_at DESC LIMIT 5")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LinkedIn Clone - Homepage</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: Arial, sans-serif; }
        body { background: #f0f2f5; }
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .header { background: #0073b1; color: white; padding: 10px 20px; display: flex; justify-content: space-between; align-items: center; }
        .header a { color: white; text-decoration: none; margin: 0 10px; }
        .header a:hover { text-decoration: underline; }
        .main { display: grid; grid-template-columns: 1fr 2fr 1fr; gap: 20px; margin-top: 20px; }
        .sidebar, .right-sidebar { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .content { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .post { border-bottom: 1px solid #e0e0e0; padding: 15px 0; }
        .post-header { display: flex; align-items: center; gap: 10px; }
        .post-actions button { background: none; border: none; color: #0073b1; cursor: pointer; margin-right: 10px; }
        .post-actions button:hover { text-decoration: underline; }
        .job { background: #f8f9fa; padding: 15px; margin-bottom: 10px; border-radius: 8px; }
        .create-post { margin-bottom: 20px; }
        .create-post textarea { width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #ccc; }
        .create-post button { background: #0073b1; color: white; padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer; }
        .create-post button:hover { background: #005f91; }
        @media (max-width: 768px) { .main { grid-template-columns: 1fr; } .sidebar, .right-sidebar { display: none; } }
    </style>
</head>
<body>
    <div class="header">
        <h1>LinkedIn Clone</h1>
        <div>
            <a href="profile.php">Profile</a>
            <a href="jobs.php">Jobs</a>
            <a href="messages.php">Messages</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>
    <div class="container">
        <div class="main">
            <div class="sidebar">
                <h3><?php echo htmlspecialchars($user['full_name']); ?></h3>
                <p><?php echo htmlspecialchars($user['job_title'] ?? ''); ?></p>
                <a href="profile.php">View Profile</a>
            </div>
            <div class="content">
                <div class="create-post">
                    <form method="POST" action="create_post.php">
                        <textarea name="content" placeholder="Share an update..." required></textarea>
                        <button type="submit">Post</button>
                    </form>
                </div>
                <?php foreach ($posts as $post): ?>
                    <div class="post">
                        <div class="post-header">
                            <h4><?php echo htmlspecialchars($post['full_name']); ?></h4>
                            <small><?php echo $post['created_at']; ?></small>
                        </div>
                        <p><?php echo htmlspecialchars($post['content']); ?></p>
                        <div class="post-actions">
                            <button onclick="likePost(<?php echo $post['id']; ?>)">Like</button>
                            <button onclick="window.location.href='comment.php?post_id=<?php echo $post['id']; ?>'">Comment</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="right-sidebar">
                <h3>Job Openings</h3>
                <?php foreach ($jobs as $job): ?>
                    <div class="job">
                        <h4><?php echo htmlspecialchars($job['title']); ?></h4>
                        <p><?php echo htmlspecialchars($job['company']); ?> - <?php echo htmlspecialchars($job['location']); ?></p>
                        <button onclick="window.location.href='apply_job.php?job_id=<?php echo $job['id']; ?>'">Apply</button>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <script>
        function likePost(postId) {
            fetch('like_post.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'post_id=' + postId
            }).then(() => location.reload());
        }
    </script>
</body>
</html>
