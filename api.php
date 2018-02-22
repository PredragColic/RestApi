<?php

class Api extends Rest
{
    public $dbConn;
    public function __construct()
    {
        parent::__construct();
        $db = new DbConnect;
        $this->dbConn = $db->connect();
    }

    private function isAuthorized()
    {
        $token = $this->getBearerToken();
        $payload = JWT::decode($token, SECRET_KEY, ['HS256']);

        if( $payload && !empty($payload->userId) && $payload->authorized){
            return $payload;
        } else {
            return false;
        }

    }

    public function generateToken()
    {
        $email = $this->validateParameter('email', $this->param['email'], STRING);
        $pass = $this->validateParameter('pass', $this->param['pass'], STRING);
        

        try{
            $stmt = $this->dbConn->prepare("SELECT * FROM users WHERE email = :email AND password = :pass");
            $stmt->bindParam(":email", $email);
            $stmt->bindParam(":pass", $pass);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if( ! is_array($user) ){
                $this->returnResponse(INVALID_USER_PASS, 'Email or password is incorrect');
            }
            if( $user['active'] == 0 ){
                $this->throwError(USER_NOT_ACTIVE,'User is not activated. Please contact admin.');
            }

            $paylod = [
                'iat' => time(),
                'iss' => 'localhost',
                'authorized' => true,
                'exp' => time() + (60*60),
                'userId' => $user['id']
            ];

            $token = JWT::encode($paylod, SECRET_KEY);

            $data = ['token' => $token];

            $this->returnResponse(SUCCESS_RESPONSE, $data);

        }catch(Exception $e){
            $this->throwError(JWT_PROCESSING_ERROR, $e->getMessage());
        }
 
    }

    public function addKnjiga()
    {
        if( isset($this->param['naziv']) ){
            $naziv = $this->validateParameter('naziv', $this->param['naziv'], STRING);
        } else {
            $this->throwError(400,'Naziv knjige je obavezan parametar');
        }

        if( isset($this->param['autor']) ){
            $autor = $this->validateParameter('autor', $this->param['autor'], STRING);
        } else {
            $this->throwError(400,'Autor knjige je obavezan parametar');
        }
        
        if( isset($this->param['godina_izdavanja']) ){
            $godina_izdavanja = $this->validateParameter('godina_izdavanja', $this->param['godina_izdavanja'], INTEGER, false);
        } else {
            $godina_izdavanja = null;
        }

        if( isset($this->param['jezik']) ){
            $jezik = $this->validateParameter('jezik', $this->param['jezik'], STRING, false);
        } else {
            $jezik = null;
        }

        if( isset($this->param['originalni_jezik']) ){
            $originalni_jezik = $this->validateParameter('originalni_jezik', $this->param['originalni_jezik'], STRING, false);
        } else {
            $originalni_jezik = null;
        }
        
        try{
            if( $this->isAuthorized() ){
                $knjiga = new Knjiga;
                $knjiga->setNaziv($naziv);
                $knjiga->setAutor($autor);
                $knjiga->setGodinaIzdavanja($godina_izdavanja);
                $knjiga->setJezik($jezik);
                $knjiga->setOriginalniJezik($originalni_jezik);

                if( !$knjiga->insert() ){
                    $message = "Failed to insert."; 
                } else {
                    $message = "Inserted successfully.";
                }

                $this->returnResponse(SUCCESS_RESPONSE, $message);
            } else{
                $this->throwError(ACCESS_TOKEN_ERRORS, 'You are not authorized to insert.');
            } 
            

        } catch(Exception $e){
            $this->throwError(ACCESS_TOKEN_ERRORS, $e->getMessage());
        }
    }

    public function listaKnjiga()
    {
        //$pagination = $this->validateParameter('pagination', $this->param['pagination'], BOOLEAN, false);
        $per_page = $this->validateParameter('per_page', $this->param['per_page'], INTEGER, false);
        $page = $this->validateParameter('pagination', $this->param['page'], INTEGER, false);
        //print_r($this->param);

        $knjiga = new Knjiga;
        if(!isset($per_page)) {
            $lista = $knjiga->listaKnjiga();
        }else{
            $lista = $knjiga->listaKnjigaPagination($page,$per_page);
        }
        
        $this->returnResponse(200, $lista);
    }

