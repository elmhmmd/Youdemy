<?php
session_start();
require_once '../classes/database.php';
require_once '../classes/enrollments.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 3) {
    echo json_encode(['success' => false, 'message' => 'Accès non autorisé']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['course_id'])) {
    $userId = $_SESSION['user_id'];
    $courseId = $_POST['course_id'];
    
    $enrollment = new enrollments();
    
    try {
        if ($enrollment->enrollStudent($userId, $courseId)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Échec de l\'inscription']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Requête invalide']);
}
?>