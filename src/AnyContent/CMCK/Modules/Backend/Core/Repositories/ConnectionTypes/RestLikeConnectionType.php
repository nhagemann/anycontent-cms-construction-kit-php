<?php

namespace AnyContent\CMCK\Modules\Backend\Core\Repositories\ConnectionTypes;

use AnyContent\Client\Repository;
use AnyContent\Client\RepositoryFactory;

class RestLikeConnectionType implements ConnectionTypeInterface
{

    /** @return Repository */
    public function createRepository($name, $params, $session = [])
    {

        $repositoryFactory = new RepositoryFactory();

        $repository = $repositoryFactory->createRestLikeRepository($name, $params['url'], $session);
        $repository->setPublicUrl($params['url']);
        if (isset ($params['files'])) {
            $fileManager = $repository->getFileManager();
            if ($fileManager) {
                $fileManager->setPublicUrl($params['files']);
            }
        }

        return $repository;
    }
}