    public function getKnjiga()
    {
        $id = $this->validateParameter('id', $this->param['id'], STRING, false);
        $knjiga = new Knjiga;
        $knjiga->setId($id);
        $res = $knjiga->getKnjiga();
        $this->returnResponse(200, $res);
    }

    public function updateKnjiga()
    {
        if( isset($this->param['id']) ){
            $id = $this->validateParameter('naziv', $this->param['id'], INTEGER);
        } else {
            $this->throwError(400,'ID knjige je obavezan parametar');
        }
        
        
        if( isset($this->param['naziv']) ){
            $naziv = $this->validateParameter('naziv', $this->param['naziv'], STRING);
        } else{
            $naziv = null;
        }

        if( isset($this->param['autor']) ){
            $autor = $this->validateParameter('autor', $this->param['autor'], STRING);
        } else{
            $autor = null;
        }
        
        if( isset($this->param['godina_izdavanja']) ){
            $godina_izdavanja = $this->validateParameter('godina_izdavanja', $this->param['godina_izdavanja'], INTEGER, false);
        } else{
            $godina_izdavanja = null;
        }

        if( isset($this->param['jezik']) ){
            $jezik = $this->validateParameter('jezik', $this->param['jezik'], STRING, false);
        } else{
            $jezik = null;
        }

        if( isset($this->param['originalni_jezik']) ){
            $originalni_jezik = $this->validateParameter('originalni_jezik', $this->param['originalni_jezik'], STRING, false);
        } else{
            $originalni_jezik = null;
        }

        try{

            if( $this->isAuthorized() ){
                $knjiga = new Knjiga;

                if( !$knjiga->update($this->param) ){
                    $message = "Failed to update."; 
                } else {
                    $message = "Updated successfully.";
                }

                $this->returnResponse(SUCCESS_RESPONSE, $message);
            } else{
                $this->throwError(ACCESS_TOKEN_ERRORS, 'You are not authorized to insert.');
            } 
            

        } catch(Exception $e){
            $this->throwError(ACCESS_TOKEN_ERRORS, $e->getMessage());
        }
    }

    public function deleteKnjiga()
    {
        $id = $this->validateParameter('id', $this->param['id'], INTEGER);

        $knjiga = new Knjiga;
        $knjiga->setId($id);

        if( $this->isAuthorized() ){
            if( !$knjiga->delete() ){
                $message = 'Greska prilikom brisanja knjige';
            } else {
                $message = 'Knjiga je uspesno izbrisana';
            }
            $this->returnResponse(SUCCESS_RESPONSE, $message);
        } else{
            $this->throwError(ACCESS_TOKEN_ERRORS, 'Niste autorizovani za brisanje knjige');
        } 
    }

    public function search()
    {
        isset($this->param['autor']) ? $autor = $this->param['autor'] : $autor = null;
        isset($this->param['godina_izdavanja']) ? $gi = $this->param['godina_izdavanja'] : $gi = null;

        $knjiga = new Knjiga;
        $result = $knjiga->search($autor, $gi);

        if( !$result ){
            $message = "Neuspela pretraga"; 
        } else {
            $message = "Pronadjene su sledece knjige";
        }

        $this->returnResponse(SUCCESS_RESPONSE, $result);

        

    }

    public function registracija()
    {
        $name = $this->validateParameter('name', $this->param['name'], STRING, false);
        $email = $this->validateParameter('email', $this->param['email'], STRING);
        $password = $this->validateParameter('password', $this->param['password'], STRING);

        try{
            $user = new User;
            $user->setName($name);
            $user->setEmail($email);
            $user->setPassword($password);

            if(!$user->register()){
                $message = "Failed to register."; 
            } else {
                $message = "Registred successfully.";
            }

            $this->returnResponse(SUCCESS_RESPONSE, $message);

        } catch(Exception $e){
            $this->throwError(400, $e->getMessage());
        }
    }
}

?>