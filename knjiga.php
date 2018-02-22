<?php 

class Knjiga
{
    private $id;
    private $naziv;
    private $autor;
    private $godina_izdavanja;
    private $jezik;
    private $originalni_jezik;

    private $tableName = 'knjige';
    private $dbConn;

    public function setId($id){ $this->id = $id; }
    public function setNaziv($naziv){ $this->naziv = $naziv; }
    public function setAutor($autor){ $this->autor = $autor; }
    public function setGodinaIzdavanja($godina_izdavanja){ $this->godina_izdavanja = $godina_izdavanja; }
    public function setJezik($jezik){ $this->jezik = $jezik; }
    public function setOriginalniJezik($originalni_jezik){ $this->originalni_jezik = $originalni_jezik; }
    public function getId(){ return $this->id; }
    public function getNaziv(){ return $this->naziv; }
    public function getAutor(){ return $this->autor; }
    public function getGodinaIzdavanja(){ return $this->godina_izdavanja; }
    public function getJezik(){ return $this->jezik; }
    public function getOriginalniJezik(){ return $this->originalni_jezik; }

    public function __construct()
    {
        $db = new DbConnect();
        $this->dbConn = $db->connect();
    }

    public function listaKnjiga()
    {
        
        $sql = "SELECT * FROM ". $this->tableName;
        $stmt = $this->dbConn->prepare($sql);
        $stmt->execute();
        $knjige = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $knjige;
    }

    public function listaKnjigaPagination($page=1, $per_page=10)
    {
        $offset = ($page - 1) * $per_page;
        $sql = "SELECT * FROM ".$this->tableName." LIMIT " . $offset . "," . $per_page;
        $stmt = $this->dbConn->prepare($sql);
        $stmt->execute();
        $knjige = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $knjige;
    }

    public function getKnjiga()
    {
        $sql = "SELECT * FROM ".$this->tableName." WHERE id = :id";
        $stmt = $this->dbConn->prepare($sql);
        $stmt->bindParam(':id',$this->id);
        if( $stmt->execute() ){
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            return false;
        }
    }

    public function insert()
    {
        $sql = "INSERT INTO ".$this->tableName." (naziv,autor,godina_izdavanja,jezik,originalni_jezik) VALUES(:naziv,:autor,:godina_izdavanja,:jezik,:originalni_jezik)";
        $stmt = $this->dbConn->prepare($sql);
        $stmt->bindParam(':naziv', $this->naziv);
        $stmt->bindParam(':autor', $this->autor);
        $stmt->bindParam(':godina_izdavanja', $this->godina_izdavanja);
        $stmt->bindParam(':jezik', $this->jezik);
        $stmt->bindParam(':originalni_jezik', $this->originalni_jezik);

        if($stmt->execute()){
            return true;
        } else {
            return false;
        }
    }

    public function update($data)
    {
        if( !empty($data['id']) ){
            //unset($data['id']);
            $sql = "UPDATE ".$this->tableName." SET ";
            $sqladd = '';
            foreach($data as $k=>$v){
                if($k !== 'id'){
                    $sqladd .=  $k. " = '".$v."',";
                }
            }
            $sqladd = rtrim($sqladd, ",");
            $sql .= $sqladd." WHERE id = :id";

            $stmt = $this->dbConn->prepare($sql);
            $stmt->bindParam(':id', $data['id']);
            if( $stmt->execute() ){
                return true;
            } else {
                return false;
            }

        } else {
            return false;
        }

    }

    public function delete()
    {
        $sql = "DELETE FROM ".$this->tableName." WHERE id = :id";
        $stmt = $this->dbConn->prepare($sql);
        $stmt->bindParam(':id',$this->id);
        if( $stmt->execute() ){
            return true;
        } else {
            return false;
        }
    }

    public function search($autor=null, $gi=null){
        $sql = "SELECT * FROM ".$this->tableName." WHERE 1=1 ";
        if(!empty($autor)){
            $sql .= "AND autor LIKE :autor ";
        }
        if(!empty($gi)){
            $sql .= "AND godina_izdavanja = :gi";
        }
        $stmt = $this->dbConn->prepare($sql);
        if(!is_null($autor)){
            $autor = '%'.$autor.'%';
            $stmt->bindParam(':autor', $autor, PDO::PARAM_STR);
        }
        if(!is_null($gi)){
            $stmt->bindParam(':gi', $gi);
        }
        
        if($stmt->execute()){
            return $stmt->fetchAll(PDO::FETCH_ASSOC);;
        } else {
            return false;
        }

    }

}