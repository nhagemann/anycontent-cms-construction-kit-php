<?php

namespace AnyContent\CMCK\Modules\Backend\Core\Menu;

class MenuManager
{

    protected $app;
    protected $repositoryManager;
    protected $twig;
    protected $layout;
    protected $urlGenerator;
    protected $cache;
    protected $cacheSeconds = 0;
    protected $session;


    public function __construct($app, $repositoryManager, $twig, $layout, $urlGenerator, $cache, $config)
    {
        $this->app               = $app;
        $this->session           = $app['session'];
        $this->repositoryManager = $repositoryManager;
        $this->twig              = $twig;
        $this->layout            = $layout;
        $this->urlGenerator      = $urlGenerator;
        $this->cache             = $cache;
        $cacheConfiguration      = $config->getCacheConfiguration();
        $this->cacheSeconds      = $cacheConfiguration['seconds_caching_menu'];
    }


    public function renderMainMenu()
    {
        $this->layout->addCssFile('menu.css');

        $cacheToken = 'cmck_menu_main_' . $this->session->getId();

        if ($this->cacheSeconds > 0 && $this->cache->contains($cacheToken))
        {
            return $this->cache->fetch($cacheToken);
        }

        $items = array();

        foreach ($this->repositoryManager->listRepositories() as $repositoryUrl => $repositoryItem)
        {

            $url     = $this->urlGenerator->generate('indexRepository', array( 'repositoryAccessHash' => $repositoryItem['accessHash'] ));
            $items[] = array( 'type' => 'header', 'text' => $repositoryItem['title'], 'url' => $url );

            foreach ($this->repositoryManager->listContentTypes($repositoryUrl) as $contentTypName => $contentTypeItem)
            {
                $url     = $this->urlGenerator->generate('listRecords', array( 'contentTypeAccessHash' => $contentTypeItem['accessHash'], 'page' => 1 ));
                $items[] = array( 'type' => 'link', 'text' => $contentTypeItem['title'], 'url' => $url, 'glyphicon' => 'glyphicon-file' );
            }
            foreach ($this->repositoryManager->listConfigTypes($repositoryUrl) as $configTypeName => $configTypeItem)
            {
                $url     = $this->urlGenerator->generate('editConfig', array( 'configTypeAccessHash' => $configTypeItem['accessHash'] ));
                $items[] = array( 'type' => 'link', 'text' => $configTypeItem['title'], 'url' => $url, 'glyphicon' => 'glyphicon-wrench' );
            }
            if ($this->repositoryManager->hasFiles($repositoryUrl))
            {
                $url     = $this->urlGenerator->generate('listFiles', array( 'repositoryAccessHash' => $repositoryItem['accessHash'], 'path' => '' ));
                $items[] = array( 'type' => 'link', 'text' => 'Files', 'url' => $url, 'glyphicon' => 'glyphicon-folder-open' );
            }
            foreach ($this->repositoryManager->listApps($repositoryUrl) as $appName => $appItem)
            {

                $url     = rtrim($appItem['url'], '/') . '/' . $repositoryItem['accessHash'];
                $items[] = array( 'type' => 'link', 'text' => $appName, 'url' => $url, 'glyphicon' => 'glyphicon-dashboard' );
            }
            $items[] = array( 'type' => 'divider' );
        }

        // Add menu items Admin and/or Help if appropriate routes exist

        if ($this->app->routeExists('admin') || $this->app->routeExists('help'))
        {
            if ($this->app->routeExists('admin'))
            {
                $url     = $this->urlGenerator->generate('admin');
                $items[] = array( 'type' => 'link', 'text' => 'Admin', 'url' => $url, 'glyphicon' => 'glyphicon-cog' );

            }
            if ($this->app->routeExists('help'))
            {
                $url     = $this->urlGenerator->generate('help');
                $items[] = array( 'type' => 'link', 'text' => 'Help', 'url' => $url, 'glyphicon' => 'glyphicon-book' );

            }
            $items[] = array( 'type' => 'divider' );
        }

        $url     = $this->urlGenerator->generate('logout');
        $items[] = array( 'type' => 'link', 'text' => 'Logout', 'url' => $url, 'glyphicon' => 'glyphicon-user' );

        $html = $this->renderDropDown($items, 'mainmenu');

        $this->cache->save($cacheToken, $html, $this->cacheSeconds);

        return $html;
    }


    public function renderDropDown($items, $id = null)
    {
        return $this->twig->render('core_menu_dropdown.twig', array( 'items' => $items, 'id' => $id ));
    }


    public function renderButtonGroup($buttons)
    {

        ksort($buttons);

        /** @var MenuButtonGroupRenderEvent $event */
        $event = new MenuButtonGroupRenderEvent($this->app, $buttons);

        $event = $this->app['dispatcher']->dispatch(Module::EVENT_MENU_BUTTONGROUP_RENDER, $event);

        $buttons = $event->getButtons();

        return $this->twig->render('core_menu_buttongroup.twig', array( 'buttons' => $buttons ));
    }


    public function clearCache()
    {
        $cacheToken = 'cmck_menu_main_' . $this->session->getId();
        $this->cache->delete($cacheToken);
    }
}