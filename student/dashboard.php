<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'student') {
    header("Location: ../index.php");
    exit();
}
include '../config.php';
?>
<!DOCTYPE html>
<html>

<head>
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <style>
    .card-hover:hover {
        transform: scale(1.03);
        transition: 0.3s;
    }
    </style>
</head>

<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Student Dashboard</a>
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
                <div class="card text-white bg-info card-hover">
                    <div class="card-body">
                        <h5 class="card-title">My Profile</h5>
                        <p class="card-text">View and update your personal information</p>
                        <a href="../profile.php" class="btn btn-light">View Profile</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-success card-hover">
                    <div class="card-body">
                        <h5 class="card-title">My Courses</h5>
                        <p class="card-text">Check your enrolled courses or subjects</p>
                        <a href="course.php" class="btn btn-light">View Courses</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-warning card-hover">
                    <div class="card-body">
                        <h5 class="card-title">Notifications</h5>
                        <p class="card-text">Check system notifications and updates</p>
                        <a href="#" class="btn btn-light">View</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Optional info card -->
        <div class="card shadow-sm">
            <div class="card-body">
                <h5>Welcome to your dashboard!</h5>
                <p>Here you can view your profile, check courses, and see notifications.</p>
            </div>
        </div>

    </div>
</body>

</html>
