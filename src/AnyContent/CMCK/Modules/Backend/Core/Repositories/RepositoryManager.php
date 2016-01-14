<?php

namespace AnyContent\CMCK\Modules\Backend\Core\Repositories;

use CMDL\Parser;

use AnyContent\Client\Client;
use AnyContent\Client\UserInfo;
use AnyContent\Client\Repository;
use AnyContent\CMCK\Modules\Backend\Core\Context;

class RepositoryManager
{

    /**
     * @var Repository[]
     */
    protected $repositories = [ ];

    protected $repositoryAccessHashes = [ ];

    protected $contentTypeAccessHashes = [ ];

    protected $configTypeAccessHashes = [ ];

    /** @var  UserInfo */
    protected $userInfo;


    public function addRepository($name, Repository $repository, $title = null)
    {
        $repository->setName($name);
        $repository->setTitle($title);

        $userInfo = $repository->getCurrentUserInfo();
        if ($userInfo->getName() == '' && $this->userInfo != null)
        {
            $repository->setUserInfo($this->userInfo);
        }

        $this->repositories[$repository->getName()] = $repository;

        foreach ($repository->getContentTypeNames() as $contentTypeName)
        {
            $this->contentTypeAccessHashes[$this->getContentTypeAccessHash($repository, $contentTypeName)] = [ 'repositoryId' => $repository->getName(), 'contentTypeName' => $contentTypeName ];
        }

        foreach ($repository->getConfigTypeNames() as $configTypeName)
        {
            $this->configTypeAccessHashes[$this->getConfigTypeAccessHash($repository, $configTypeName)] = [ 'repositoryId' => $repository->getName(), 'configTypeName' => $configTypeName ];
        }

        $this->repositoryAccessHashes[$this->getRepositoryAccessHash($repository)] = [ 'repositoryId' => $repository->getName() ];

    }


    public function getRepositoryAccessHash(Repository $repository)
    {
        return md5($repository->getName());
    }


    public function getContentTypeAccessHash(Repository $repository, $contentTypeName)
    {
        return md5($repository->getName() . '-contentType-' . $contentTypeName);
    }


    public function getConfigTypeAccessHash(Repository $repository, $configTypeName)
    {
        return md5($repository->getName() . '-contentType-' . $configTypeName);
    }


    public function getAccessHash($repository, $contentTypeDefinition = null)
    {

        if ($contentTypeDefinition != null)
        {
            return $this->getContentTypeAccessHash($repository, $contentTypeDefinition->getName());
        }
        else
        {
            return $this->getRepositoryAccessHash($repository);
        }

    }


    public function setUserInfo(UserInfo $userInfo)
    {
        $this->userInfo = $userInfo;
        foreach ($this->repositories as $repository)
        {
            $repository->setUserInfo($userInfo);
        }
    }


    public function listRepositories()
    {

        $repositories = array();
        foreach ($this->repositories as $repository)
        {
            $title = $repository->getTitle();
            if ($title == '')
            {
                $title = $repository->getName();
            }
            //$repositories[$repository->getName()] = array( 'title' => $title, 'accessHash' => $this->getRepositoryAccessHash($repository), 'shortcut' => $repository->getShortcut() );
            $repositories[$repository->getName()] = array( 'title' => $title, 'accessHash' => $this->getRepositoryAccessHash($repository) );
        }

        return $repositories;
    }


    public function listContentTypes($id)
    {

        $contentTypes = [ ];

        if (array_key_exists($id, $this->repositories))
        {
            $repository = $this->repositories[$id];

            foreach ($repository->getContentTypeDefinitions() as $contentType)
            {

                $contentTypes[$contentType->getName()] = array( 'name' => $contentType->getName(), 'title' => $contentType->getTitle(), 'accessHash' => $this->getContentTypeAccessHash($repository, $contentType->getName()) );
            }

        }

        return $contentTypes;
    }


    public function listConfigTypes($id)
    {

        $configTypes = [ ];

        if (array_key_exists($id, $this->repositories))
        {
            $repository = $this->repositories[$id];

            foreach ($repository->getConfigTypeDefinitions() as $configType)
            {

                $configTypes[$configType->getName()] = array( 'name' => $configType->getName(), 'title' => $configType->getTitle(), 'accessHash' => $this->getConfigTypeAccessHash($repository, $configType->getName()) );
            }

        }

        return $configTypes;
    }


