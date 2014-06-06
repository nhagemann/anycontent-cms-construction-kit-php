<?php

namespace AnyContent\CMCK\Modules\Backend\Core\Repositories;

use CMDL\Parser;

use AnyContent\Client\Client;
use AnyContent\Client\UserInfo;
use AnyContent\Client\Repository;
use AnyContent\CMCK\Modules\Backend\Core\Context;

class RepositoryManager
{

    protected $cache = null;

    /** @var  ContextManager */
    protected $context;

    protected $requestedRepositories = array();

    protected $repositoryObjects = null;

    protected $contentTypeAccessHashes = null;
    protected $configTypeAccessHashes = null;

    protected $userInfo = null;


    public function __construct($cache, $context, $config)
    {
        $this->cache   = $cache;
        $this->context = $context;
        $this->config  = $config;
    }


    public function init($config)
    {
        foreach ($config->getRepositoriesConfiguration() as $repository)
        {
            $this->addAllContentTypesOfRepository($repository['url'], null, null, 'Basic', $repository['shortcut'], null);
            $this->addAllConfigTypesOfRepository($repository['url']);

            foreach ($config->getAppsConfiguration($repository['shortcut']) as $app)
            {
                if (array_key_exists('url', $app))
                {
                    $name = 'Content App';
                    if (array_key_exists('name', $app))
                    {
                        $name = $app['name'];
                        unset($app['name']);
                    }
                    $this->addAppToRepository($repository['url'], $name, $app);
                }
            }
        }
    }


    public function addAllContentTypesOfRepository($repositoryUrl, $apiUser = null, $apiPassword = null, $authType = 'Basic', $shortcut = null, $repositoryTitle = null)
    {

        if (array_key_exists($repositoryUrl, $this->requestedRepositories))
        {
            $repositoryInfo = $this->requestedRepositories[$repositoryUrl];
        }
        else
        {
            $repositoryInfo                = array();
            $repositoryInfo['url']         = $repositoryUrl;
            $repositoryInfo['apiUser']     = $apiUser;
            $repositoryInfo['apiPassword'] = $apiPassword;
            $repositoryInfo['authType']    = $authType;
            $repositoryInfo['shortcut']    = $shortcut;
            $repositoryInfo['title']       = $repositoryUrl;
            if ($repositoryTitle != null)
            {
                $repositoryInfo['title'] = $repositoryTitle;
            }
            $repositoryInfo['configTypes'] = array();
            $repositoryInfo['apps']        = array();

        }

        $repositoryInfo['contentTypes'] = array( '*' => '*' );

        $this->requestedRepositories[$repositoryUrl] = $repositoryInfo;

    }


    public function addAllConfigTypesOfRepository($repositoryUrl, $apiUser = null, $apiPassword = null, $authType = 'Basic', $repositoryTitle = null)
    {
        if (array_key_exists($repositoryUrl, $this->requestedRepositories))
        {
            $repositoryInfo = $this->requestedRepositories[$repositoryUrl];
        }
        else
        {
            $repositoryInfo                = array();
            $repositoryInfo['url']         = $repositoryUrl;
            $repositoryInfo['apiUser']     = $apiUser;
            $repositoryInfo['apiPassword'] = $apiPassword;
            $repositoryInfo['authType']    = $authType;
            $repositoryInfo['title']       = $repositoryUrl;
            if ($repositoryTitle)
            {
                $repositoryInfo['title'] = $repositoryTitle;
            }
            $repositoryInfo['contentTypes'] = array();
            $repositoryInfo['apps']         = array();
        }
        $repositoryInfo['configTypes'] = array( '*' => '*' );

        $this->requestedRepositories[$repositoryUrl] = $repositoryInfo;

    }


