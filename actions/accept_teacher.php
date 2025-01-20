<?php
require_once '../classes/database.php';
require_once '../classes/user.php';
require_once '../classes/teacher.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['teacher_id'])) {
    $teacherId = $_POST['teacher_id'];
    $teacher = new teacher();

    if ($teacher->acceptEnseignant($teacherId)) {
        header("Location: ../pages/admin_dashboard.php");
        exit();
    } else {
        echo "Error accepting teacher.";
    }
} else {
    header("Location: ../pages/admin_dashboard.php");
    exit();
}
?>