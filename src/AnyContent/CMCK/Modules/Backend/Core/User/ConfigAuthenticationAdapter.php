<?php

namespace AnyContent\CMCK\Modules\Backend\Core\User;

use AnyContent\CMCK\Modules\Backend\Core\Context;

class ConfigAuthenticationAdapter
{

    protected $session;

    protected $prefix = 'user_';

    protected $users = array();

    protected $user = array();


    public function __construct($config, $session, $options)
    {
        $this->session = $session;
        if (!$this->session->has($this->prefix . 'username'))
        {
            $this->session->set($this->prefix . 'username', '');
        }
        if (!$this->session->has($this->prefix . 'firstname'))
        {
            $this->session->set($this->prefix . 'firstname', '');
        }
        if (!$this->session->has($this->prefix . 'lastname'))
        {
            $this->session->set($this->prefix . 'lastname', '');
        }

        if (isset($config['users']))
        {
            $this->users = $config['users'];
        }

    }


    public function isLoggedIn()
    {
        if ($this->session->get($this->prefix . 'username') != '')
        {
            return true;
        }

        return false;
    }


    public function login($username, $password)
    {
        $username = trim($username);
        $password = trim($password);
        if ($username != '' && $password != '')
        {
            foreach ($this->users as $user)
            {
                if (@$user['username'] === $username)
                {
                    $valid = false;

                    switch (@$user['encryption'])
                    {
                        case 'none':
                        default:
                            if (@$user['password'] === $password)
                            {
                                $valid = true;
                            }
                            break;
                    }
                    if ($valid)
                    {
                        $this->user = $user;
                        $this->session->set($this->prefix . 'username', @$user['username']);
                        $this->session->set($this->prefix . 'firstname', @$user['firstname']);
                        $this->session->set($this->prefix . 'lastname', @$user['lastname']);

                        return true;
                    }
                    else
                    {
                        return false;
                    }
                }

            }
        }
        $this->session->set($this->prefix . 'username', '');

        return false;
    }


    public function logout()
    {

        $this->session->set($this->prefix . 'username', '');
    }


    public function getUserName()
    {
        return $this->session->get($this->prefix . 'username');
    }


    public function getFirstName()
    {
        return $this->session->get($this->prefix . 'firstname');
    }


    public function getLastName()
    {
        return $this->session->get($this->prefix . 'lastname');
    }


    public function getFullName()
    {
        return trim($this->getFirstName() . ' ' . $this->getLastName());
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