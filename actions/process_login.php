<?php
require_once '../classes/database.php';
require_once '../classes/user.php';
require_once '../classes/admin.php';   
require_once '../classes/teacher.php';  
require_once '../classes/student.php'; 

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    $email = htmlspecialchars(trim($email));


    if (empty($email) || empty($password)) {
        $_SESSION['login_error'] = "Veuillez saisir votre adresse e-mail et votre mot de passe.";
        header('Location: ../pages/login.php');
        exit();
    }

    $user = new User();
    $loggedInUser = $user->login($email, $password);

    if ($loggedInUser) {

        $_SESSION['user_id'] = $loggedInUser['user_id'];
        $_SESSION['username'] = $loggedInUser['username'];
        $_SESSION['role_id'] = $loggedInUser['role_id'];

        
        switch ($loggedInUser['role_id']) {
            case 1:
                $user = new Admin(
                    $loggedInUser['user_id'],
                    $loggedInUser['username'],
                    $loggedInUser['email'],
                    null,
                    $loggedInUser['status']
                );
                
                header('Location: ../pages/admin_dashboard.php');
                exit();
            case 2:
                $user = new teacher(
                    $loggedInUser['user_id'],
                    $loggedInUser['username'],
                    $loggedInUser['email'],
                    null,
                    $loggedInUser['status']
                );
                $_SESSION['user'] = $loggedInUser;
                header('Location: ../pages/enseignant_dashboard.php');
                exit();
            case 3:
                $user = new student(
                    $loggedInUser['user_id'],
                    $loggedInUser['username'],
                    $loggedInUser['email'],
                    null,
                    $loggedInUser['status']
                );
                $_SESSION['user'] = $loggedInUser;
                header('Location: ../pages/etudiant_page.php');
                exit();
            default:
                $_SESSION['login_error'] = "Rôle d'utilisateur inconnu.";
                header('Location: ../pages/login.php');
                exit();
        }
    } else {

        $_SESSION['login_error'] = "Identifiants incorrects.";
        header('Location: ../pages/login.php');
        exit();
    }
} else {
    header('Location: ../pages/login.php');
    exit();
}
?>