<?php

namespace AnyContent\CMCK\Modules\Backend\Core\Repositories;

use AnyContent\Client\Repository;

class RepositoryWrapper extends Repository
{

    public function setWorkspace($workspace)
    {
        return $this->selectWorkspace($workspace);
    }

    public function setLanguage($language)
    {
        return $this->selectWorkspace($language);
    }

    public function setViewName($viewName)
    {
        return $this->selectView($viewName);
    }

    public function setOrder($order)
    {
        return $this;
    }

    public function getRepositoryName()
    {
        return $this->getId();
    }

    public function stashDimensions()
    {
        //$this->stash = array( 'workspace' => $this->workspace, 'viewName' => $this->viewName, 'language' => $this->language, 'timeshift' => $this->timeshift, 'order' => $this->order );

        return $this;
    }


    public function unStashDimensions()
    {
        /*
        $this->workspace = $this->stash['workspace'];
        $this->language  = $this->stash['language'];
        $this->timeshift = $this->stash['timeshift'];
        $this->viewName  = $this->stash['viewName'];
        $this->order     = $this->stash['order'];*/

        return $this;
    }


    /**
     * @param null $filter
     * @param null $limit
     * @param int  $page
     *
     * @return array|bool
     * @throws AnyContentClientException
     */
    public function getRecordsAsIDNameList($filter = null, $limit = null, $page = 1)
    {

        $records = $this->getReadConnection()->getAllRecords();
        $list = [];
        foreach ($records as $record)
        {
            $list[$record->getID()]=$record->getName();
        }
        return $list;

    }


}