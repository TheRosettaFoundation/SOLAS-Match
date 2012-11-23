<?php

/*
 *  A client for accessing the DB through the API
 *  Makes HTTP requests to the API server and handles serialization and deserialization
 *
 *  @author: Dave O Carroll
 */

require_once 'Serializer.class.php';

class APIClient
{
    var $_serializer;
    const API_VERSION = 'v0';

    public function APIClient()
    {
        $this->_serializer = new Serializer();
    }

    public function call($url, $method = HTTP_Request2::METHOD_GET, 
                    $data = null, $query_args = array(), $format = ".json",$file=null)
    {
        $app = Slim::getInstance();
        $settings = new Settings();

        $request_url = $settings->get('site.api');
        $request_url .= $url.$format.'/?';
        $request = new HTTP_Request2($request_url, $method);

        if(!is_null($data)&&"null"!=$data) {
            $data=$this->_serializer->serialize($data, $this->_serializer->getFormat($format));
            $request->setBody($data);
            
        }  elseif (!is_null($file)) {
           $request->setBody($file);
        }

        if(count($query_args) > 0) {
            $url = $request->getUrl();
            $url->setQueryVariables($query_args);
        }

        $response = $request->send();
        $response_data = $this->_serializer->deserialize(trim($response->getBody()), $format);
        return $response_data;
    }

    public function cast($destination, $sourceObject)
    {
        return $this->_serializer->cast($destination, $sourceObject);
    }
    
    public function castCall($destination,$url, $method = HTTP_Request2::METHOD_GET, 
        $data = null, $query_args = array(), $format = ".json"){
        $ret=null;
        $result= $this->call($url, $method,$data, $query_args, $format);
        if(is_array($destination)){
            if($result){
                foreach($result as $row){
                    $ret[]=$this->_serializer->cast($destination[0],$row);
                }
            }
        }else $ret=$this->_serializer->cast($destination,$result);
        return $ret; 
    }
}
