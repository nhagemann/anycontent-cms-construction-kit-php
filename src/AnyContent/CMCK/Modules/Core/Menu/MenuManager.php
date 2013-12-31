<?php

namespace Anycontent\CMCK\Modules\Core\Menu;

use Knp\Menu\Matcher\Matcher;
use Knp\Menu\MenuFactory;

use AnyContent\CMCK\Modules\Core\Menu\NavBarListRenderer;

use AnyContent\Client\Client;

class MenuManager
{

    protected $repositoryManager;
    protected $twig;


    public function __construct($repositoryManager, $twig)
    {
        $this->repositoryManager = $repositoryManager;
        $this->twig              = $twig;
    }


    public function renderMainMenu()
    {

        $factory = new MenuFactory();

        $menu = $factory->createItem('main')->setAttribute('class', 'dropdown-menu');

        foreach ($this->repositoryManager->listRepositories() as $repositoryUrl => $repositoryItem)
        {

            $uri    = '/content/repository/' . $repositoryItem['accessHash'];
            $level2 = $menu->addChild($repositoryItem['title'], array( 'uri' => $uri ));

            foreach ($this->repositoryManager->listContentTypes($repositoryUrl) as $contentTypName => $contentTypeItem)
            {
                $uri = '/content/list/' . $contentTypeItem['accessHash'];
                $level2->addChild($contentTypName, array( 'uri' => $uri ));
            }
        }

        $menu->addChild('')->setAttribute('class', 'divider');
        $uri = 'logout';
        $menu->addChild('Logout', array( 'uri' => $uri ));

        $options = array(

            'currentClass'  => '',
            'ancestorClass' => '',
            'firstClass'    => '',
            'lastClass'     => ''
        );

        $renderer = new NavBarListRenderer(new Matcher(), $options);

        return $renderer->render($menu);
    }


    public function renderButtonGroup($buttons)
    {

        ksort($buttons);

        return $this->twig->render('core_menu_buttongroup.twig', array( 'buttons' => $buttons ));
    }
}