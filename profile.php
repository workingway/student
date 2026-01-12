<?php
session_start();
include 'config.php';

// Redirect to login if not logged in
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

$user = $_SESSION['user'];

// Update profile if form submitted
if (isset($_POST['update'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];

    // Optional password update
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $stmt = $conn->prepare("UPDATE users SET name=?, email=?, password=? WHERE id=?");
        $stmt->bind_param("sssi", $name, $email, $password, $user['id']);
    } else {
        $stmt = $conn->prepare("UPDATE users SET name=?, email=? WHERE id=?");
        $stmt->bind_param("ssi", $name, $email, $user['id']);
    }

    if ($stmt->execute()) {
        // Update session data
        $_SESSION['user']['name'] = $name;
        $_SESSION['user']['email'] = $email;
        $success = "Profile updated successfully!";
    } else {
        $error = "Failed to update profile!";
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>My Profile | Student Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <div class="card shadow-lg mx-auto p-4" style="max-width: 500px;">
            <h3 class="text-center mb-4">My Profile</h3>

            <?php if(isset($success)): ?>
            <div class="alert alert-success"><?= $success ?></div>
            <?php elseif(isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" class="form-control"
                        required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>"
                        class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Role</label>
                    <input type="text" class="form-control" value="<?= ucfirst($user['role']) ?>" disabled>
                </div>
                <div class="mb-3">
                    <label class="form-label">Change Password (optional)</label>
                    <input type="password" name="password" class="form-control"
                        placeholder="Enter new password if you want to change it">
                </div>
                <div class="mb-3">
                    <label class="form-label">Member Since</label>
                    <input type="text" class="form-control"
                        value="<?= date('F j, Y', strtotime($user['created_at'])) ?>" disabled>
                </div>
                <button type="submit" name="update" class="btn btn-primary w-100">Update Profile</button>
            </form>

            <div class="text-center mt-3">
                <?php if ($user['role'] == 'teacher'): ?>
                <a href="admin/dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
                <?php else: ?>
                <a href="student/dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
                <?php endif; ?>
                <a href="logout.php" class="btn btn-danger ms-2">Logout</a>
            </div>
        </div>
    </div>
</body>

</html>