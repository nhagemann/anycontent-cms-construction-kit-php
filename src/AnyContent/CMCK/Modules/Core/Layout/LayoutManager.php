<?php

namespace Anycontent\CMCK\Modules\Core\Layout;


class LayoutManager
{

    protected $twig;

    protected $vars = array();

    protected $cssFiles = array();

    protected $jsFiles = array();


    public function __construct($twig)
    {
        $this->twig = $twig;
    }

    public function addVar($key,$value)
    {
        $this->vars[$key]=$value;
    }

    public function addCssFile($filename)
    {
        $this->cssFiles[] = $filename;
    }

    public function addJsFile($filename)
    {
        $this->jsFiles[] = $filename;
    }


    public function render($templateFilename, $vars = array())
    {

        $vars = array_merge($this->vars,$vars);

        $cssurl = '';
        foreach ($this->cssFiles as $cssFilename)
        {
           $cssurl .= pathinfo($cssFilename,PATHINFO_FILENAME).'/';
        }
        $cssurl = trim($cssurl,'/');

        $vars['cssurl']=$cssurl;

        $jsurl = '';
        foreach ($this->jsFiles as $jsFilename)
        {
            $jsurl .= pathinfo($jsFilename,PATHINFO_FILENAME).'/';
        }
        $jsurl = trim($jsurl,'/');

        $vars['jsurl']=$jsurl;

        return $this->twig->render($templateFilename, $vars);
    }
}