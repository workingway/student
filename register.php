<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include 'config.php';

$name = $email = $role = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Get form data
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];
    $role     = $_POST['role'];

    /* ======================
       VALIDATION
    ====================== */

    if ($name === '') {
        $errors[] = "Name is required.";
    } elseif (strlen($name) < 3) {
        $errors[] = "Name must be at least 3 characters.";
    }

    if ($email === '') {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if ($password === '') {
        $errors[] = "Password is required.";
    } elseif (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters.";
    }

    if (!in_array($role, ['student', 'teacher'])) {
        $errors[] = "Invalid role selected.";
    }

    /* ======================
       EMAIL DUPLICATE CHECK
    ====================== */
    if (empty($errors)) {
        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $errors[] = "Email already registered.";
        }
        $check->close();
    }

    /* ======================
       INSERT USER
    ====================== */
    if (empty($errors)) {

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $conn->prepare(
            "INSERT INTO users (name, email, password, role) 
             VALUES (?, ?, ?, ?)"
        );

        if (!$stmt) {
            die("Prepare error: " . $conn->error);
        }

        $stmt->bind_param(
            "ssss",
            $name,
            $email,
            $hashedPassword,
            $role
        );

        if ($stmt->execute()) {

            /* ======================
               INSERT STUDENT RECORD
            ====================== */
            if ($role === 'student') {

                $phone   = null;
                $address = null;
                $course  = null;

                $studentStmt = $conn->prepare(
                    "INSERT INTO student_records 
                     (name, email, phone, address, course) 
                     VALUES (?, ?, ?, ?, ?)"
                );

                if (!$studentStmt) {
                    die("Student prepare error: " . $conn->error);
                }

                $studentStmt->bind_param(
                    "sssss",
                    $name,
                    $email,
                    $phone,
                    $address,
                    $course
                );

                if (!$studentStmt->execute()) {
                    die("Student insert error: " . $studentStmt->error);
                }

                $studentStmt->close();
            }

            session_regenerate_id(true);
            $_SESSION['success'] = "Registration successful. Please login.";
            header("Location: index.php");
            exit;

        } else {
            $errors[] = "Registration failed. Try again.";
        }

        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register | Student Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card p-4 mx-auto shadow" style="max-width: 420px;">
        <h3 class="text-center mb-3">Register</h3>

        <!-- ERRORS -->
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" novalidate>

            <div class="mb-3">
                <label class="form-label">Name</label>
                <input type="text" name="name" class="form-control"
                       value="<?= htmlspecialchars($name) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control"
                       value="<?= htmlspecialchars($email) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Role</label>
                <select name="role" class="form-control" required>
                    <option value="">-- Select Role --</option>
                    <option value="student" <?= $role === 'student' ? 'selected' : '' ?>>Student</option>
                    <option value="teacher" <?= $role === 'teacher' ? 'selected' : '' ?>>Teacher</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary w-100">
                Register
            </button>

            <p class="text-center mt-3 mb-0">
                Already have an account? <a href="index.php">Login</a>
            </p>
        </form>
    </div>
</div>

</body>
</html>
