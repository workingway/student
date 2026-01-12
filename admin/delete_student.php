<?php
session_start();
include '../config.php';
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'teacher') {
    header("Location: ../index.php");
    exit();
}

$id = $_GET['id'];
$conn->query("DELETE FROM student_records WHERE id=$id");
header("Location: manage_students.php");
exit();
?>