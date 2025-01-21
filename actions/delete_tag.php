<?php
require_once '../classes/database.php';
require_once '../classes/tag.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tag_id'])) {
    $tagId = $_POST['tag_id'];

    $tag = new Tag($tagId);
    $result = $tag->deleteTag();

    if ($result === true) {
        header('Location: ../pages/admin_dashboard.php');
        exit();
    } else {
        echo "Erreur lors de la suppression du tag : " . $result;
    }
} else {
    header('Location: ../pages/admin_dashboard.php');
    exit();
}
?>