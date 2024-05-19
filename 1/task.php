<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'];
    $title = $_POST['title'] ?? null;
    $description = $_POST['description'] ?? null;
    $status = $_POST['status'] ?? null;
    $priority = $_POST['priority'] ?? null;
    $deadline = $_POST['deadline'] ?? null;
    $task_id = $_POST['task_id'] ?? null;

    if ($action == 'create' && $title) {
        $sql = "INSERT INTO tasks (user_id, title, description, priority, deadline) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issss", $user_id, $title, $description, $priority, $deadline);
        $stmt->execute();
    } elseif ($action == 'update' && $task_id && $title) {
        $sql = "UPDATE tasks SET title = ?, description = ?, status = ?, priority = ?, deadline = ? WHERE id = ? AND user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssii", $title, $description, $status, $priority, $deadline, $task_id, $user_id);
        $stmt->execute();
    } elseif ($action == 'delete' && $task_id) {
        $sql = "DELETE FROM tasks WHERE id = ? AND user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $task_id, $user_id);
        $stmt->execute();
    }
}

header("Location: index.php");
exit;
?>
