<?php
class enrollments {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }


    public function enrollStudent($userId, $courseId) {
        try {
            $sql = "INSERT INTO enrollments (user_id, course_id) VALUES (?, ?)";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$userId, $courseId]);
        } catch (PDOException $e) {
            error_log("Enrollment error: " . $e->getMessage());
            return false;
        }
    }

    public function getCourseEnrollments($courseId) {
        try {
            $sql = "SELECT u.user_id, u.username, u.email 
                    FROM enrollments e
                    JOIN users u ON e.user_id = u.user_id
                    WHERE e.course_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$courseId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Fetch enrollments error: " . $e->getMessage());
            return [];
        }
    }

    public function isEnrolled($userId, $courseId) {
        try {
            $sql = "SELECT * FROM enrollments 
                    WHERE user_id = ? AND course_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$userId, $courseId]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Enrollment check error: " . $e->getMessage());
            return false;
        }
    }
}
?>