    public function hasFiles($id)
    {
        if (array_key_exists($id, $this->repositories))
        {
            $repository = $this->repositories[$id];

            return $repository->hasFiles();
        }

        return false;
    }


    public function listApps($id)
    {
        return [ ];
    }


    public function getRepositoryById($id)
    {

        if (array_key_exists($id, $this->repositories))
        {
            return $this->repositories[$id];
        }

        return false;
    }


    public function getRepositoryByRepositoryAccessHash($hash)
    {
        if (array_key_exists($hash, $this->repositoryAccessHashes))
        {
            $id = $this->repositoryAccessHashes[$hash]['repositoryId'];

            return $this->getRepositoryById($id);
        }

        return false;
    }


    public function getRepositoryByContentTypeAccessHash($hash)
    {

        if (array_key_exists($hash, $this->contentTypeAccessHashes))
        {
            $id              = $this->contentTypeAccessHashes[$hash]['repositoryId'];
            $contentTypeName = $this->contentTypeAccessHashes[$hash]['contentTypeName'];
            $repository      = $this->getRepositoryById($id);

            $repository->selectContentType($contentTypeName);

            return $repository;
        }

        return false;
    }


    public function getRepositoryByConfigTypeAccessHash($hash)
    {

        if (array_key_exists($hash, $this->configTypeAccessHashes))
        {
            $id         = $this->configTypeAccessHashes[$hash]['repositoryId'];
            $repository = $this->getRepositoryById($id);

            return $repository;
        }

        return false;
    }


