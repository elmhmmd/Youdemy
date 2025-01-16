<?php

class Role
{
    private int $roleId;
    private string $roleName;
    private $db;

    public function __construct($roleId = null, $roleName = null)
    {
        $this->roleId = $roleId;
        $this->roleName = $roleName;
        $this->db = Database::getInstance()->getConnection();
    }

    public function getRoleId()
    {
        return $this->roleId;
    }

    public function getRoleName()
    {
        return $this->roleName;
    }

    public function setRoleId()
    {
        $this->roleId= $roleId;
    }

    public function setRoleName()
    {
        $this->roleName = $roleName;
    }
}

?>