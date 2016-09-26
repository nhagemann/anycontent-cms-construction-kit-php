<?php

namespace AnyContent\CMCK\Modules\Backend\Core\Repositories\ConnectionTypes;

use AnyContent\Client\Repository;

interface ConnectionTypeInterface
{
    /** @return Repository */
    public function createRepository($name, $params, $session=[]);

}