    public function getConfigTypeDefinitionByConfigTypeAccessHash($hash)
    {

        if (array_key_exists($hash, $this->configTypeAccessHashes))
        {
            $id             = $this->configTypeAccessHashes[$hash]['repositoryId'];
            $configTypeName = $this->configTypeAccessHashes[$hash]['configTypeName'];
            $repository     = $this->getRepositoryById($id);

            if ($repository->hasConfigType($configTypeName))
            {
                return $repository->getConfigTypeDefinition($configTypeName);
            }

        }

        return false;
    }


//
//    protected $cache = null;
//
//    /** @var  ContextManager */
//    protected $context;
//
//    protected $requestedRepositories = array();
//
//    protected $repositoryObjects = null;
//
//    protected $contentTypeAccessHashes = null;
//    protected $configTypeAccessHashes = null;
//
//    protected $userInfo = null;
//
//
//    public function __construct($cache, $context, $config)
//    {
//        $this->cache   = $cache;
//        $this->context = $context;
//        $this->config  = $config;
//    }
//
//
//    public function init($config)
//    {
//        /*
//        foreach ($config->getRepositoriesConfiguration() as $repository)
//        {
//            $this->addAllContentTypesOfRepository($repository['url'], null, null, 'Basic', $repository['shortcut'], null);
//            $this->addAllConfigTypesOfRepository($repository['url']);
//
//            foreach ($config->getAppsConfiguration($repository['shortcut']) as $app)
//            {
//                if (array_key_exists('url', $app))
//                {
//                    $name = 'Content App';
//                    if (array_key_exists('name', $app))
//                    {
//                        $name = $app['name'];
//                        unset($app['name']);
//                    }
//                    $this->addAppToRepository($repository['url'], $name, $app);
//                }
//            }
//        } */
//    }
//
//
//
//
//    public function addAllContentTypesOfRepository($repositoryUrl, $apiUser = null, $apiPassword = null, $authType = 'Basic', $shortcut = null, $repositoryTitle = null)
//    {
//
//        if (array_key_exists($repositoryUrl, $this->requestedRepositories))
//        {
//            $repositoryInfo = $this->requestedRepositories[$repositoryUrl];
//        }
//        else
//        {
//            $repositoryInfo = $this->addRepositoryInfo($repositoryUrl, $apiUser, $apiPassword, $authType, $shortcut, $repositoryTitle);
//        }
//
//        $repositoryInfo['contentTypes'] = array( '*' => '*' );
//
//        $this->requestedRepositories[$repositoryUrl] = $repositoryInfo;
//
//    }
//
//
//    public function addAllConfigTypesOfRepository($repositoryUrl, $apiUser = null, $apiPassword = null, $authType = 'Basic', $repositoryTitle = null)
//    {
//        if (array_key_exists($repositoryUrl, $this->requestedRepositories))
//        {
//            $repositoryInfo = $this->requestedRepositories[$repositoryUrl];
//        }
//        else
//        {
//            $repositoryInfo = $this->addRepositoryInfo($repositoryUrl, $apiUser, $apiPassword, $authType, null, $repositoryTitle);
//
//        }
//        $repositoryInfo['configTypes'] = array( '*' => '*' );
//
//        $this->requestedRepositories[$repositoryUrl] = $repositoryInfo;
//
//    }
//
//
//    public function addOneContentType($contentTypeName, $repositoryUrl, $apiUser = null, $apiPassword = null, $authType = 'Basic', $repositoryTitle = null, $shortcut = null, $contentTypeTitle = null)
//    {
//        if (array_key_exists($repositoryUrl, $this->requestedRepositories))
//        {
//            $repositoryInfo = $this->requestedRepositories[$repositoryUrl];
//        }
//        else
//        {
//            $repositoryInfo = $this->addRepositoryInfo($repositoryUrl, $apiUser, $apiPassword, $authType, $shortcut, $repositoryTitle);
//        }
//
//        $repositoryInfo['contentTypes'][$contentTypeName] = $contentTypeTitle;
//
//        $this->requestedRepositories[$repositoryUrl] = $repositoryInfo;
//
//    }
//
//
//    public function addOneConfigType($configTypeName, $repositoryUrl, $apiUser = null, $apiPassword = null, $authType = 'Basic', $repositoryTitle = null, $shortcut = null, $configTypeTitle = null)
//    {
//        if (array_key_exists($repositoryUrl, $this->requestedRepositories))
//        {
//            $repositoryInfo = $this->requestedRepositories[$repositoryUrl];
//        }
//        else
//        {
//            $repositoryInfo = $this->addRepositoryInfo($repositoryUrl, $apiUser, $apiPassword, $authType, null, $repositoryTitle);
//
//        }
//
//        $repositoryInfo['configTypes'][$configTypeName] = $configTypeTitle;
//
//        $this->requestedRepositories[$repositoryUrl] = $repositoryInfo;
//
//    }
//
//
//    protected function addRepositoryInfo($repositoryUrl, $apiUser = null, $apiPassword = null, $authType = 'Basic', $shortcut = null, $repositoryTitle = null)
//    {
//        $repositoryInfo                = array();
//        $repositoryInfo['url']         = $repositoryUrl;
//        $repositoryInfo['apiUser']     = $apiUser;
//        $repositoryInfo['apiPassword'] = $apiPassword;
//        $repositoryInfo['authType']    = $authType;
//        $repositoryInfo['shortcut']    = $shortcut;
//        $repositoryInfo['title']       = @array_pop(explode('/', $repositoryUrl));
//
//        if ($repositoryTitle != null)
//        {
//            $repositoryInfo['title'] = $repositoryTitle;
//        }
//        $repositoryInfo['contentTypes'] = array();
//        $repositoryInfo['configTypes']  = array();
//        $repositoryInfo['apps']         = array();
//
//        return $repositoryInfo;
//
//    }
//
//
//    public function addAppToRepository($repositoryUrl, $name, $settings = array())
//    {
//        if (array_key_exists($repositoryUrl, $this->requestedRepositories))
//        {
//            $repositoryInfo = $this->requestedRepositories[$repositoryUrl];
//
//            $repositoryInfo['apps'][$name]               = $settings;
//            $this->requestedRepositories[$repositoryUrl] = $repositoryInfo;
//        }
//
//    }
//
//
//    public function setUserInfo(UserInfo $userInfo)
//    {
//        $this->userInfo = $userInfo;
//    }
//
//
//    public function listRepositories()
//    {
//
//        $repositories = array();
//        foreach ($this->requestedRepositories as $repositoryInfo)
//        {
//
//            $hash                                 = md5($repositoryInfo['url']);
//            $repositories[$repositoryInfo['url']] = array( 'title' => $repositoryInfo['title'], 'accessHash' => $hash, 'shortcut' => $repositoryInfo['shortcut'] );
//        }
//
//        return $repositories;
//    }
//
//
//    public function listContentTypes($url)
//    {
//
//        if (!$this->repositoryObjects)
//        {
//            $this->initRepositoryObjects();
//        }
//
//        if (array_key_exists($url, $this->requestedRepositories))
//        {
//            $repositoryInfo = $this->requestedRepositories[$url];
//
//            $contentTypes = array();
//
//            if (array_key_exists($repositoryInfo['url'], $this->repositoryObjects))
//            {
//                $repository = $this->repositoryObjects[$repositoryInfo['url']];
//
//                foreach ($repository->getContentTypes() as $contentTypeName => $contentTypeTitle)
//                {
//                    if (array_key_exists('*', $repositoryInfo['contentTypes']) OR array_key_exists($contentTypeName, $repositoryInfo['contentTypes']))
//                    {
//                        $hash                           = md5($url . '-contentType-' . $contentTypeName);
//                        $contentTypes[$contentTypeName] = array( 'name'=>$contentTypeName, 'title' => $contentTypeTitle, 'accessHash' => $hash );
//                    }
//                }
//            }
//
//            return $contentTypes;
//        }
//
//        return false;
//    }
//
//
//    public function listConfigTypes($url)
//    {
//        if (!$this->repositoryObjects)
//        {
//            $this->initRepositoryObjects();
//        }
//
//        if (array_key_exists($url, $this->requestedRepositories))
//        {
//            $repositoryInfo = $this->requestedRepositories[$url];
//
//            $configTypes = array();
//
//            if (array_key_exists($repositoryInfo['url'], $this->repositoryObjects))
//            {
//                $repository = $this->repositoryObjects[$repositoryInfo['url']];
//
//                foreach ($repository->getConfigTypeDefinitions() as $configTypeName => $configTypeTitle)
//                {
//                    if (array_key_exists('*', $repositoryInfo['configTypes']) OR array_key_exists($configTypeName, $repositoryInfo['configTypes']))
//                    {
//                        $hash                         = md5($url . '-configType-' . $configTypeName);
//                        $configTypes[$configTypeName] = array( 'name'=>$configTypeName, 'title' => $configTypeTitle, 'accessHash' => $hash );
//                    }
//                }
//            }
//
//            return $configTypes;
//        }
//
//        return false;
//    }
//
//
//    public function listApps($url)
//    {
//        if (!$this->repositoryObjects)
//        {
//            $this->initRepositoryObjects();
//        }
//
//        if (array_key_exists($url, $this->requestedRepositories))
//        {
//            $repositoryInfo = $this->requestedRepositories[$url];
//
//            return $repositoryInfo['apps'];
//
//        }
//
//        return false;
//
//    }
//
//

//
//
//    /**
//     * @param $hash
//     *
//     * @return bool|Repository
//     */
//    public function getRepositoryByContentTypeAccessHash($hash)
//    {
//        if (!$this->contentTypeAccessHashes)
//        {
//            $this->initAccessHashes();
//        }
//
//        if (array_key_exists($hash, $this->contentTypeAccessHashes))
//        {
//            $repository = $this->contentTypeAccessHashes[$hash]['repository'];
//
//            $repository->selectContentType($this->contentTypeAccessHashes[$hash]['contentTypeName']);
//
//            return $repository;
//        }
//
//        return false;
//    }
//
//
//    /**
//     * @param $shortcut
//     *
//     * @return Repository|bool
//     */
//    public function getRepositoryByContentTypeShortcut($shortcut)
//    {
//        $tokens = explode('.', $shortcut);
//
//        if (count($tokens) != 2)
//        {
//            return false;
//        }
//        $repository = $this->getRepositoryByShortcut($tokens[0]);
//
//        if ($repository)
//        {
//            if ($repository->hasContentType($tokens[1]))
//            {
//                $repository->selectContentType($tokens[1]);
//
//                return $repository;
//            }
//
//        }
//
//        return false;
//    }
//
//
//    /**
//     * @param $hash
//     *
//     * @return bool|Repository
//     */
//    public function getRepositoryByConfigTypeAccessHash($hash)
//    {
//        if (!$this->configTypeAccessHashes)
//        {
//            $this->initAccessHashes();
//        }
//
//        if (array_key_exists($hash, $this->configTypeAccessHashes))
//        {
//            $repository = $this->configTypeAccessHashes[$hash]['repository'];
//
//            if ($repository->hasConfigType($this->configTypeAccessHashes[$hash]['configTypeName']))
//            {
//                $repository->selectConfigType($this->configTypeAccessHashes[$hash]['configTypeName']);
//
//                return $repository;
//            }
//        }
//
//        return false;
//    }
//
//
//    /**
//     * @param $shortcut
//     *
//     * @return Repository|bool
//     */
//    public function getRepositoryByConfigTypeShortcut($shortcut)
//    {
//        $tokens = explode('.', $shortcut);
//
//        if (count($tokens) != 3 OR $tokens[1] != 'config')
//        {
//            return false;
//        }
//        $repository = $this->getRepositoryByShortcut($tokens[0]);
//
//        if ($repository)
//        {
//            if ($repository->hasConfigType($tokens[2]))
//            {
//                return $repository;
//            }
//
//        }
//
//        return false;
//    }
//
//
//    public function getConfigTypeDefinitionByConfigTypeAccessHash($hash)
//    {
//        if (!$this->configTypeAccessHashes)
//        {
//            $this->initAccessHashes();
//        }
//
//        if (array_key_exists($hash, $this->configTypeAccessHashes))
//        {
//            /** @var Repository $repository */
//            $repository = $this->configTypeAccessHashes[$hash]['repository'];
//
//            return $repository->getConfigTypeDefinition($this->configTypeAccessHashes[$hash]['configTypeName']);
//        }
//
//        return false;
//    }
//
//
//    /**
//     * @param $hash
//     *
//     * @return bool|Repository
//     */
//    public function getRepositoryByRepositoryAccessHash($hash)
//    {
//        if (!$this->repositoryObjects)
//        {
//            $this->initRepositoryObjects();
//        }
//
//        foreach ($this->listRepositories() AS $url => $item)
//        {
//            if ($item['accessHash'] == $hash)
//            {
//                if (array_key_exists($url, $this->repositoryObjects))
//                {
//                    return $this->repositoryObjects[$url];
//                }
//            }
//        }
//
//        return false;
//    }
//
//
//    /**
//     * @param $shortcut
//     *
//     * @return bool|Repository
//     */
//    public function getRepositoryByShortcut($shortcut)
//    {
//        if (!$this->repositoryObjects)
//        {
//            $this->initRepositoryObjects();
//        }
//
//        foreach ($this->listRepositories() AS $url => $item)
//        {
//            if ($item['shortcut'] == $shortcut)
//            {
//                if (array_key_exists($url, $this->repositoryObjects))
//                {
//                    return $this->repositoryObjects[$url];
//                }
//            }
//        }
//
//        return false;
//    }
//
//
//    public function getRepositoryAccessHashByUrl($repositoryUrl)
//    {
//        return md5($repositoryUrl);
//    }
//
//
//    protected function initAccessHashes()
//    {
//        if (!$this->repositoryObjects)
//        {
//            $this->initRepositoryObjects();
//        }
//
//        $this->contentTypeAccessHashes = array();
//        $this->configTypeAccessHashes  = array();
//
//        foreach ($this->requestedRepositories as $repositoryInfo)
//        {
//            $repository = $this->repositoryObjects[$repositoryInfo['url']];
//
//            foreach ($this->listContentTypes($repositoryInfo['url']) as $contentTypName => $contentTypeItem)
//            {
//                $this->contentTypeAccessHashes[$contentTypeItem['accessHash']] = array( 'contentTypeName' => $contentTypName, 'repository' => $repository );
//            }
//            foreach ($this->listConfigTypes($repositoryInfo['url']) as $configTypeName => $configTypeItem)
//            {
//                $this->configTypeAccessHashes[$configTypeItem['accessHash']] = array( 'configTypeName' => $configTypeName, 'repository' => $repository );
//            }
//        }
//
//    }
//
//
//    protected function initRepositoryObjects()
//    {
//        $this->repositoryObjects = array();
//        foreach ($this->requestedRepositories as $repositoryInfo)
//        {
//            try
//            {
//                $cacheConfiguration = $this->config->getCacheConfiguration();
//
//                $client = new Client($repositoryInfo['url'], $repositoryInfo['apiUser'], $repositoryInfo['apiPassword'], $repositoryInfo['authType'], $this->cache, $cacheConfiguration['seconds_caching_api_responses'], $cacheConfiguration['seconds_ignoring_eventually_concurrent_writes'], $cacheConfiguration['seconds_ignoring_eventually_concurrent_file_updates']);
//                if ($this->userInfo)
//                {
//                    $client->setUserInfo($this->userInfo);
//                }
//
//                $repository = $client->getRepository();
//
//                $this->repositoryObjects[$repositoryInfo['url']] = $repository;
//            }
//            catch (\Exception $e)
//            {
//                $this->context->addErrorMessage('Could not connect to repository ' . $repositoryInfo['url'] . '.');
//            }
//
//        }
//
//    }
//
//
//
}