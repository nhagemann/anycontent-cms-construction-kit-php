<?php

namespace AnyContent\CMCK\Modules\Backend\Core\Application;

use AnyContent\CMCK\Modules\Backend\Core\Context\ContextManager;
use Silex\Application as SilexApplication;

use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

use Symfony\Component\HttpFoundation\Request;

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

            $adapter = new $class($config, $this['session'], $options);
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

        $this->addTemplatesFolders(__DIR__ . '/views/');

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
                $memcached->setOption(\Memcached::OPT_BINARY_PROTOCOL, 1);
                if (array_key_exists('username', $cacheConfiguration['driver']))
                {
                    $memcached->setSaslAuthData($cacheConfiguration['driver']['username'], $cacheConfiguration['driver']['password']);
                }
                $cacheDriver = new \Doctrine\Common\Cache\MemcachedCache();
                $cacheDriver->setMemcached($memcached);
                $this->setCacheDriver($cacheDriver);
                break;
            case 'file':
                $cacheDriver = new PhPFileCache(APPLICATION_PATH . '/doctrine-cache', 'txt');
                $this->setCacheDriver($cacheDriver);
                break;
        }

        // Now add the repositories

        $this['repos']->init($this['config']);

        foreach ($this->modules as $module)
        {
            $module['module']->run($this);

        }

        $this['repos']->setUserInfo($this['user']->getClientUserInfo());

    }


    public function renderPage($templateFilename, $vars = array(), $displayMessages = true)
    {
        foreach ($this->modules as $module)
        {
            $module['module']->preRender($this);

        }


        $vars['requestLog'] = false;

        /** @var ContextManager $contextManager */
        $contextManager = $this['context'];

        $repository = $contextManager->getCurrentRepository();

        if ($repository)
        {
            $client = $repository->getClient();

            $vars['requestLog'] = $client->getLog();

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


    /**
     * Adds an event listener that listens on the specified events.
     *
     * @param string   $eventName The event to listen on
     * @param callable $listener  The listener
     * @param int      $priority  The higher this value, the earlier an event
     *                            listener will be triggered in the chain (defaults to 0)
     *
     * @see EventDispatcherInterface::addListener
     */
    public function addListener($eventName, $listener, $priority = 0)
    {
        $this['dispatcher']->addListener($eventName, $listener, $priority);
    }


    /**
     * Check if a route has been defined
     *
     * @param $name
     *
     * @return bool
     */
    public function routeExists($name)
    {
        $routeCollection = $this['routes'];

        return (null === $routeCollection->get($name)) ? false : true;
    }


    /**
     * Revision is used for caching, random number during development ($app['debug']=true)
     *
     * @return string
     */
    public function getRevision()
    {
        if ($this['debug'] == false)
        {

            if (file_exists(APPLICATION_PATH . '/config/revision.txt'))
            {
                return file_get_contents(APPLICATION_PATH . '/config/revision.txt');
            }
        }

        return substr(md5(uniqid()), 0, 8);
    }

}

