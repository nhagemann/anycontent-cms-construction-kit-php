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


    public function getRepositoryURLs()
    {
        $yml = $this->getYML();

        if (!isset($yml['repositories']) || !is_array($yml['repositories']))
        {
            throw new \Exception ('Missing or incomplete repositories configuration.');
        }

        return $yml['repositories'];
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


    protected function getYML()
    {
        if ($this->yml)
        {
            return $this->yml;
        }

        $configFile = file_get_contents(APPLICATION_PATH . '/config/config.yml');

        $yamlParser = new Parser();

        $this->yml = $yamlParser->parse($configFile);

        return $this->yml;
    }

}