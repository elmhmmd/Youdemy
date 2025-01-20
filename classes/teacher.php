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
}
