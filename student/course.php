<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'student') {
    header("Location: ../index.php");
    exit();
}

include '../config.php';

$email = $_SESSION['user']['email'];

// Fetch student record by email
$stmt = $conn->prepare("SELECT name, email, course FROM student_records WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Courses</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand" href="dashboard.php">Student Dashboard</a>
        <a href="../logout.php" class="btn btn-danger">Logout</a>
    </div>
</nav>

<div class="container mt-4">
    <h3>My Courses</h3>
    <hr>

    <?php if ($student && !empty($student['course'])): ?>
        <div class="card shadow">
            <div class="card-body">
                <p><strong>Name:</strong> <?= htmlspecialchars($student['name']) ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($student['email']) ?></p>
                <p><strong>Assigned Course:</strong></p>
                <span class="badge bg-success fs-6">
                    <?= htmlspecialchars($student['course']) ?>
                </span>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-warning">
            No course assigned yet. Please contact your teacher.
        </div>
    <?php endif; ?>

    <a href="dashboard.php" class="btn btn-primary mt-3">Back to Dashboard</a>
</div>

</body>
</html>
