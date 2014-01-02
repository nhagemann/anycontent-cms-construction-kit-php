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
        if (!$this->session->has($this->prefix . 'sorting'))
        {
            $this->session->set($this->prefix . 'sorting', array());
        }
        if (!$this->session->has($this->prefix . 'listing_page'))
        {
            $this->session->set($this->prefix . 'listing_page', array());
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


    public function setCurrentSortingOrder($order, $switch = true)
    {
        $options = array( 'id', 'subtype', 'name', 'change', 'status', 'pos' );
        if (in_array($order, $options))
        {

            if ($switch == true)
            {
                if ($this->getCurrentSortingOrder() == $order)
                {
                    $order = $order . '-';

                }
                if ($this->getCurrentSortingOrder() == $order . '-')
                {
                    $order = trim($order, '-');
                }
            }
        }
        else
        {
            $order = 'id';
        }

        $sorting                                        = $this->session->get($this->prefix . 'sorting');
        $sorting[$this->contentTypeDefinion->getName()] = $order;
        $this->session->set($this->prefix . 'sorting', $sorting);
    }


    public function getCurrentSortingOrder()
    {
        if ($this->session->has($this->prefix . 'sorting'))
        {
            $sorting = $this->session->get($this->prefix . 'sorting');
            if (array_key_exists($this->contentTypeDefinion->getName(), $sorting))
            {
                return $sorting[$this->contentTypeDefinion->getName()];
            }
        }

        return 'id';
    }


    public function setCurrentListingPage($page)
    {
        $listing                                        = $this->session->get($this->prefix . 'listing_page');
        $listing[$this->contentTypeDefinion->getName()] = $page;
        $this->session->set($this->prefix . 'listing_page', $listing);
    }


    public function getCurrentListingPage()
    {
        if ($this->session->has($this->prefix . 'listing_page'))
        {
            $listing = $this->session->get($this->prefix . 'listing_page');
            if (array_key_exists($this->contentTypeDefinion->getName(), $listing))
            {
                return $listing[$this->contentTypeDefinion->getName()];
            }
        }

        return '1';
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