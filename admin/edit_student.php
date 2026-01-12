<?php
session_start();
include '../config.php';

// Only allow teachers
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'teacher') {
    header("Location: ../index.php");
    exit();
}

// Get student ID
$id = intval($_GET['id']);
$errors = [];
$success = '';

// Fetch student
$result = $conn->query("SELECT * FROM student_records WHERE id=$id");
if ($result->num_rows == 0) {
    die("Student not found!");
}
$student = $result->fetch_assoc();

if (isset($_POST['update'])) {
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

    // Check email uniqueness excluding current student
    $check = $conn->prepare("SELECT * FROM student_records WHERE email=? AND id!=?");
    $check->bind_param("si", $email, $id);
    $check->execute();
    $res = $check->get_result();
    if ($res->num_rows > 0) $errors[] = "Email already exists.";

    // Update if no errors
    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE student_records SET name=?, email=?, phone=?, address=?, course=? WHERE id=?");
        $stmt->bind_param("sssssi", $name,$email,$phone,$address,$course,$id);
        if ($stmt->execute()) {
            $success = "Student updated successfully!";
            $student = ['name'=>$name,'email'=>$email,'phone'=>$phone,'address'=>$address,'course'=>$course];
        } else {
            $errors[] = "Failed to update student.";
        }
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Edit Student</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-4">
        <h3>Edit Student</h3>

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
                <input type="text" name="name" value="<?= htmlspecialchars($student['name']) ?>" class="form-control"
                    required minlength="3">
            </div>
            <div class="mb-3"><label>Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($student['email']) ?>" class="form-control"
                    required>
            </div>
            <div class="mb-3"><label>Phone</label>
                <input type="text" name="phone" value="<?= htmlspecialchars($student['phone']) ?>" class="form-control"
                    pattern="[0-9]{7,15}">
            </div>
            <div class="mb-3"><label>Address</label>
                <input type="text" name="address" value="<?= htmlspecialchars($student['address']) ?>"
                    class="form-control">
            </div>
            <div class="mb-3"><label>Course</label>
                <input type="text" name="course" value="<?= htmlspecialchars($student['course']) ?>"
                    class="form-control" maxlength="100">
            </div>
            <button type="submit" name="update" class="btn btn-primary">Update Student</button>
            <a href="manage_students.php" class="btn btn-secondary">Back</a>
        </form>
    </div>
</body>

</html>