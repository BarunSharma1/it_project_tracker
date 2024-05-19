<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $status = $_POST['status'];

    $sql = "INSERT INTO projects (name, description, start_date, end_date, status) VALUES ('$name', '$description', '$start_date', '$end_date', '$status')";
    if ($conn->query($sql) === TRUE) {
        header('Location: index.php');
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Project</title>
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
        <h1>Create Project</h1>
        <form method="POST">
            <label>Name</label>
            <input type="text" name="name" required>
            <label>Description</label>
            <textarea name="description" required></textarea>
            <label>Start Date</label>
            <input type="date" name="start_date" required>
            <label>End Date</label>
            <input type="date" name="end_date" required>
            <label>Status</label>
            <select name="status">
                <option value="Not Started">Not Started</option>
                <option value="In Progress">In Progress</option>
                <option value="Completed">Completed</option>
            </select>
            <input type="submit" value="Create Project">
        </form>
    </div>
</body>
</html>
