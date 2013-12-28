<?php

namespace AnyContent\CMCK\Application;

use Silex\Application as SilexApplication;

class Application extends SilexApplication
{

    protected $modules = array();
    protected $templatesFolder = array();

    protected $repositories = array();


    public function registerModule($class)
    {
        $this->modules[] = $class;

    }


    /**
     * Adds a path to a folder with templates. The later you add a folder, the more priority you give to the folder, in case a template file exists more than once.
     *
     * @param $path
     */
    public function addTemplatesFolders($path)
    {
        $this->templatesFolder[] = $path;
    }


    public function addRepository($url, $user = null, $password = null, $title = null)
    {
        if (!$title)
        {
            $title = $url;
        }

        $this->repositories[$title] = $url;
    }



    public function getContentList()
    {
        foreach ($this->repositories as $repository)
        {

        }
    }

    public function run($request = null)
    {
        foreach ($this->modules as $module)
        {
            $module .= '\Module';
            $module::init($this);
        }

        $this->register(new \Silex\Provider\TwigServiceProvider(), array(
            'twig.path' => array_reverse($this->templatesFolder),
        ));

        parent::run($request);
    }

}