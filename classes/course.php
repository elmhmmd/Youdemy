<?php

class course {
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
        try {
            $sql = "SELECT 
                course_id, 
                title, 
                description, 
                content_type, 
                content_url, 
                content_text, 
                teacher_id, 
                category_id
            FROM courses";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $coursesData = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $courses = [];
            foreach ($coursesData as $data) {
                $courses[] = new course(
                    $data['course_id'],
                    $data['title'],
                    $data['description'],
                    $data['content_type'],
                    $data['content_url'],
                    $data['content_text'],
                    $data['teacher_id'],
                    $data['category_id']
                );
            }
            return $courses;
        } catch (PDOException $e) {
            error_log("Error fetching courses: " . $e->getMessage());
            return [];
        }
    }

    public function getCoursesByTeacherId($teacherId) {
        try {
            $sql = "SELECT 
                course_id, 
                title, 
                description, 
                content_type, 
                content_url, 
                content_text, 
                teacher_id, 
                category_id
            FROM courses 
            WHERE teacher_id = :teacherId";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':teacherId', $teacherId, PDO::PARAM_INT);
            $stmt->execute();
            $coursesData = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $courses = [];
            foreach ($coursesData as $data) {
                $courses[] = new course(
                    $data['course_id'],
                    $data['title'],
                    $data['description'],
                    $data['content_type'],
                    $data['content_url'],
                    $data['content_text'],
                    $data['teacher_id'],
                    $data['category_id']
                );
            }
            return $courses;
        } catch (PDOException $e) {
            error_log("Error fetching courses by teacher: " . $e->getMessage());
            return [];
        }
    }

}