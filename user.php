<?php

class User 
{
    private $id;
    private $name;
    private $email;
    private $password;
    private $active;
    private $createdOn;

    private $tableName = 'users';
    private $dbConn;

    public function setId($id){ $this->id = $id; }
    public function setName($name){ $this->name = $name; }
    public function setEmail($email){ $this->email = $email; }
    public function setPassword($password){ $this->password = $password; }
    public function setActive($active){ $this->active = $active; }

    public function getId(){ return $this->id; }
    public function getName(){ return $this->name; }
    public function getEmail(){ return $this->email; }
    public function getPassword(){ return $this->password; }
    public function getActive(){ return $this->actove; }

    public function __construct()
    {
        $db = new DbConnect();
        $this->dbConn = $db->connect();
    }

    public function register()
    {
        $sql = "INSERT INTO ".$this->tableName." (name,email,password) VALUES(:name,:email,:password)";
        $stmt = $this->dbConn->prepare($sql);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':password', $this->password);
        if($stmt->execute()){
            return true;
        } else {
            return false;
        }
    }




}

?>