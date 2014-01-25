<?php

namespace Anycontent\CMCK\Modules\Backend\Core\Repositories;

use CMDL\Parser;

use AnyContent\Client\Client;
use AnyContent\Client\UserInfo;

class RepositoryManager
{

    protected $cache = null;

    protected $requestedRepositories = array();

    protected $repositoryObjects = null;

    protected $contentTypeAccessHashes = null;

    protected $userInfo = null;

    public function __construct($cache)
    {
        $this->cache = $cache;
    }


    public function addAllContentTypesOfRepository($url, $apiUser = null, $apiPassword = null, $authType = 'Basic', $repositoryTitle = null)
    {

        $repository                = array();
        $repository['url']         = $url;
        $repository['apiUser']     = $apiUser;
        $repository['apiPassword'] = $apiPassword;
        $repository['authType']    = $authType;
        $repository['title']       = $url;
        if ($repositoryTitle)
        {
            $repository['title'] = $repositoryTitle;
        }
        $repository['contentTypes'] = array( '*' => '*' );

        $this->requestedRepositories[$url] = $repository;

    }


    public function addOneContentType($contentTypeName, $url, $apiUser = null, $apiPassword = null, $authType = 'Basic', $repositoryTitle = null, $contentTypeTitle = null)
    {
        if (array_key_exists($url, $this->requestedRepositories))
        {
            $repositoryInfo = $this->requestedRepositories[$url];
        }
        else
        {
            $repositoryInfo                = array();
            $repositoryInfo['url']         = $url;
            $repositoryInfo['apiUser']     = $apiUser;
            $repositoryInfo['apiPassword'] = $apiPassword;
            $repositoryInfo['authType']    = $authType;
            $repositoryInfo['title']       = $url;
            if ($repositoryTitle)
            {
                $repositoryInfo['title'] = $repositoryTitle;
            }
            $repositoryInfo['contentTypes'] = array();
        }

        $repositoryInfo['contentTypes'][$contentTypeName] = $contentTypeTitle;

        $this->requestedRepositories[$url] = $repositoryInfo;

    }


    public function setUserInfo(UserInfo $userInfo)
    {
        $this->userInfo = $userInfo;
    }


    public function listRepositories()
    {

        $repositories = array();
        foreach ($this->requestedRepositories as $repositoryInfo)
        {
            $hash                                 = md5($repositoryInfo['url']);
            $repositories[$repositoryInfo['url']] = array( 'title' => $repositoryInfo['title'], 'accessHash' => $hash );
        }

        return $repositories;
    }


    public function listContentTypes($url)
    {

        if (!$this->repositoryObjects)
        {
            $this->initRepositoryObjects();
        }

        if (array_key_exists($url, $this->requestedRepositories))
        {
            $repositoryInfo = $this->requestedRepositories[$url];

            $contentTypes = array();

            $repository = $this->repositoryObjects[$repositoryInfo['url']];

            foreach ($repository->getContentTypes() as $contentTypeName => $contentTypeTitle)
            {
                if (array_key_exists('*', $repositoryInfo['contentTypes']) OR array_key_exists($contentTypeName, $repositoryInfo['contentTypes']))
                {
                    if (!$contentTypeTitle)
                    {
                        $contentTypeTitle = $contentTypeName;
                    }
                    $hash                           = md5($url . '-' . $contentTypeName);
                    $contentTypes[$contentTypeName] = array( 'title' => $contentTypeTitle, 'accessHash' => $hash );
                }
            }

            return $contentTypes;
        }

        return false;
    }


    public function hasFiles($url)
    {
        return true;
    }


    public function getRepositoryByContentTypeAccessHash($hash)
    {
        if (!$this->contentTypeAccessHashes)
        {
            $this->initAccessHashes();
        }

        if (array_key_exists($hash, $this->contentTypeAccessHashes))
        {
            $repository = $this->contentTypeAccessHashes[$hash]['repository'];
            $repository->selectContentType($this->contentTypeAccessHashes[$hash]['contentTypeName']);

            return $repository;
        }

        return false;
    }


    public function getRepositoryByRepositoryAccessHash($hash)
    {
        if (!$this->repositoryObjects)
        {
            $this->initRepositoryObjects();
        }

        foreach ($this->listRepositories() AS $url => $item)
        {
            if ($item['accessHash'] == $hash)
            {
                if (array_key_exists($url, $this->repositoryObjects))
                {
                    return $this->repositoryObjects[$url];
                }
            }
        }

        return false;
    }


    protected function initAccessHashes()
    {
        if (!$this->repositoryObjects)
        {
            $this->initRepositoryObjects();
        }

        $this->contentTypeAccessHashes = array();
        foreach ($this->requestedRepositories as $repositoryInfo)
        {
            $repository = $this->repositoryObjects[$repositoryInfo['url']];

            foreach ($this->listContentTypes($repositoryInfo['url']) as $contentTypName => $contentTypeItem)
            {
                $this->contentTypeAccessHashes[$contentTypeItem['accessHash']] = array( 'contentTypeName' => $contentTypName, 'repository' => $repository );
            }
        }

    }


    protected function initRepositoryObjects()
    {
        $this->repositoryObjects = array();
        foreach ($this->requestedRepositories as $repositoryInfo)
        {

            $client = new Client($repositoryInfo['url'], $repositoryInfo['apiUser'], $repositoryInfo['apiPassword'], $repositoryInfo['authType'], $this->cache);

            if ($this->userInfo)
            {
                $client->setUserInfo($this->userInfo);
            }

            $repository = $client->getRepository();

            $this->repositoryObjects[$repositoryInfo['url']] = $repository;
        }

    }


    public function getAccessHash($repository, $contentTypeDefinition = null)
    {
        foreach ($this->repositoryObjects as $repositoryUrl => $repositoryObject)
        {
            if ($repository == $repositoryObject)
            {
                return md5($repositoryUrl . '-' . $contentTypeDefinition->getName());
            }
        }

        return false;
    }

}