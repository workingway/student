<?php
session_start();
include '../config.php';

// Only allow teachers
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'teacher') {
    header("Location: ../index.php");
    exit();
}

$errors = [];
$success = '';

if (isset($_POST['add'])) {
    // Trim inputs
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $course = trim($_POST['course']);

    // Validation
    if (empty($name) || strlen($name) < 3) $errors[] = "Name must be at least 3 characters.";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email is required.";
    if (!empty($phone) && !preg_match("/^[0-9]{7,15}$/", $phone)) $errors[] = "Phone must be numeric, 7-15 digits.";
    if (!empty($course) && strlen($course) > 100) $errors[] = "Course name is too long.";

    // Check unique email
    $check = $conn->prepare("SELECT * FROM student_records WHERE email=?");
    $check->bind_param("s", $email);
    $check->execute();
    $result = $check->get_result();
    if ($result->num_rows > 0) $errors[] = "Email already exists in records.";

    // Insert if no errors
    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO student_records (name,email,phone,address,course) VALUES (?,?,?,?,?)");
        $stmt->bind_param("sssss", $name,$email,$phone,$address,$course);
        if ($stmt->execute()) {
            $success = "Student added successfully!";
            // Clear form
            $name = $email = $phone = $address = $course = '';
        } else {
            $errors[] = "Failed to add student.";
        }
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Add Student</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-4">
        <h3>Add Student</h3>

        <?php if(!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul><?php foreach($errors as $err) echo "<li>$err</li>"; ?></ul>
        </div>
        <?php endif; ?>

        <?php if($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3"><label>Name</label>
                <input type="text" name="name" value="<?= isset($name)?htmlspecialchars($name):'' ?>"
                    class="form-control" required minlength="3">
            </div>
            <div class="mb-3"><label>Email</label>
                <input type="email" name="email" value="<?= isset($email)?htmlspecialchars($email):'' ?>"
                    class="form-control" required>
            </div>
            <div class="mb-3"><label>Phone</label>
                <input type="text" name="phone" value="<?= isset($phone)?htmlspecialchars($phone):'' ?>"
                    class="form-control" pattern="[0-9]{7,15}">
            </div>
            <div class="mb-3"><label>Address</label>
                <input type="text" name="address" value="<?= isset($address)?htmlspecialchars($address):'' ?>"
                    class="form-control">
            </div>
            <div class="mb-3"><label>Course</label>
                <input type="text" name="course" value="<?= isset($course)?htmlspecialchars($course):'' ?>"
                    class="form-control" maxlength="100">
            </div>
            <button type="submit" name="add" class="btn btn-primary">Add Student</button>
            <a href="manage_students.php" class="btn btn-secondary">Back</a>
        </form>
    </div>
</body>

</html>