<?php

namespace AnyContent\CMCK\Modules\Backend\ConnectionTypes\ContentArchiveConnectionType;

use AnyContent\Client\Repository;
use AnyContent\Client\RepositoryFactory;
use AnyContent\CMCK\Modules\Backend\Core\Repositories\ConnectionTypes\ConnectionTypeInterface;

class ContentArchiveConnectionType implements ConnectionTypeInterface
{

    /** @return Repository */
    public function createRepository($name, $params, $session = [])
    {
        $repositoryFactory = new RepositoryFactory();

        return $repositoryFactory->createContentArchiveRepository($name, $params['folder']);
    }
}
