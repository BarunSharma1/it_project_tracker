<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$project_id = $_GET['id'];

$sql = "SELECT * FROM projects WHERE id = $project_id";
$project = $conn->query($sql)->fetch_assoc();

$sql = "SELECT * FROM tasks WHERE project_id = $project_id";
$tasks = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Project</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <header>
        <div class="container">
            <div id="branding">
                <h1>IT Project Tracker</h1>
            </div>
            <nav>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="create_project.php">Create Project</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <div class="container">
        <h1><?= $project['name'] ?></h1>
        <p><?= $project['description'] ?></p>
        <p><strong>Start Date:</strong> <?= $project['start_date'] ?></p>
        <p><strong>End Date:</strong> <?= $project['end_date'] ?></p>
        <p><strong>Status:</strong> <?= $project['status'] ?></p>

        <h2>Tasks</h2>
        <ul>
            <?php while($row = $tasks->fetch_assoc()): ?>
                <li><?= $row['name'] ?> - <?= $row['status'] ?></li>
            <?php endwhile; ?>
        </ul>

        <a href="create_task.php?project_id=<?= $project_id ?>" class="button">Add Task</a>
    </div>
</body>
</html>
