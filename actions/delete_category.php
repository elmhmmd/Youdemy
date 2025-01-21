<?php
require_once '../classes/database.php';
require_once '../classes/category.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['category_id'])) {
    $categoryId = $_POST['category_id'];

    $category = new Category();
    $category->setCategoryId($categoryId);
    $deleteResult = $category->deleteCategory();

    if ($deleteResult === true) {
        header('Location: ../pages/admin_dashboard.php');
        exit();
    } else {
        echo "Error deleting category.";
        exit();
    }
} else {
    header('Location: ../pages/admin_dashboard.php');
    exit();
}
?>