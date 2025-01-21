<?php
require_once '../classes/database.php';
require_once '../classes/category.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['categoryName'])) {
    $categoryName = trim($_POST['categoryName']);

    if (!empty($categoryName)) {
        $category = new Category();
        $category->setCategoryName($categoryName);
        $createResult = $category->addCategory();

        if ($createResult === true) {
            header('Location: ../pages/admin_dashboard.php');
            exit();
        } else {
            echo "Error adding category: " . $createResult;
            exit();
        }
    } else {
        echo "Category name cannot be empty.";
        exit();
    }
} else {
    header('Location: ../pages/admin_dashboard.php');
    exit();
}
?>