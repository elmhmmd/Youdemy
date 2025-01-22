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


public function searchCourses($searchTerm) {
    try {
        $searchTerm = "%$searchTerm%";
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
        WHERE title LIKE :searchTerm 
        OR description LIKE :searchTerm";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':searchTerm', $searchTerm, PDO::PARAM_STR);
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
        error_log("Error searching courses: " . $e->getMessage());
        return [];
    }
}

// Dans course.php
public function addCourse($title, $description, $contentType, $contentUrl, $contentText, $teacherId, $categoryId) {
    try {
        $sql = "INSERT INTO courses (title, description, content_type, content_url, content_text, teacher_id, category_id) 
                VALUES (:title, :description, :contentType, :contentUrl, :contentText, :teacherId, :categoryId)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'title' => $title,
            'description' => $description,
            'contentType' => $contentType,
            'contentUrl' => $contentUrl,
            'contentText' => $contentText,
            'teacherId' => $teacherId,
            'categoryId' => $categoryId
        ]);
        return $this->db->lastInsertId();
    } catch (PDOException $e) {
        error_log("Error adding course: " . $e->getMessage());
        return false;
    }
}

public function deleteCourse($courseId) {
    try {
        $sql = "DELETE FROM courses WHERE course_id = :courseId";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['courseId' => $courseId]);
    } catch (PDOException $e) {
        error_log("Error deleting course: " . $e->getMessage());
        return false;
    }
}

public function getCourseById($courseId) {
    try {
        $sql = "SELECT * FROM courses WHERE course_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$courseId]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$data) return null;

        return new course(
            $data['course_id'],
            $data['title'],
            $data['description'],
            $data['content_type'],
            $data['content_url'],
            $data['content_text'],
            $data['teacher_id'],
            $data['category_id']
        );
    } catch (PDOException $e) {
        error_log("Error fetching course: " . $e->getMessage());
        return null;
    }
}


public function getAllCourses() {
    try {
        $sql = "SELECT * FROM courses ORDER BY course_id DESC";
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

public function getTotalCourseCount() {
    try {
        $sql = "SELECT COUNT(*) FROM courses";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchColumn();
    } catch (PDOException $e) {
        error_log("Error counting courses: " . $e->getMessage());
        return 0;
    }
}

public function getCategoryDistribution() {
    try {
        $sql = "SELECT c.category_name, COUNT(*) as course_count
                FROM courses co
                JOIN categories c ON co.category_id = c.category_id
                GROUP BY co.category_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error getting category distribution: " . $e->getMessage());
        return [];
    }
}

public function getTotalVideosForTeacher($teacherId) {
    $sql = "SELECT COUNT(*) FROM courses 
            WHERE teacher_id = ? AND content_type = 'video'";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([$teacherId]);
    return $stmt->fetchColumn();
}

}