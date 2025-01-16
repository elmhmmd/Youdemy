<?php
require_once '../classes/database.php';
require_once '../classes/user.php';

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
        // Store user information in the session
        $_SESSION['user_id'] = $loggedInUser['user_id'];
        $_SESSION['username'] = $loggedInUser['username'];
        $_SESSION['role_id'] = $loggedInUser['role_id'];

        // Redirect based on role
        switch ($loggedInUser['role_id']) {
            case 1: // Admin role ID
                header('Location: ../pages/admin/dashboard.php');
                exit();
            case 2: // Enseignant role ID
                header('Location: ../pages/enseignant/dashboard.php');
                exit();
            case 3: // Etudiant role ID
                header('Location: ../pages/etudiant/dashboard.php');
                exit();
            default:
                // Handle unexpected role ID (optional)
                $_SESSION['login_error'] = "Rôle d'utilisateur inconnu.";
                header('Location: ../pages/login.php');
                exit();
        }
    } else {
        // Login failed
        $_SESSION['login_error'] = "Identifiants incorrects.";
        header('Location: ../pages/login.php');
        exit();
    }
} else {
    // If accessed directly without submitting the form
    header('Location: ../pages/login.php');
    exit();
}
?>