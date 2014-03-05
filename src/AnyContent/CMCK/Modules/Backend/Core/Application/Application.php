<?php

namespace AnyContent\CMCK\Modules\Backend\Core\Application;

use Silex\Application as SilexApplication;

class Application extends SilexApplication
{

    protected $modules = array();

    protected $templatesFolder = array();

    protected $repositories = array();


    public function registerModule($class, $options = array())
    {
        $this->modules[$class] = array( 'class' => $class, 'options' => $options );

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


    public function setCacheDriver($cache)
    {
        $this['cache'] = $cache;
    }


    public function initModules()
    {

        foreach ($this->modules as $module)
        {
            $class = $module['class'] . '\Module';
            $o     = new $class;
            $o->init($this, $module['options']);
            $module['module']      = $o;
            $this->modules[$module['class']] = $module;
        }

        $this->register(new \Silex\Provider\TwigServiceProvider(), array(
            'twig.path' => array_reverse($this->templatesFolder)
        ));

        $this['twig']->setCache(APPLICATION_PATH.'/twig-cache');
    }


    public function run($request = null)
    {

        foreach ($this->modules as $module)
        {
            $module['module']->run($this);

        }

        parent::run($request);
    }


    public function renderPage($templateFilename, $vars = array(), $displayMessages = true)
    {
        foreach ($this->modules as $module)
        {
            $module['module']->preRender($this);

        }

        return $this['layout']->render($templateFilename, $vars, $displayMessages);
    }

}