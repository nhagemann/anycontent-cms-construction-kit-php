<?php

namespace Anycontent\CMCK\Modules\Core\Layout;

class LayoutManager
{

    protected $twig;

    protected $context;

    protected $vars = array();

    protected $cssFiles = array();

    protected $jsFiles = array();

    protected $jsLinks = array( 'head' => array(), 'body' => array() );


    public function __construct($twig, $context)
    {
        $this->twig    = $twig;
        $this->context = $context;
    }


    public function addVar($key, $value)
    {
        $this->vars[$key] = $value;
    }


    public function getVar($key, $default = '')
    {
        if (array_key_exists($key, $this->vars))
        {
            return $this->vars[$key];
        }

        return $default;
    }


    public function addCssFile($filename)
    {
        if (!in_array($filename, $this->cssFiles))
        {
            $this->cssFiles[] = $filename;
        }

    }


    public function addJsFile($filename)
    {

        if (!in_array($filename, $this->jsFiles))
        {
            $this->jsFiles[] = $filename;
        }
    }


    public function addJsLinkToHead($link)
    {
        $this->jsLinks['head'][] = $link;
    }


    public function addJsLinkToEndOfBody($link)
    {
        $this->jsLinks['body'][] = $link;
    }


    public function render($templateFilename, $vars = array(), $displayMessages = true)
    {

        $this->addCssFile('layout.css');
        $this->addJsFile('messages.js');

        $vars = array_merge($this->vars, $vars);

        $cssurl = '';
        foreach ($this->cssFiles as $cssFilename)
        {
            $cssurl .= pathinfo($cssFilename, PATHINFO_FILENAME) . '/';
        }
        $cssurl         = trim($cssurl, '/');
        $vars['cssurl'] = $cssurl;

        $jsurl = '';
        foreach ($this->jsFiles as $jsFilename)
        {
            $jsurl .= pathinfo($jsFilename, PATHINFO_FILENAME) . '/';
        }
        $jsurl         = trim($jsurl, '/');
        $vars['jsurl'] = $jsurl;

        $jsheadlinks = '';
        foreach ($this->jsLinks['head'] as $link)
        {
            $jsheadlinks .= '<script src="' . $link . '"></script>' . PHP_EOL;
        }
        $vars['jsheadlinks'] = $jsheadlinks;

        $jsbodylinks = '';
        foreach ($this->jsLinks['body'] as $link)
        {
            $jsbodylinks .= '<script src="' . $link . '"></script>' . PHP_EOL;
        }
        $vars['jsbodylinks'] = $jsheadlinks;

        if ($displayMessages)
        {

            $messages            = array();
            $messages['success'] = $this->context->getSuccessMessages();
            $messages['info']    = $this->context->getInfoMessages();
            $messages['alert']   = $this->context->getAlertMessages();
            $messages['error']   = $this->context->getErrorMessages();
            $vars['messages']    = $messages;
        }

        return $this->twig->render($templateFilename, $vars);
    }
}