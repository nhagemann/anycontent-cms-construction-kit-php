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

        $this->adapter = $app->getAuthenticationAdapter($config->getAuthenticationConfiguration());
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


    public function getClientUserInfo()
    {

        return new \AnyContent\Client\UserInfo($this->getUserName(), $this->getFirstName(), $this->getLastName());
    }


    public function getUserName()
    {
        return $this->adapter->getUserName();
    }


    public function getFirstName()
    {
        return $this->adapter->getFirstName();
    }


    public function getLastName()
    {
        return $this->adapter->getLastName();
    }


    public function getFullName()
    {
        return $this->adapter->getFullName();
    }


    /**
     * Allow additionally arbitrary methods on user adapter
     *
     * @param $name
     * @param $arguments
     *
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return call_user_func_array(array( $this->adapter, $name ), $arguments);
    }
}
