<?php

namespace AnyContent\CMCK\Modules\Backend\Core\User;

use AnyContent\CMCK\Modules\Backend\Core\Context\ContextManager;

class UserManager
{

    protected $cache = null;

    /** @var  ContextManager */
    protected $context;

    protected $config;

    protected $adapter;


    public function __construct($app, $context, $config, $session)
    {

        $this->context = $context;
        $this->config  = $config;
        $this->session = $session;

        $this->adapter = $app->getAuthenticationAdapter($config->getAuthenticationAdapterConfig());
    }


    public function isLoggedIn()
    {
        return $this->adapter->isLoggedIn();
    }


    public function login($username, $password)
    {
        return $this->adapter->login($username, $password);

    }


    public function logout()
    {

        $this->adapter->logout();
    }
}
