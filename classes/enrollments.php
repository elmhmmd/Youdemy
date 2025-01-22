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


    public function getEnrolledCoursesByStudentId($studentId) {
        try {

            $sql = "SELECT course_id FROM enrollments WHERE user_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$studentId]);
            $enrollments = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    
            if (empty($enrollments)) return [];
    
            
            $placeholders = implode(',', array_fill(0, count($enrollments), '?'));
            $sql = "SELECT 
                        c.course_id,
                        c.title,
                        c.description,
                        cat.category_name,
                        u.username as teacher_name
                    FROM courses c
                    JOIN categories cat ON c.category_id = cat.category_id
                    JOIN users u ON c.teacher_id = u.user_id
                    WHERE c.course_id IN ($placeholders)";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($enrollments);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        } catch (PDOException $e) {
            error_log("Error fetching enrolled courses: " . $e->getMessage());
            return [];
        }
    }

        public function getMostPopularCourses($limit = 3) {
            try {
                $sql = "SELECT c.course_id, c.title, COUNT(e.user_id) as enrollment_count
                        FROM enrollments e
                        JOIN courses c ON e.course_id = c.course_id
                        GROUP BY c.course_id
                        ORDER BY enrollment_count DESC
                        LIMIT ?";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([$limit]);
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                error_log("Error fetching popular courses: " . $e->getMessage());
                return [];
            }
        }

        public function getTotalStudentsForTeacher($teacherId) {
            $sql = "SELECT COUNT(DISTINCT e.user_id) 
                    FROM enrollments e
                    JOIN courses c ON e.course_id = c.course_id
                    WHERE c.teacher_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$teacherId]);
            return $stmt->fetchColumn();
        }
        
        public function getEnrollmentCount($courseId) {
            $sql = "SELECT COUNT(*) FROM enrollments WHERE course_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$courseId]);
            return $stmt->fetchColumn();
        }

}
?>