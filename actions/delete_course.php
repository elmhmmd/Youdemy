<?php
require_once '../classes/database.php';
require_once '../classes/course.php';

session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1 || !isset($_POST['course_id'])) {
    header('Location: ../pages/admin_dashboard.php');
    exit();
}

$courseId = (int)$_POST['course_id'];
$course = new course();

if ($course->deleteCourse($courseId)) {
    $_SESSION['admin_notice'] = "Cours supprimé avec succès";
} else {
    $_SESSION['admin_error'] = "Erreur lors de la suppression du cours";
}

header('Location: ../pages/admin_dashboard.php#gestion-cours');
exit();
?>
