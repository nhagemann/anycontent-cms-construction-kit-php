<?php

namespace Anycontent\CMCK\Modules\Core\Menu;

class MenuManager
{

    protected $repositoryManager;
    protected $twig;
    protected $layout;


    public function __construct($repositoryManager, $twig, $layout)
    {
        $this->repositoryManager = $repositoryManager;
        $this->twig              = $twig;
        $this->layout            = $layout;
    }


    public function renderMainMenu()
    {
        $items = array();

        foreach ($this->repositoryManager->listRepositories() as $repositoryUrl => $repositoryItem)
        {

            $url     = '/content/repository/' . $repositoryItem['accessHash'];
            $items[] = array( 'type' => 'header', 'text' => $repositoryItem['title'], 'url' => $url );

            foreach ($this->repositoryManager->listContentTypes($repositoryUrl) as $contentTypName => $contentTypeItem)
            {
                $url     = '/content/list/' . $contentTypeItem['accessHash'].'/page/1';
                $items[] = array( 'type' => 'link', 'text' => $contentTypName, 'url' => $url, 'glyphicon' => 'glyphicon-file' );
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