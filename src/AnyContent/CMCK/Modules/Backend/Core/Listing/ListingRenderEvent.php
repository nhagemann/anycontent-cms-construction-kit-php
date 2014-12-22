<?php

namespace AnyContent\CMCK\Modules\Backend\Core\Listing;

use AnyContent\CMCK\Modules\Backend\Core\Application\Application;
use Symfony\Component\EventDispatcher\Event;

class ListingRenderEvent extends Event
{

    protected $app;
    protected $template;
    protected $vars;

    function __construct(Application $app, $template, $vars)
    {
        $this->app      = $app;
        $this->template = $template;
        $this->vars     = $vars;
    }

    /**
     * @return mixed
     */
    public function getTemplate()
    {
        return $this->template;
    }


    /**
     * @return mixed
     */
    public function getVars()
    {
        return $this->vars;
    }



}