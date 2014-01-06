<?php

namespace Anycontent\CMCK\Modules\Core\Context;

use CMDL\ConfigTypeDefinition;
use CMDL\ContentTypeDefinition;

class ContextManager
{

    protected $session;

    protected $contentTypeDefinion = null;

    protected $prefix = 'context_';

    protected $context = null;


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
        if (!$this->session->has($this->prefix . 'searchterms'))
        {
            $this->session->set($this->prefix . 'searchterms', array());
        }
        if (!$this->session->has($this->prefix . 'listing_page'))
        {
            $this->session->set($this->prefix . 'listing_page', array());
        }
        if (!$this->session->has($this->prefix . 'timeshift'))
        {
            $this->session->set($this->prefix . 'timeshift', 0);
        }
        if (!$this->session->has($this->prefix . 'workspace'))
        {
            $this->session->set($this->prefix . 'workspace', 'default');
        }
        if (!$this->session->has($this->prefix . 'language'))
        {
            $this->session->set($this->prefix . 'language', 'default');
        }
    }


    public function setCurrentContentType(ContentTypeDefinition $contentTypeDefinition)
    {
        $this->contentTypeDefinion = $contentTypeDefinition;
        $this->context             = 'content';

        $contentType = $contentTypeDefinition->getTitle();
        if (!$contentType)
        {
            $contentType = $contentTypeDefinition->getName();
        }
        // check workspaces

        $workspaces = $contentTypeDefinition->getWorkspaces();

        if (!array_key_exists($this->getCurrentWorkspace(), $workspaces))
        {
            reset($workspaces);
            list($key, $workspace) = each($workspaces);

            $this->setCurrentWorkspace($key);
            $this->addInfoMessage('Switching to workspace ' . $workspace . ' (' . $key . ') for content type ' . $contentType . '.');
        }

        if ($contentTypeDefinition->hasLanguages())
        {
            $languages = $contentTypeDefinition->getLanguages();
        }
        else
        {
            $languages = array( 'default' => 'None' );
        }

        if (!array_key_exists($this->getCurrentLanguage(), $languages))
        {
            reset($languages);
            list($key, $language) = each($languages);

            $this->setCurrentLanguage($key);
            $this->addInfoMessage('Switching to language ' . $language . ' (' . $key . ') for content type ' . $contentType . '.');
        }

        if (!$contentTypeDefinition->isTimeShiftable() AND $this->getCurrentTimeShift() != 0)
        {
            $this->resetTimeShift();
        }
    }


    public function setCurrentConfigType(ConfigTypeDefinition $configTypeDefinition)
    {
        $this->context = 'config';
    }


    public function setFilesContext()
    {
        $this->context = 'files';
    }


    public function isContentContext()
    {
        if ($this->context == 'content')
        {
            return true;
        }

        return false;
    }


    public function isConfigContext()
    {
        if ($this->context == 'config')
        {
            return true;
        }

        return false;
    }


    public function isFilesContext()
    {
        if ($this->context == 'files')
        {
            return true;
        }

        return false;
    }


    /**
     * @return ContentTypeDefinition
     */
    public function getCurrentContentType()
    {
        return $this->contentTypeDefinion;
    }


    public function setCurrentWorkspace($workspace)
    {
        return $this->session->set($this->prefix . 'workspace', $workspace);
    }


    public function setCurrentLanguage($language)
    {
        return $this->session->set($this->prefix . 'language', $language);
    }


    public function getCurrentWorkspace()
    {
        return $this->session->get($this->prefix . 'workspace');
    }


    public function getCurrentLanguage()
    {
        return $this->session->get($this->prefix . 'language');
    }


    public function getCurrentTimeShift()
    {
        return $this->session->get($this->prefix . 'timeshift');
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


    public function setCurrentTimeShift($timestamp)
    {
        $date = New \DateTime();
        if ($timestamp > $date->getTimestamp())
        {
            $this->addErrorMessage('Cannot time shift into the future! - "Jesus, George, it was a wonder I was even born." (Marty McFly)');
        }
        else
        {
            $this->session->set($this->prefix . 'timeshift', $timestamp);
        }
    }


    public function resetTimeShift()
    {
        if ($this->getCurrentTimeShift() != 0)
        {
            if ($this->isContentContext() AND $this->getCurrentContentType()->isTimeShiftable() == false)
            {
                $contentType = $this->getCurrentContentType()->getTitle();
                if (!$contentType)
                {
                    $contentType = $this->getCurrentContentType()->getName();
                }

                $this->addInfoMessage('Content type ' . $contentType . ' doesn\'t support time shifting. Switching back to real time.');
            }
            else
            {
                $this->addInfoMessage('Switching back to real time.');
            }
        }
        $this->session->set($this->prefix . 'timeshift', 0);
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


    public function setCurrentSearchTerm($searchTerm)
    {
        $searchTerms                                        = $this->session->get($this->prefix . 'searchterms');
        $searchTerms[$this->contentTypeDefinion->getName()] = $searchTerm;
        $this->session->set($this->prefix . 'searchterms', $searchTerms);
    }


    public function getCurrentSearchTerm()
    {
        if ($this->session->has($this->prefix . 'searchterms'))
        {
            $searchTerms = $this->session->get($this->prefix . 'searchterms');
            if (array_key_exists($this->contentTypeDefinion->getName(), $searchTerms))
            {
                return $searchTerms[$this->contentTypeDefinion->getName()];
            }
        }

        return '';
    }


    public function getCurrentItemsPerPage()
    {
        return 5;
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


    public function canDoTimeshift()
    {
        if ($this->isContentContext())
        {
            if ($this->getCurrentContentType())
            {
                return $this->getCurrentContentType()->isTimeShiftable();
            }
        }

        return false;
    }


    public function canDoSearch()
    {
        if ($this->isContentContext())
        {
            if ($this->getCurrentContentType())
            {
                return $this->getCurrentContentType()->hasListOperation();
            }
        }

        return false;
    }


    public function canChangeWorkspace()
    {
        if ($this->isContentContext())
        {
            return $this->getCurrentContentType()->hasWorkspaces();
        }

        return false;
    }


    public function canChangeLanguage()
    {
        if ($this->isContentContext())
        {
            return $this->getCurrentContentType()->hasLanguages();
        }

        return false;
    }
}