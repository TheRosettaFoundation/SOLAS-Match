<?php

namespace SolasMatch\UI\DAO;

abstract class BaseDao
{
    protected $client;
    protected $siteApi;
    
    public function getClient()
    {
        return $this->client;
    }
}
