<?php
require_once '../classes/database.php';
require_once '../classes/user.php';

session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1 || !isset($_POST['user_id'])) {
    header('Location: ../pages/admin_dashboard.php');
    exit();
}

$userId = (int)$_POST['user_id'];
$user = new user();

if ($user->toggleUserStatus($userId)) {
    $_SESSION['admin_notice'] = "Statut utilisateur mis à jour";
} else {
    $_SESSION['admin_error'] = "Erreur lors de la mise à jour";
}

header('Location: ../pages/admin_dashboard.php#gestion-utilisateurs');
exit();
?>