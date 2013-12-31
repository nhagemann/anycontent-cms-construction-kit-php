<?php

namespace Anycontent\CMCK\Modules\Core\Context;

class ContextManager
{

    protected $session;

    protected $prefix = 'context_';


    public function __construct($session)
    {
        $this->session = $session;
    }


    public function setCurrentContentType($contentTypeDefinition)
    {

    }


    public function getCurrentWorkspace()
    {
        return 'default';
    }


    public function getCurrentLanguage()
    {
        return 'null';
    }


    public function getCurrentTimeShift()
    {
        return 0;
    }


    public function getCurrentSaveOperation()
    {
        if ($this->session->has($this->prefix . 'save_operation'))
        {
            return array( $this->session->get($this->prefix . 'save_operation') => $this->session->get($this->prefix . 'save_operation_title') );
        }

        return array( 'save' => 'Save' );
    }


    public function setCurrentSaveOperation($operation, $title)
    {

        $this->session->set($this->prefix . 'save_operation', $operation);
        $this->session->set($this->prefix . 'save_operation_title', $title);
    }


    public function resetTimeShift()
    {

    }
}