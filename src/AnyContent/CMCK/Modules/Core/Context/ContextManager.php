<?php

namespace Anycontent\CMCK\Modules\Core\Context;

class ContextManager
{

    protected $session;

    protected $contentTypeDefinion = null;

    protected $prefix = 'context_';


    public function __construct($session)
    {
        $this->session = $session;
        if (!$this->session->has($this->prefix . 'messages'))
        {
            $this->session->set($this->prefix . 'messages', array( 'success' => array(), 'info' => array(), 'alert' => array(), 'error' => array() ));
        }
    }


    public function setCurrentContentType($contentTypeDefinition)
    {
        $this->contentTypeDefinion = $contentTypeDefinition;
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


    public function addSuccessMessage($message, $errorCode = null)
    {
        $messages              = $this->session->get($this->prefix . 'messages');
        $messages['success'][] = array( 'errorCode' => $errorCode, 'message' => $message );
        $this->session->set($this->prefix . 'messages', $messages);
    }


    public function addInfoMessage($message, $errorCode = null)
    {
        $messages           = $this->session->get($this->prefix . 'messages');
        $messages['info'][] = array( 'errorCode' => $errorCode, 'message' => $message );
        $this->session->set($this->prefix . 'messages', $messages);
    }


    public function addAlertMessage($message, $errorCode = null)
    {
        $messages            = $this->session->get($this->prefix . 'messages');
        $messages['alert'][] = array( 'errorCode' => $errorCode, 'message' => $message );
        $this->session->set($this->prefix . 'messages', $messages);
    }


    public function addErrorMessage($message, $errorCode = null)
    {
        $messages            = $this->session->get($this->prefix . 'messages');
        $messages['error'][] = array( 'errorCode' => $errorCode, 'message' => $message );
        $this->session->set($this->prefix . 'messages', $messages);
    }


    public function getSuccessMessages()
    {
        $messages            = $this->session->get($this->prefix . 'messages');
        $result              = $messages['success'];
        $messages['success'] = array();
        $this->session->set($this->prefix . 'messages', $messages);

        return $result;
    }


    public function getInfoMessages()
    {
        $messages         = $this->session->get($this->prefix . 'messages');
        $result           = $messages['info'];
        $messages['info'] = array();
        $this->session->set($this->prefix . 'messages', $messages);

        return $result;
    }


    public function getAlertMessages()
    {
        $messages          = $this->session->get($this->prefix . 'messages');
        $result            = $messages['alert'];
        $messages['alert'] = array();
        $this->session->set($this->prefix . 'messages', $messages);

        return $result;
    }


    public function getErrorMessages()
    {
        $messages          = $this->session->get($this->prefix . 'messages');
        $result            = $messages['error'];
        $messages['error'] = array();
        $this->session->set($this->prefix . 'messages', $messages);

        return $result;
    }
}