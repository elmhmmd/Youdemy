<?php
require_once '../classes/database.php';
require_once '../classes/tag.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tagString'])) {
    $tagString = $_POST['tagString'];

    $tag = new Tag();
    $result = $tag->addMultipleTags($tagString);

    header('Location: ../pages/admin_dashboard.php');
    exit();
} else {

    header('Location: ../pages/admin_dashboard.php');
    exit();
}
?>