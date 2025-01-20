<?php
class admin extends user {
    public function __construct($userId = null, $username = null, $email = null, $password = null, $status = 'active') {
        parent::__construct($userId, $username, $email, $password, 1, $status);
    }

}