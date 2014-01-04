<?php

namespace Anycontent\CMCK\Modules\Core\Repositories;

use CMDL\Parser;
use CMDL\ContentTypeDefinition;

use AnyContent\Client\Client;
use AnyContent\Client\UserInfo;

class RepositoryManager
{

    protected $cache = null;

    protected $repositoryInfos = array();

    protected $repositoryObjects = null;

    protected $accessHashes = null;

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

        $this->repositoryInfos[$url] = $repository;

    }


    public function addOneContentType($contentTypeName, $url, $apiUser = null, $apiPassword = null, $authType = 'Basic', $repositoryTitle = null, $contentTypeTitle = null)
    {
        if (array_key_exists($url, $this->repositoryInfos))
        {
            $repositoryInfo = $this->repositoryInfos[$url];
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

        $this->repositoryInfos[$url] = $repositoryInfo;

    }


    public function setUserInfo(UserInfo $userInfo)
    {
        $this->userInfo = $userInfo;
    }


    public function listRepositories()
    {

        $repositories = array();
        foreach ($this->repositoryInfos as $repositoryInfo)
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

        if (array_key_exists($url, $this->repositoryInfos))
        {
            $repositoryInfo = $this->repositoryInfos[$url];

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


    public function getRepositoryContentAccessByHash($hash)
    {
        if (!$this->accessHashes)
        {
            $this->initAccessHashes();
        }

        if (array_key_exists($hash, $this->accessHashes))
        {
            $repository = $this->accessHashes[$hash]['repository'];
            $repository->selectContentType($this->accessHashes[$hash]['contentTypeName']);

            return $repository;
        }

        return false;
    }


    protected function initRepositoryObjects()
    {
        $this->repositoryObjects = array();
        foreach ($this->repositoryInfos as $repositoryInfo)
        {
            $client = new Client($repositoryInfo['url'], $repositoryInfo['apiUser'], $repositoryInfo['apiPassword'], $repositoryInfo['authType'], $this->cache);

            if ($this->userInfo)
            {
                $client->setUserInfo($this->userInfo);
            }

            $repository                                      = $client->getRepository();
            $this->repositoryObjects[$repositoryInfo['url']] = $repository;
        }
    }


    protected function initAccessHashes()
    {
        if (!$this->repositoryObjects)
        {
            $this->initRepositoryObjects();
        }

        $this->accessHashes = array();
        foreach ($this->repositoryInfos as $repositoryInfo)
        {
            $repository = $this->repositoryObjects[$repositoryInfo['url']];

            foreach ($this->listContentTypes($repositoryInfo['url']) as $contentTypName => $contentTypeItem)
            {
                $this->accessHashes[$contentTypeItem['accessHash']] = array( 'contentTypeName' => $contentTypName, 'repository' => $repository );
            }
        }

    }

}