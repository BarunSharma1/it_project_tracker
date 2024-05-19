<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$status = $_GET['status'] ?? '';
$priority = $_GET['priority'] ?? '';
$deadline = $_GET['deadline'] ?? '';

$query = "SELECT * FROM tasks WHERE user_id = ?";
$params = [$user_id];

if ($status) {
    $query .= " AND status = ?";
    $params[] = $status;
}
if ($priority) {
    $query .= " AND priority = ?";
    $params[] = $priority;
}
if ($deadline) {
    $query .= " AND deadline <= ?";
    $params[] = $deadline;
}

$stmt = $conn->prepare($query);
$stmt->bind_param(str_repeat('s', count($params)), ...$params);
$stmt->execute();
$result = $stmt->get_result();
$tasks = [
    'Pending' => [],
    'In Progress' => [],
    'Completed' => [],
];
while ($task = $result->fetch_assoc()) {
    $tasks[$task['status']][] = $task;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>IT Project Tracker</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">IT Project Tracker</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </nav>
    <div class="container mt-4">
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></h2>

        <h3>Filters</h3>
        <form method="GET" action="index.php">
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="filterStatus">Status</label>
                    <select class="form-control" id="filterStatus" name="status">
                        <option value="">All</option>
                        <option value="Pending">Pending</option>
                        <option value="In Progress">In Progress</option>
                        <option value="Completed">Completed</option>
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label for="filterPriority">Priority</label>
                    <select class="form-control" id="filterPriority" name="priority">
                        <option value="">All</option>
                        <option value="Low">Low</option>
                        <option value="Medium">Medium</option>
                        <option value="High">High</option>
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label for="filterDeadline">Deadline</label>
                    <input type="date" class="form-control" id="filterDeadline" name="deadline">
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Filter</button>
        </form>

        <h3>Your Tasks</h3>
        <div class="row">
            <?php foreach ($tasks as $status => $statusTasks): ?>
                <div class="col-md-4">
                    <h4><?php echo htmlspecialchars($status); ?></h4>
                    <?php foreach ($statusTasks as $task): ?>
                        <div class="card mb-3">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($task['title']); ?></h5>
                                <p class="card-text"><?php echo htmlspecialchars($task['description']); ?></p>
                                <p class="card-text"><small class="text-muted">Priority: <?php echo htmlspecialchars($task['priority']); ?></small></p>
                                <p class="card-text"><small class="text-muted">Deadline: <?php echo htmlspecialchars($task['deadline']); ?></small></p>
                                <form method="POST" action="task.php" style="display:inline;">
                                    <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                                    <input type="hidden" name="action" value="delete">
                                    <button type="submit" class="btn btn-danger">Delete</button>
                                </form>
                                <button class="btn btn-primary" onclick="fillUpdateForm(<?php echo htmlspecialchars(json_encode($task)); ?>)">Edit</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <h3>Add Task</h3>
        <form method="POST" action="task.php">
            <input type="hidden" name="action" value="create">
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" class="form-control" id="title" name="title" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea class="form-control" id="description" name="description"></textarea>
            </div>
            <div class="form-group">
                <label for="priority">Priority</label>
                <select class="form-control" id="priority" name="priority">
                    <option value="Low">Low</option>
                    <option value="Medium">Medium</option>
                    <option value="High">High</option>
                </select>
            </div>
            <div class="form-group">
                <label for="deadline">Deadline</label>
                <input type="date" class="form-control" id="deadline" name="deadline">
            </div>
            <button type="submit" class="btn btn-success">Add Task</button>
        </form>

        <h3>Update Task</h3>
        <form method="POST" action="task.php" id="updateForm">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="task_id" id="updateTaskId">
            <div class="form-group">
                <label for="updateTitle">Title</label>
                <input type="text" class="form-control" id="updateTitle" name="title" required>
            </div>
            <div class="form-group">
                <label for="updateDescription">Description</label>
                <textarea class="form-control" id="updateDescription" name="description"></textarea>
            </div>
            <div class="form-group">
                <label for="updatePriority">Priority</label>
                <select class="form-control" id="updatePriority" name="priority">
                    <option value="Low">Low</option>
                    <option value="Medium">Medium</option>
                    <option value="High">High</option>
                </select>
            </div>
            <div class="form-group">
                <label for="updateDeadline">Deadline</label>
                <input type="date" class="form-control" id="updateDeadline" name="deadline">
            </div>
            <div class="form-group">
                <label for="updateStatus">Status</label>
                <select class="form-control" id="updateStatus" name="status">
                    <option value="Pending">Pending</option>
                    <option value="In Progress">In Progress</option>
                    <option value="Completed">Completed</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Update Task</button>
        </form>
    </div>

    <script>
        function fillUpdateForm(task) {
            document.getElementById('updateTaskId').value = task.id;
            document.getElementById('updateTitle').value = task.title;
            document.getElementById('updateDescription').value = task.description;
            document.getElementById('updatePriority').value = task.priority;
            document.getElementById('updateDeadline').value = task.deadline;
            document.getElementById('updateStatus').value = task.status;
        }
    </script>
</body>
</html>
