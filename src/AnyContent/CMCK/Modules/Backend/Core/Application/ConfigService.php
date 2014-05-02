<?php

namespace AnyContent\CMCK\Modules\Backend\Core\Application;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Yaml\Parser;

class ConfigService
{

    protected $app;

    protected $yml = null;


    public function __construct(Application $app)
    {
        $this->app = $app;

    }


    public function getToBeConnectedRepositories()
    {
        $yml = $this->getYML();

        if (!isset($yml['repositories']) || !is_array($yml['repositories']))
        {
            throw new \Exception ('Missing or incomplete repositories configuration.');
        }

        $repositories = array();
        foreach ($yml['repositories'] as $shortcut => $repository)
        {
            $repositories[$shortcut]['url']      = $repository;
            $repositories[$shortcut]['shortcut'] = (string)$shortcut;
        }

        return $repositories;
    }


    public function getCMDLDirectory()
    {
        return $this->basepath . 'cmdl';
    }


    public function getClientUserInfo()
    {
        $yml = $this->getYML();

        if (!isset($yml['userinfo']['username']) || !isset($yml['userinfo']['firstname']) || !isset($yml['userinfo']['lastname']))
        {
            throw new \Exception ('Missing or incomplete user info configuration.');
        }

        return new \AnyContent\Client\UserInfo($yml['userinfo']['username'], $yml['userinfo']['firstname'], $yml['userinfo']['lastname']);
    }


    public function getConfiguredApps($repositoryShortcut)
    {
        $yml = $this->getYML();

        $apps = array();
        if (isset($yml['apps']))
        {

            foreach ($yml['apps'] as $app)
            {
                $repositories = explode(',',$app['repositories']);
                if (in_array($repositoryShortcut,$repositories))
                {
                    $apps[]=$app;
                }
            }
        }

        return $apps;
    }


    protected function getYML()
    {
        if ($this->yml)
        {
            return $this->yml;
        }

        if (!file_exists(APPLICATION_PATH . '/config/config.yml'))
        {
            throw new \Exception ('Missing configuration file /config/config.yml');
        }

        $configFile = file_get_contents(APPLICATION_PATH . '/config/config.yml');

        $yamlParser = new Parser();

        $this->yml = $yamlParser->parse($configFile);

        return $this->yml;
    }

}