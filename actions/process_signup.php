<?php
require_once '../classes/database.php';
require_once '../classes/user.php';
require_once '../classes/admin.php';
require_once '../classes/teacher.php';
require_once '../classes/student.php';

session_start();


if ($_SERVER["REQUEST_METHOD"] == "POST") {
   
    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $confirmPassword = $_POST["confirm_password"];
    $roleId = $_POST["role"];

    $username = htmlspecialchars(trim($username));
    $email = htmlspecialchars(trim($email));
    $roleId = intval($roleId);
    
    $errors = [];
     
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $errors[] = "Le nom d'utilisateur doit contenir uniquement des lettres et des chiffres.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Veuillez entrer une adresse e-mail valide.";
    }
    
      if (strlen($password) < 6) {
        $errors[] = "Le mot de passe doit contenir au moins 6 caractères.";
    }
     
    if($password !== $confirmPassword) {
       $errors[] = "Les mots de passe ne correspondent pas.";
    }

    if ($roleId !== 1 && $roleId !== 2 && $roleId !== 3) {
        $errors[] = "Le rôle sélectionné est invalide.";
    }
     
    if (!empty($errors)) {
        $_SESSION['form_errors'] = $errors;
      header('Location: ../pages/signup.php');
        exit;
    }

    if (!empty($errors)) {
        $_SESSION['form_errors'] = $errors;
        header('Location: ../pages/signup.php');
        exit;
    }

   
    switch ($roleId) {
        case 1:
            $user = new admin(null, $username, $email, $password); 
            break;
        case 2:
            $user = new teacher(null, $username, $email, $password);
            break;
        case 3:
            $user = new student(null, $username, $email, $password);
            break;
        default:
            $_SESSION['signup_error'] = "Rôle d'utilisateur invalide.";
            header('Location: ../pages/signup.php');
            exit;
    }

    
    $signupResult = $user->signup($username, $email, $password);
   

    if ($signupResult === true) {
        $_SESSION['signup_success'] = true; 
        header('Location: ../pages/login.php');
        exit;
    } else {
        $_SESSION['signup_error'] = $signupResult;
        header('Location: ../pages/signup.php');
        exit;
    }
}

else {
     header('Location: ../pages/signup.php');
        exit;
}
?>