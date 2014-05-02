<?php

namespace AnyContent\CMCK\Modules\Backend\Core\Menu;

class MenuManager
{

    protected $repositoryManager;
    protected $twig;
    protected $layout;
    protected $urlGenerator;


    public function __construct($repositoryManager, $twig, $layout, $urlGenerator)
    {
        $this->repositoryManager = $repositoryManager;
        $this->twig              = $twig;
        $this->layout            = $layout;
        $this->urlGenerator      = $urlGenerator;
    }


    public function renderMainMenu()
    {
        $items = array();

        foreach ($this->repositoryManager->listRepositories() as $repositoryUrl => $repositoryItem)
        {

            $url     = $this->urlGenerator->generate('indexRepository', array( 'repositoryAccessHash' => $repositoryItem['accessHash'] ));
            $items[] = array( 'type' => 'header', 'text' => $repositoryItem['title'], 'url' => $url );

            foreach ($this->repositoryManager->listContentTypes($repositoryUrl) as $contentTypName => $contentTypeItem)
            {
                $url     = $this->urlGenerator->generate('listRecords', array( 'contentTypeAccessHash' => $contentTypeItem['accessHash'], 'page' => 1 ));
                $items[] = array( 'type' => 'link', 'text' => $contentTypName, 'url' => $url, 'glyphicon' => 'glyphicon-file' );
            }
            foreach ($this->repositoryManager->listConfigTypes($repositoryUrl) as $configTypeName => $configTypeItem)
            {
                $url     = $this->urlGenerator->generate('editConfig', array( 'configTypeAccessHash' => $configTypeItem['accessHash'] ));
                $items[] = array( 'type' => 'link', 'text' => $configTypeName, 'url' => $url, 'glyphicon' => 'glyphicon-wrench' );
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

        $items[] = array( 'type' => 'link', 'text' => 'Logout', 'url' => '#', 'glyphicon' => 'glyphicon-user' );

        $this->layout->addCssFile('menu.css');

        return $this->renderDropDown($items);
    }


    public function renderDropDown($items)
    {
        return $this->twig->render('core_menu_dropdown.twig', array( 'items' => $items ));
    }


    public function renderButtonGroup($buttons)
    {

        ksort($buttons);

        return $this->twig->render('core_menu_buttongroup.twig', array( 'buttons' => $buttons ));
    }
}