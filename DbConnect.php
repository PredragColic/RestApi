<?php

class DbConnect {

    private $server = 'localhost';
    private $dbname = 'jwtapi';
    private $user = 'root';
    private $pass = '';

    public function connect()
    {
        try {
            $mysql_str = 'mysql:host='.$this->server.';dbname='.$this->dbname;
            $conn = new PDO($mysql_str, $this->user, $this->pass);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return $conn;
        } catch(Exception $e) {
            echo 'Database Error: '.$e->getMessage();
        }    
    }
}

?>