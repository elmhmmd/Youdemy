<?php

class Course {
    private $courseId;
    private $title;
    private $description;
    private $contentType;
    private $contentUrl;
    private $contentText;
    private $teacherId;
    private $categoryId;
    private $db;

    public function __construct($courseId = null, $title = null, $description = null, $contentType = 'document', $contentUrl = null, $contentText = null, $teacherId = null, $categoryId = null) {
        $this->courseId = $courseId;
        $this->title = $title;
        $this->description = $description;
        $this->contentType = $contentType;
        $this->contentUrl = $contentUrl;
        $this->contentText = $contentText;
        $this->teacherId = $teacherId;
        $this->categoryId = $categoryId;
        $this->db = Database::getInstance()->getConnection();
    }

    public function getCourseId() {
        return $this->courseId;
    }

    public function getTitle() {
        return $this->title;
    }

    public function getDescription() {
        return $this->description;
    }

    public function getContentType() {
        return $this->contentType;
    }

    public function getContentUrl() {
        return $this->contentUrl;
    }

    public function getContentText() {
        return $this->contentText;
    }

    public function getTeacherId() {
        return $this->teacherId;
    }

    public function getCategoryId() {
        return $this->categoryId;
    }

    public function setCourseId($courseId) {
        $this->courseId = $courseId;
    }

    public function setTitle($title) {
        $this->title = $title;
    }

    public function setDescription($description) {
        $this->description = $description;
    }

    public function setContentType($contentType) {
        $this->contentType = $contentType;
    }

    public function setContentUrl($contentUrl) {
        $this->contentUrl = $contentUrl;
    }

    public function setContentText($contentText) {
        $this->contentText = $contentText;
    }

    public function setTeacherId($teacherId) {
        $this->teacherId = $teacherId;
    }

    public function setCategoryId($categoryId) {
        $this->categoryId = $categoryId;
    }

    public function getAllCourses() {
        $sql = "SELECT 
                    course_id AS courseId, 
                    title AS title, 
                    description AS description, 
                    content_type AS contentType, 
                    content_url AS contentUrl, 
                    content_text AS contentText, 
                    teacher_id AS teacherId, 
                    category_id AS categoryId
                FROM courses";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_CLASS, self::class);
    }

}