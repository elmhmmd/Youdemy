<?php
class student extends user {
    public function __construct($userId = null, $username = null, $email = null, $password = null, $status = 'active') {
        parent::__construct($userId, $username, $email, $password, 3, $status);
    }
}