<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$sql = "SELECT * FROM projects";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>IT Project Tracker</title>
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
        <h2>Projects</h2>
        <ul>
            <?php while($row = $result->fetch_assoc()): ?>
                <li>
                    <a href="view_project.php?id=<?= $row['id'] ?>"><?= $row['name'] ?></a> - <?= $row['status'] ?>
                </li>
            <?php endwhile; ?>
        </ul>
    </div>
</body>
</html>
