<?php

class user
{
    protected $userId;
    protected $username;
    protected $email;
    protected $password;
    protected $roleId;
    protected $status;
    protected $db;

    public function __construct($userId = null, $username = null, $email = null, $password = null, $roleId = null, $status = null)
    {
        $this->userId = $userId;
        $this->username = $username;
        $this->email = $email;
        $this->password = $password;
        $this->roleId = $roleId;
        $this->status = $status;
        $this->db = Database::getInstance()->getConnection();
    }

    // Getters
    public function getUserId()
    {
        return $this->userId;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getRoleId()
    {
        return $this->roleId;
    }

    public function getStatus()
    {
        return $this->status;
    }


    // Setters
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function setRoleId($roleId)
    {
        $this->roleId = $roleId;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function signup($username, $email, $password, $roleId)
{
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    if ($roleId == 2) {
        $status = 'pending';
    } elseif ($roleId == 3) {
        $status = 'activated';
    } elseif($roleId == 1){
        $status = 'active';
    }

    try {
        $sql = "INSERT INTO users (username, email, password, role_id, status) VALUES (:username, :email, :password, :role_id, :status)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'username' => $username,
            'email' => $email,
            'password' => $hashedPassword,
            'role_id' => $roleId,
            'status' => $status
        ]);

        return true;

    } catch (PDOException $e) {
        return "Signup failed: " . $e->getMessage();
    }
}

        public function login($email, $password)
        {
            try {
                $sql = "SELECT * FROM users WHERE email = :email";
                $stmt = $this->db->prepare($sql);
                $stmt->execute(['email' => $email]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($user && password_verify($password, $user['password'])) {
                    return $user;
                } else {
                    return "Invalid email or password.";
                }
            } catch (PDOException $e) {
                    return "Login failed: " . $e->getMessage();
            }
        }

public function getAllUsersWithRoles() {
    try {
        $sql = "SELECT u.*, r.role_name 
                FROM users u
                JOIN roles r ON u.role_id = r.role_id
                ORDER BY u.user_id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching users: " . $e->getMessage());
        return [];
    }
}

public function toggleUserStatus($userId) {
    try {
        $sql = "UPDATE users SET status = 
                CASE 
                    WHEN status = 'activated' THEN 'suspended'
                    ELSE 'activated' 
                END
                WHERE user_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$userId]);
    } catch (PDOException $e) {
        error_log("Error toggling user status: " . $e->getMessage());
        return false;
    }
}

public function deleteUser($userId) {
    try {
        $sql = "DELETE FROM users WHERE user_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$userId]);
    } catch (PDOException $e) {
        error_log("Error deleting user: " . $e->getMessage());
        return false;
    }
}

public function getTopTeachers($limit = 3) {
    try {
        $sql = "SELECT u.user_id, u.username, COUNT(c.course_id) as course_count
                FROM users u
                JOIN courses c ON u.user_id = c.teacher_id
                WHERE u.role_id = 2
                GROUP BY u.user_id
                ORDER BY course_count DESC
                LIMIT ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching top teachers: " . $e->getMessage());
        return [];
    }
}

}
?>