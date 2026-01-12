<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'teacher') {
    header("Location: ../index.php");
    exit();
}
include '../config.php';

// Count total students
$studentCount = $conn->query("SELECT COUNT(*) as total FROM student_records")->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html>

<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <style>
    .card-hover:hover {
        transform: scale(1.05);
        transition: 0.3s;
    }
    </style>
</head>

<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Teacher Dashboard</a>
            <div class="d-flex">
                <a href="../profile.php" class="btn btn-light me-2">Profile</a>
                <a href="../logout.php" class="btn btn-danger">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">

        <h3>Welcome, <?= $_SESSION['user']['name'] ?></h3>
        <hr>

        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card text-white bg-success card-hover">
                    <div class="card-body">
                        <h5 class="card-title">Total Students</h5>
                        <p class="card-text display-5"><?= $studentCount ?></p>
                        <a href="manage_students.php" class="btn btn-light">Manage Students</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-info card-hover">
                    <div class="card-body">
                        <h5 class="card-title">Add New Student</h5>
                        <p class="card-text">Quickly add a new student to the system</p>
                        <a href="add_student.php" class="btn btn-light">Add Student</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-warning card-hover">
                    <div class="card-body">
                        <h5 class="card-title">Profile</h5>
                        <p class="card-text">Update your profile and account settings</p>
                        <a href="../profile.php" class="btn btn-light">View Profile</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Students Table Preview -->
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                Latest Students
            </div>
            <div class="card-body">
                <?php
            $students = $conn->query("SELECT * FROM student_records ORDER BY id DESC LIMIT 5");
            if($students->num_rows > 0):
            ?>
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Course</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($s = $students->fetch_assoc()): ?>
                        <tr>
                            <td><?= $s['id'] ?></td>
                            <td><?= htmlspecialchars($s['name']) ?></td>
                            <td><?= htmlspecialchars($s['email']) ?></td>
                            <td><?= htmlspecialchars($s['course']) ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <a href="manage_students.php" class="btn btn-primary">View All Students</a>
                <?php else: ?>
                <p>No students found. <a href="add_student.php">Add a student</a></p>
                <?php endif; ?>
            </div>
        </div>

    </div>
</body>

</html>