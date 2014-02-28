<?php

namespace League\OAuth2\Server\Storage\PDO;

require_once __DIR__."/../lib/PDOWrapper.class.php";

use \League\OAuth2\Server\Storage\ClientInterface;
use \SolasMatch\API\Lib\PDOWrapper;

class Client implements ClientInterface
{
    public function getClient($clientId, $clientSecret = null, $redirectUri = null, $grantType = null)
    {        
        $args = PDOWrapper::cleanseNullOrWrapStr($clientId).",".
            PDOWrapper::cleanseNullOrWrapStr($clientSecret).",".
            PDOWrapper::cleanseNullOrWrapStr($redirectUri);
        error_log("Call oauthGetClient($args)");
        if($result = PDOWrapper::call("oauthGetClient", $args)) {
            $result = $result[0];
            return array(
                'client_id' => $result['id'],
                'client_secret' => $result['secret'],
                'redirect_uri' => $result['redirect_uri'],
                'name' => $result['name'],
                'auto_approve' => $result['auto_approve']
            );
        } else {
            return false;
        }
    }
}
