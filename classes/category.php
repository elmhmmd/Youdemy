<?php

class category
{
    private $categoryId;
    private $categoryName;
    private $db;

    public function __construct($categoryId = null, $categoryName = null)
    {
        $this->categoryId = $categoryId;
        $this->categoryName = $categoryName;
        $this->db = Database::getInstance()->getConnection();
    }

    // Getters
    public function getCategoryId()
    {
        return $this->categoryId;
    }

    public function getCategoryName()
    {
        return $this->categoryName;
    }

    // Setters
    public function setCategoryId($categoryId)
    {
        $this->categoryId = $categoryId;
    }

    public function setCategoryName($categoryName)
    {
        $this->categoryName = $categoryName;
    }

    
    public function getCategoryNameById($categoryId) {
        try {
            $sql = "SELECT category_name FROM categories WHERE category_id = :category_id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['category_id' => $categoryId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? $result['category_name'] : 'Unknown Category';
        } catch (PDOException $e) {
            return 'Error fetching category';
        }
    }

    public function addCategory()
    {
        try {
            $sql = "INSERT INTO categories (category_name) VALUES (:category_name)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['category_name' => $this->categoryName]);
            $this->categoryId = $this->db->lastInsertId();
            return true;
        } catch (PDOException $e) {
            return "Category creation failed: " . $e->getMessage();
        }
    }

    public function deleteCategory()
    {
        try {
            $sql = "DELETE FROM categories WHERE category_id = :category_id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['category_id' => $this->categoryId]);
            return true;
        } catch (PDOException $e) {
            return "Category deletion failed: " . $e->getMessage();
        }
    }

    public function getAllCategories()
    {
        try {
            $sql = "SELECT * FROM categories";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $categoriesData = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $categories = [];
            foreach ($categoriesData as $categoryData) {
                $categories[] = new category($categoryData['category_id'], $categoryData['category_name']);
            }
            return $categories;
        } catch (PDOException $e) {
            return "Error fetching categories: " . $e->getMessage();
        }
    }

}