    public function addOneContentType($contentTypeName, $url, $apiUser = null, $apiPassword = null, $authType = 'Basic', $repositoryTitle = null, $shortcut = null, $contentTypeTitle = null)
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
            $repositoryInfo['shortcut']    = $shortcut;
            $repositoryInfo['title']       = $url;
            if ($repositoryTitle)
            {
                $repositoryInfo['title'] = $repositoryTitle;
            }
            $repositoryInfo['contentTypes'] = array();
            $repositoryInfo['configTypes']  = array();
            $repositoryInfo['apps']         = array();
        }

        $repositoryInfo['contentTypes'][$contentTypeName] = $contentTypeTitle;

        $this->requestedRepositories[$url] = $repositoryInfo;

    }


    public function addAppToRepository($repositoryUrl, $name, $settings = array())
    {
        if (array_key_exists($repositoryUrl, $this->requestedRepositories))
        {
            $repositoryInfo = $this->requestedRepositories[$repositoryUrl];

            $repositoryInfo['apps'][$name]               = $settings;
            $this->requestedRepositories[$repositoryUrl] = $repositoryInfo;
        }

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
            $repositories[$repositoryInfo['url']] = array( 'title' => $repositoryInfo['title'], 'accessHash' => $hash, 'shortcut' => $repositoryInfo['shortcut'] );
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

            if (array_key_exists($repositoryInfo['url'], $this->repositoryObjects))
            {
                $repository = $this->repositoryObjects[$repositoryInfo['url']];

                foreach ($repository->getContentTypes() as $contentTypeName => $contentTypeTitle)
                {
                    if (array_key_exists('*', $repositoryInfo['contentTypes']) OR array_key_exists($contentTypeName, $repositoryInfo['contentTypes']))
                    {
                        if (!$contentTypeTitle)
                        {
                            $contentTypeTitle = $contentTypeName;
                        }
                        $hash                           = md5($url . '-contentType-' . $contentTypeName);
                        $contentTypes[$contentTypeName] = array( 'title' => $contentTypeTitle, 'accessHash' => $hash );
                    }
                }
            }

            return $contentTypes;
        }

        return false;
    }


    public function listConfigTypes($url)
    {
        if (!$this->repositoryObjects)
        {
            $this->initRepositoryObjects();
        }

        if (array_key_exists($url, $this->requestedRepositories))
        {
            $repositoryInfo = $this->requestedRepositories[$url];

            $configTypes = array();

            if (array_key_exists($repositoryInfo['url'], $this->repositoryObjects))
            {
                $repository = $this->repositoryObjects[$repositoryInfo['url']];

                foreach ($repository->getConfigTypes() as $configTypeName => $configTypeTitle)
                {
                    if (array_key_exists('*', $repositoryInfo['configTypes']) OR array_key_exists($configTypeName, $repositoryInfo['configTypes']))
                    {
                        if (!$configTypeTitle)
                        {
                            $configTypeTitle = $configTypeName;
                        }
                        $hash                         = md5($url . '-configType-' . $configTypeName);
                        $configTypes[$configTypeName] = array( 'title' => $configTypeTitle, 'accessHash' => $hash );
                    }
                }
            }

            return $configTypes;
        }

        return false;
    }


    public function listApps($url)
    {
        if (!$this->repositoryObjects)
        {
            $this->initRepositoryObjects();
        }

        if (array_key_exists($url, $this->requestedRepositories))
        {
            $repositoryInfo = $this->requestedRepositories[$url];

            return $repositoryInfo['apps'];

        }

        return false;

    }


    public function hasFiles($url)
    {
        return true;
    }


    /**
     * @param $hash
     *
     * @return bool|Repository
     */
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


    /**
     * @param $shortcut
     *
     * @return Repository|bool
     */
    public function getRepositoryByContentTypeShortcut($shortcut)
    {
        $tokens = explode('.', $shortcut);

        if (count($tokens) != 2)
        {
            return false;
        }
        $repository = $this->getRepositoryByShortcut($tokens[0]);

        if ($repository)
        {
            if ($repository->hasContentType($tokens[1]))
            {
                $repository->selectContentType($tokens[1]);

                return $repository;
            }

        }

        return false;
    }


    /**
     * @param $hash
     *
     * @return bool|Repository
     */
    public function getRepositoryByConfigTypeAccessHash($hash)
    {
        if (!$this->configTypeAccessHashes)
        {
            $this->initAccessHashes();
        }

        if (array_key_exists($hash, $this->configTypeAccessHashes))
        {
            $repository = $this->configTypeAccessHashes[$hash]['repository'];

            return $repository;
        }

        return false;
    }


    /**
     * @param $shortcut
     *
     * @return Repository|bool
     */
    public function getRepositoryByConfigTypeShortcut($shortcut)
    {
        $tokens = explode('.', $shortcut);

        if (count($tokens) != 3 OR $tokens[1] != 'config')
        {
            return false;
        }
        $repository = $this->getRepositoryByShortcut($tokens[0]);

        if ($repository)
        {
            if ($repository->hasConfigType($tokens[2]))
            {
                return $repository;
            }

        }

        return false;
    }


    public function getConfigTypeDefinitionByConfigTypeAccessHash($hash)
    {
        if (!$this->configTypeAccessHashes)
        {
            $this->initAccessHashes();
        }

        if (array_key_exists($hash, $this->configTypeAccessHashes))
        {
            /** @var Repository $repository */
            $repository = $this->configTypeAccessHashes[$hash]['repository'];

            return $repository->getConfigTypeDefinition($this->configTypeAccessHashes[$hash]['configTypeName']);
        }

        return false;
    }


    /**
     * @param $hash
     *
     * @return bool|Repository
     */
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


    /**
     * @param $shortcut
     *
     * @return bool|Repository
     */
    public function getRepositoryByShortcut($shortcut)
    {
        if (!$this->repositoryObjects)
        {
            $this->initRepositoryObjects();
        }

        foreach ($this->listRepositories() AS $url => $item)
        {
            if ($item['shortcut'] == $shortcut)
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
        $this->configTypeAccessHashes  = array();

        foreach ($this->requestedRepositories as $repositoryInfo)
        {
            $repository = $this->repositoryObjects[$repositoryInfo['url']];

            foreach ($this->listContentTypes($repositoryInfo['url']) as $contentTypName => $contentTypeItem)
            {
                $this->contentTypeAccessHashes[$contentTypeItem['accessHash']] = array( 'contentTypeName' => $contentTypName, 'repository' => $repository );
            }
            foreach ($this->listConfigTypes($repositoryInfo['url']) as $configTypeName => $configTypeItem)
            {
                $this->configTypeAccessHashes[$configTypeItem['accessHash']] = array( 'configTypeName' => $configTypeName, 'repository' => $repository );
            }
        }

    }


    protected function initRepositoryObjects()
    {
        $this->repositoryObjects = array();
        foreach ($this->requestedRepositories as $repositoryInfo)
        {
            try
            {
                $cacheConfiguration = $this->config->getCacheConfiguration();

                $client = new Client($repositoryInfo['url'], $repositoryInfo['apiUser'], $repositoryInfo['apiPassword'], $repositoryInfo['authType'], $this->cache, $cacheConfiguration['cmdl'], $cacheConfiguration['concurrent_writes'], $cacheConfiguration['data']);
                if ($this->userInfo)
                {
                    $client->setUserInfo($this->userInfo);
                }

                $repository = $client->getRepository();

                $this->repositoryObjects[$repositoryInfo['url']] = $repository;
            }
            catch (\Exception $e)
            {
                $this->context->addErrorMessage('Could not connect to repository ' . $repositoryInfo['url'] . '.');
            }

        }

    }


    public function getAccessHash($repository, $contentTypeDefinition = null)
    {
        foreach ($this->repositoryObjects as $repositoryUrl => $repositoryObject)
        {
            if ($repository == $repositoryObject)
            {
                if ($contentTypeDefinition != null)
                {
                    return md5($repositoryUrl . '-contentType-' . $contentTypeDefinition->getName());
                }
                else
                {
                    return md5($repositoryUrl);
                }
            }
        }

        return false;
    }

}