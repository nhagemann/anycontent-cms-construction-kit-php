<?php

$aCms = new AnyContentCMC($app);

$aCms->registerModule('abc', 'Modules\Core\Layout');

class Module
{

    public function bootstrap($aCms)
    {
        $aCms->registerLayout('plain', 'CLASS');

        $aCms->registerBlock('menu_left', 'CLASS');

        $aCms->addMenuItem('logout', $glyphicon, $url);

        $aCms->removeMenuItem('xyz');

        $aCms->registerRoute('horst', '/test/{abc}', 'CLASS::method');

        $aCms->addBlockToRoute('horst', 'menu_left', 'plain');

        $aCms->addBlockToLayout('horst', 'menu_left', 'plain');
    }
}


class Route
{

    public function init($aCms,$request,$response)
    {
        $aCms->addContentQuery('current_news',ContentQuery $abc);

        //$this->setLayout('plain');
    }

}


class Layout
{

    public function init($aCms,$request,$response)
    {
        $aCms->addContentQuery('current_news',ContentQuery $abc);
        $this->removeBlock('xyz');
    }

    public function render()
    {

    }

}

class Block
{

    public function init($aCms,$request,$response)
    {
        $aCms->addContentQuery('current_news',ContentQuery $abc);

    }

    public function render()
    {

    }

}