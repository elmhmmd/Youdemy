<?php
require_once '../classes/database.php';
require_once '../classes/user.php';
require_once '../classes/teacher.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['teacher_id'])) {
    $teacherId = $_POST['teacher_id'];
    $teacher = new Teacher();

    if ($teacher->rejectEnseignant($teacherId)) {
        header("Location: ../admin_dashboard.php");
        exit();
    } else {
        echo "Error rejecting teacher.";
    }
} else {
    header("Location: ../admin_dashboard.php");
    exit();
}
?>