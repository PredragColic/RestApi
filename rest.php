<?php

require_once('constants.php');

class Rest
{
    protected $request;
    protected $serviceName;
    protected $param;

    public function __construct()
    {
        /*
        * Koristimo samo POST metodu, sve ostale metode nisu prihvatljive
        */      
        if($_SERVER['REQUEST_METHOD'] != 'POST'){
            $this->throwError(REQUEST_METHOD_NOT_VALID, 'Request Method is not valid.');
        }
        $handler = fopen('php://input', 'r');
        $this->request = stream_get_contents($handler);
        $this->validateRequest();
    }

    public function validateRequest()
    {
        if($_SERVER['CONTENT_TYPE'] !== 'application/json'){
            $this->throwError(REQUEST_CONTENTTYPE_NOT_VALID, 'Request content type is not valid.');
        }
        $data = json_decode($this->request, TRUE);

        /*
        * Za potrebe nekih API poziva bez parametra koje pomaze prilikom ispitivanje parametara u 50.
        */
        if(!isset($data['param'])){
            $data['param']=[];
        }
        $jump = false;
        
        /*
        * Potrebano je da znamo koju funkciju zovemo pozivamo u API-ju
        */
        if( !isset($data['name']) || empty($data['name']) ){
            $this->throwError(API_NAME_REQUIRED, 'API name is required.');
        }
        $this->serviceName = $data['name'];

        /*
        * Ako su potrebni parametri za poziv funkcije  
        */
        if( !is_array($data['param']) || $jump ){
            $this->throwError(API_PARAM_REQUIRED, 'API param is required.');
        }
        $this->param = $data['param'];
    }

    public function processApi()
    {
        $api = new API;
        if(method_exists($api, $this->serviceName)){
            $rMethod = new reflectionMethod('API', $this->serviceName);
            if( !method_exists($api, $this->serviceName) ){
                $this->throwError(API_DOST_NOT_EXIST, 'API does not existi.');
            }
            $rMethod->invoke($api);
        }

        $this->throwError(API_DOST_NOT_EXIST, 'API does not existi.');
    }

    public function validateParameter($fieldName, $value, $dataType, $required = true)
    {
        if($required == false && ( !isset($value) || empty($value) ) ){
            return null;
        }
        
        if($required == true && empty($value) == true ){
            $this->throwError(VALIDATE_PARAMETER_REQUIRED, $fieldName .' parameted is required.');
        }
        switch ($dataType) {
            case BOOLEAN:
                if( !is_bool($value) ){
                    $this->throwError(VALIDATE_PARAMETER_DATATYPE,'Datatype is not valid for '.$fieldName. '. It should be boolean');
                }
            break;

            case INTEGER:
                if( !is_numeric($value) ){
                    $this->throwError(VALIDATE_PARAMETER_DATATYPE,'Datatype is not valid for '.$fieldName. '. It should be numeric');
                }
            break;

            case STRING:
                if( !is_string($value) ){
                    $this->throwError(VALIDATE_PARAMETER_DATATYPE,'Datatype is not valid for '.$fieldName. '. It should be string');
                }
            break;

            default:
                if( !is_string($value) ){
                    $this->throwError(VALIDATE_PARAMETER_DATATYPE,'Datatype is not valid for '.$fieldName);
                }
            break;
        }
        return $value;
    }
    
    public function throwError($code, $message)
    {
        header("content-type: application/json");
        $errorMsg = json_encode(['error'=>['status'=>$code, 'message'=>$message]]);
        echo $errorMsg;
        exit;
    }

    public function returnResponse($code, $data)
    {
        header("content-type: application/json");
        $response = json_encode(['response'=> ['status'=> $code, 'result'=>$data]]);
        echo $response;
        exit;
    }

    /*GET header Authorization */

    public function getAuthorizationHeader()
    {
        $headers = null;
        if(isset($_SERVER['Authorization'])){
            $headers = trim($_SERVER['Authorization']);
        }else if(isset($_SERVER['HTTP_AUTHORIZATION'])){
            //Nginx or fast CGI
            $headers = trim($_SERVER['HTTP_AUTHORIZATION']);
        } elseif(function_exists('apache_request_headers')){
            $requestHeaders = apache_request_headers();
            // fix for android
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
            if(isset($requestHeaders['Authorization'])){
                $headers = trim($requestHeaders['Authorization']);
            }
        }
        return $headers;
    }

    // Get access token from header
    public function getBearerToken()
    {
        $headers = $this->getAuthorizationHeader();
        // HEADER: Get the access token from the header
        if(!empty($headers)){
            if(preg_match('/Bearer\s(\S+)/', $headers, $matches)){
                return $matches[1];
            }
        }
        $this->throwError(AUTHORIZATION_HEADER_NOT_FOUND,'Access Token Not found');
    }

}

?>