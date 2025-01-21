<?php
class teacher extends user {

    public function __construct($userId = null, $username = null, $email = null, $password = null, $status = 'pending') {
        parent::__construct($userId, $username, $email, $password, 2, $status);
    }

    public function getPendingTeachers() {
        $sql = "SELECT user_id, username, email FROM users WHERE role_id = 2 AND status = 'pending'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


    public function acceptEnseignant($teacherId) {
        $sql = "UPDATE users SET status = 'accepted' WHERE user_id = :teacher_id AND role_id = 2";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':teacher_id', $teacherId, PDO::PARAM_INT);
        return $stmt->execute();
}

    public function rejectEnseignant($teacherId) {
        $sql = "DELETE FROM users WHERE user_id = :teacher_id AND role_id = 2 AND status = 'pending'";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':teacher_id', $teacherId, PDO::PARAM_INT);
        return $stmt->execute();
}

    public function getCoursesByTeacherId($teacherId) {
        $sql = "SELECT
                course_id AS courseId,
                title AS title,
                description AS description,
                content_type AS contentType,
                content_url AS contentUrl,
                content_text AS contentText,
                teacher_id AS teacherId,
                category_id AS categoryId
            FROM courses
            WHERE teacher_id = :teacherId";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':teacherId', $teacherId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_CLASS, self::class);
            
    }

    public function getTeacherNameById($teacherId) {
        try {
        $sql = "SELECT username FROM users WHERE user_id = :teacherId";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':teacherId', $teacherId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['username'] ?? 'Unknown Instructor';
        } catch (PDOException $e) {
        echo "Error fetching teacher: " . $e->getMessage();
        return 'Unknown Instructor';
    }
}
}
