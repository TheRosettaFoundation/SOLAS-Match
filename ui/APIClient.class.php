<?php

/*
 *  A client for accessing the DB through the API
 *  Makes HTTP requests to the API server and handles serialization and deserialization
 *
 *  @author: Dave O Carroll
 */

require_once 'HTTP/Request2.php';

class APIClient
{
    public function call($url, $method = HTTP_Request2::METHOD_GET, 
                    $data = null, $query_args = array(), $format = ".php")
    {
        $app = Slim::getInstance();

        $settings = new Settings();
        $request_url = $settings->get('site.api');
        $request_url .= $url;
        echo $request_url;
        $request = new HTTP_Request2($request_url, $method);
        $response = $request->send();
        return $this->deserialize($response->getBody(), $format);
    }

    private function deserialize($data, $format = ".php")
    {
        $ret = null;
        switch ($format) {
            case ".php": {
                try {
                    $ret = unserialize($data);
                } catch (Exception $e) {
                    echo "Failed to unserialize data: $data";
                }
                break;
            }
        }
        return $ret;
    }
}
