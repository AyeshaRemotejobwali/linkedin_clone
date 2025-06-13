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
$education = $conn->prepare("SELECT * FROM education WHERE user_id = ?");
$education->execute([$user_id]);
$experiences = $conn->prepare("SELECT * FROM experience WHERE user_id = ?");
$experiences->execute([$user_id]);
$skills = $conn->prepare("SELECT * FROM skills WHERE user_id = ?");
$skills->execute([$user_id]);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $job_title = $_POST['job_title'];
    $summary = $_POST['summary'];
    $stmt = $conn->prepare("UPDATE users SET job_title = ?, summary = ? WHERE id = ?");
    $stmt->execute([$job_title, $summary, $user_id]);
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $file_name = $user_id . '_' . basename($_FILES['profile_picture']['name']);
        $file_path = $upload_dir . $file_name;
        if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $file_path)) {
            $stmt = $conn->prepare("UPDATE users SET profile_picture = ? WHERE id = ?");
            $stmt->execute([$file_path, $user_id]);
        }
    }
    echo "<script>window.location.href='profile.php';</script>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - LinkedIn Clone</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: Arial, sans-serif; }
        body { background: #f0f2f5; }
        .container { max-width: 800px; margin: 0 auto; padding: 20px; }
        .header { background: #0073b1; color: white; padding: 10px 20px; text-align: center; }
        .profile-card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-top: 20px; }
        .profile-card img { max-width: 150px; border-radius: 50%; }
        .section { margin-top: 20px; }
        .section h3 { color: #0073b1; margin-bottom: 10px; }
        .section ul { list-style: none; }
        .section ul li { margin-bottom: 10px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; }
        .form-group input, .form-group textarea { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; }
        .form-group button { background: #0073b1; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        .form-group button:hover { background: #005f91; }
        @media (max-width: 768px) { .container { padding: 10px; } .profile-card img { max-width: 100px; } }
    </style>
</head>
<body>
    <div class="header">
        <h1>Profile</h1>
    </div>
    <div class="container">
        <div class="profile-card">
            <?php if ($user['profile_picture']): ?>
                <img src="<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile Picture">
            <?php endif; ?>
            <h2><?php echo htmlspecialchars($user['full_name']); ?></h2>
            <p><?php echo htmlspecialchars($user['job_title'] ?? ''); ?></p>
            <p><?php echo htmlspecialchars($user['summary'] ?? ''); ?></p>
            <div class="section">
                <h3>Education</h3>
                <ul>
                    <?php while ($edu = $education->fetch()): ?>
                        <li><?php echo htmlspecialchars($edu['degree'] . ' at ' . $edu['institution']); ?></li>
                    <?php endwhile; ?>
                </ul>
            </div>
            <div class="section">
                <h3>Experience</h3>
                <ul>
                    <?php while ($exp = $experiences->fetch()): ?>
                        <li><?php echo htmlspecialchars($exp['job_title'] . ' at ' . $exp['company']); ?></li>
                    <?php endwhile; ?>
                </ul>
            </div>
            <div class="section">
                <h3>Skills</h3>
                <ul>
                    <?php while ($skill = $skills->fetch()): ?>
                        <li><?php echo htmlspecialchars($skill['skill_name']); ?></li>
                    <?php endwhile; ?>
                </ul>
            </div>
            <div class="section">
                <h3>Update Profile</h3>
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label>Job Title</label>
                        <input type="text" name="job_title" value="<?php echo htmlspecialchars($user['job_title'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label>Summary</label>
                        <textarea name="summary"><?php echo htmlspecialchars($user['summary'] ?? ''); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>Profile Picture</label>
                        <input type="file" name="profile_picture" accept="image/*">
                    </div>
                    <button type="submit">Update</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
