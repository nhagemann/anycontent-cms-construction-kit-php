<?php

namespace AnyContent\CMCK;

class RepositoriesManagerTest extends \PHPUnit_Framework_TestCase
{

    protected $app;


    public function setUp()
    {

        $app          = new \AnyContent\CMCK\Application\Application();
        $app['debug'] = 1;

        $app->registerModule('AnyContent\CMCK\Modules\Core\Layouts');
        $app->registerModule('AnyContent\CMCK\Modules\Core\Listing');
        $app->registerModule('AnyContent\CMCK\Modules\Core\Repositories');
        $app->registerModule('AnyContent\CMCK\Modules\Core\Menu');

        $app->initModules();

        $this->app = $app;

    }


    public function testAddManyRepos()
    {

        $this->app['repos']->addOneContentType('example01', 'http://anycontent.dev/1/example');
        $this->app['repos']->addAllContentTypesOfRepository('http://anycontent.dev/1/example');


        foreach ($this->app['repos']->listRepositories() as $url => $repositoryTitle)
        {
            var_dump ($this->app['repos']->listContentTypes($url));
        }


        $this->app['menus']->getMainMenu();
    }

}