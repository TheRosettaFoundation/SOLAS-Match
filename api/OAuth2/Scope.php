<?php

namespace League\OAuth2\Server\Storage\PDO;

require_once __DIR__."/../lib/PDOWrapper.class.php";

use \League\OAuth2\Server\Storage\ScopeInterface;
use \SolasMatch\API\Lib\PDOWrapper;

class Scope implements ScopeInterface
{
    public function getScope($scope, $clientId = null, $grantType = null)
    {
        $args = PDOWrapper::cleanseNullOrWrapStr($scope);
        if ($result = PDOWrapper::call("oauthGetScope", $args)) {
            $result = $result[0];
            return array(
                'id' => $result['id'],
                'scope' => $result['scope'],
                'name' => $result['name'],
                'description' => $result['description']
            );
        } else {
            return false;
        }
    }
}
