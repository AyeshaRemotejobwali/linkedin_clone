<?php
session_start();
require 'db.php';
if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href='login.php';</script>";
    exit;
}
$jobs = $conn->query("SELECT * FROM jobs ORDER BY created_at DESC")->fetchAll();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $company = $_POST['company'];
    $location = $_POST['location'];
    $description = $_POST['description'];
    $experience_level = $_POST['experience_level'];
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("INSERT INTO jobs (user_id, title, company, location, description, experience_level) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, $title, $company, $location, $description, $experience_level]);
    echo "<script>window.location.href='jobs.php';</script>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jobs - LinkedIn Clone</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: Arial, sans-serif; }
        body { background: #f0f2f5; }
        .container { max-width: 800px; margin: 0 auto; padding: 20px; }
        .header { background: #0073b1; color: white; padding: 10px 20px; text-align: center; }
        .job { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .job-form { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .job-form input, .job-form textarea, .job-form select { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ccc; border-radius: 4px; }
        .job-form button { background: #0073b1; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        .job-form button:hover { background: #005f91; }
        .job button { background: #0073b1; color: white; padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer; }
        .job button:hover { background: #005f91; }
        @media (max-width: 768px) { .container { padding: 10px; } }
    </style>
</head>
<body>
    <div class="header">
        <h1>Jobs</h1>
    </div>
    <div class="container">
        <div class="job-form">
            <h3>Post a Job</h3>
            <form method="POST">
                <input type="text" name="title" placeholder="Job Title" required>
                <input type="text" name="company" placeholder="Company" required>
                <input type="text" name="location" placeholder="Location">
                <textarea name="description" placeholder="Job Description"></textarea>
                <select name="experience_level" required>
                    <option value="entry">Entry Level</option>
                    <option value="mid">Mid Level</option>
                    <option value="senior">Senior Level</option>
                </select>
                <button type="submit">Post Job</button>
            </form>
        </div>
        <?php foreach ($jobs as $job): ?>
            <div class="job">
                <h4><?php echo htmlspecialchars($job['title']); ?></h4>
                <p><?php echo htmlspecialchars($job['company']); ?> - <?php echo htmlspecialchars($job['location']); ?></p>
                <p><?php echo htmlspecialchars($job['description']); ?></p>
                <p>Experience: <?php echo ucfirst($job['experience_level']); ?></p>
                <button onclick="window.location.href='apply_job.php?job_id=<?php echo $job['id']; ?>'">Apply</button>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
