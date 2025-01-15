<?php

class User
{
    private $userId;
    private $username;
    private $email;
    private $password;
    private $roleId;
    private $db;

    public function __construct($userId = null, $username = null, $email = null, $password = null, $roleId = null)
    {
        $this->userId = $userId;
        $this->username = $username;
        $this->email = $email;
        $this->password = $password;
        $this->roleId = $roleId;
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

    public function signup($username, $email, $password)
    {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        try {
            $sql = "INSERT INTO users (username, email, password) VALUES (:username, :email, :password)";
            $stmt = $this->db->prepare($sql);
           $stmt->execute([
                'username' => $username,
                'email' => $email,
                'password' => $hashedPassword,
            ]);

            return true; // Signup successful
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
                   session_start();
                   $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['role_id'] = $user['role_id'];
                 return true;
             } else {
                return "Invalid email or password.";
             }
         } catch (PDOException $e) {
                return "Login failed: " . $e->getMessage();
         }
    }
}
?>