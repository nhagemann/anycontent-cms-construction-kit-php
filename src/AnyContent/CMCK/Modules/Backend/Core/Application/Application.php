<?php

namespace AnyContent\CMCK\Modules\Backend\Core\Application;

use Silex\Application as SilexApplication;

use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

use Knp\Provider\ConsoleServiceProvider;

class Application extends SilexApplication
{

    protected $modules = array();

    protected $templatesFolder = array();

    protected $repositories = array();

    protected $authenticationAdapter = array();


    public function __construct(array $values = array())
    {

        parent::__construct($values);

        $this['config'] = $this->share(function ($this)
        {
            return new ConfigService($this);
        });
    }


    public function registerModule($class, $options = array())
    {
        $this->modules[$class] = array( 'class' => $class, 'options' => $options );

    }


    public function registerAuthenticationAdapter($type, $class, $options = array())
    {
        $this->authenticationAdapter[$type] = array( 'class' => $class, 'options' => $options );
    }


    public function getAuthenticationAdapter($config)
    {
        if (array_key_exists($config['type'], $this->authenticationAdapter))
        {

            $class   = $this->authenticationAdapter[$config['type']]['class'];
            $options = $this->authenticationAdapter[$config['type']]['options'];
            unset($config['type']);

            $adapter = new $class($config,$this['session'],$options);
        }
        else
        {
            throw new \Exception ('Unknown authentication adapter type ' . $config['type'] . '.');
        }

        return $adapter;
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
        $this->register(new ConsoleServiceProvider(), array(
            'console.name'              => 'AnyContent CMCK Console',
            'console.version'           => '1.0.0',
            'console.project_directory' => APPLICATION_PATH
        ));

        foreach ($this->modules as $module)
        {
            $class = $module['class'] . '\Module';
            $o     = new $class;
            $o->init($this, $module['options']);
            $module['module']                = $o;
            $this->modules[$module['class']] = $module;
        }

        $this->register(new \Silex\Provider\TwigServiceProvider(), array(
            'twig.path' => array_reverse($this->templatesFolder)
        ));

        $this['twig']->setCache(APPLICATION_PATH . '/twig-cache');
    }


    public function run($request = null)
    {
        // Init Cache

        $cacheConfiguration = $this['config']->getCacheConfiguration();

        switch ($cacheConfiguration['driver']['type'])
        {
            case 'apc':
                $cacheDriver = new  \Doctrine\Common\Cache\ApcCache();
                $this->setCacheDriver($cacheDriver);
                break;
            case 'memcached':
                $memcached = new \Memcached();
                $memcached->addServer($cacheConfiguration['driver']['host'], $cacheConfiguration['driver']['port']);
                $cacheDriver = new \Doctrine\Common\Cache\MemcachedCache();
                $cacheDriver->setMemcached($memcached);
                $this->setCacheDriver($cacheDriver);
                break;
        }

        // Now add the repositories

        foreach ($this['config']->getToBeConnectedRepositories() as $repository)
        {
            $this['repos']->addAllContentTypesOfRepository($repository['url'], null, null, 'Basic', $repository['shortcut'], null);
            $this['repos']->addAllConfigTypesOfRepository($repository['url']);

            foreach ($this['config']->getConfiguredApps($repository['shortcut']) as $app)
            {
                if (array_key_exists('url', $app))
                {
                    $name = 'Content App';
                    if (array_key_exists('name', $app))
                    {
                        $name = $app['name'];
                        unset($app['name']);
                    }
                    $this['repos']->addAppToRepository($repository['url'], $name, $app);
                }
            }
        }

        foreach ($this->modules as $module)
        {
            $module['module']->run($this);

        }

        $this['repos']->setUserInfo($this['user']->getClientUserInfo());

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


    /**
     * Registers a before filter.
     *
     * Before filters are run before any route has been matched. This override additionally provides the application
     * object to the filter.
     *
     * @param mixed   $callback Before filter callback
     * @param integer $priority The higher this value, the earlier an event
     *                          listener will be triggered in the chain (defaults to 0)
     */
    public function before($callback, $priority = 0)
    {
        $app = $this;

        $this->on(KernelEvents::REQUEST, function (GetResponseEvent $event) use ($callback, $app)
        {
            if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType())
            {
                return;
            }

            $ret = call_user_func($app['callback_resolver']->resolveCallback($callback), $event->getRequest(), $app);

            if ($ret instanceof Response)
            {
                $event->setResponse($ret);
            }
        }, $priority);
    }
}

