<?php
session_start();

require_once '../classes/database.php';
require_once '../classes/course.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
    header('Location: ../login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $contentType = $_POST['content_type'];
    $categoryId = $_POST['category_id'];
    $teacherId = $_SESSION['user_id'];

    $contentUrl = null;
    $contentText = null;

    if ($contentType == 'video') {
        $contentUrl = $_POST['content_url'];
    } elseif ($contentType == 'document' && isset($_FILES['content_document']) && $_FILES['content_document']['error'] == 0) {

        $tmpFilePath = $_FILES['content_document']['tmp_name'];

        $contentText = file_get_contents($tmpFilePath); 
        
      
    } 
    
    else {    
            
        $_SESSION['error_message'] = 'Erreur lors de l\'upload du fichier.';
        header('Location: ../pages/enseignant_dashboard.php');
        exit();
    }

    $course = new Course();
    $success = $course->addCourse($title, $description, $contentType, $contentUrl, $contentText, $teacherId, $categoryId);

    if ($success) {
        $_SESSION['success_message'] = 'Cours ajouté avec succès.';
    } else {
        $_SESSION['error_message'] = 'Erreur lors de l\'ajout du cours.';
    }
  
    header('Location: ../pages/enseignant_dashboard.php');
    exit();
} 
else {
    header('Location: ../pages/enseignant_dashboard.php');
    exit();
}
?>