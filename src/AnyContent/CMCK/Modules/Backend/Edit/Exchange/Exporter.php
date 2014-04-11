<?php

namespace AnyContent\CMCK\Modules\Backend\Edit\Exchange;

use AnyContent\Client\Repository;
use AnyContent\Client\Record;

class Exporter
{

    public function exportJSON(Repository $repository, $contentTypeName, $workspace = 'default', $language = 'default', $viewName = 'exchange')
    {

        $repository->selectContentType($contentTypeName);

        // Select view and fallback if necessary
        $contentTypeDefinition = $repository->getContentTypeDefinition();
        $viewDefinition        = $contentTypeDefinition->getExchangeViewDefinition($viewName);
        $viewName              = $viewDefinition->getName();

        /** @var Record $record */
        $records = $repository->getRecords($workspace, $viewName, $language, 'id', array(), null, 1, null, null, 0);

        if ($records)
        {
            $result = array();
            $result['info']['repository']='?'; // TODO
            $result['info']['content_type']=$contentTypeName;
            $result['info']['workspace']=$workspace;
            $result['info']['view']=$viewName;
            $result['info']['language']=$language;

            $result['records']=array();

            foreach ($records as $record)
            {
                $result['records'][$record->getID()]=array('id'=>$record->getID(),'properties'=>$record->getProperties());
            }



            $json = json_encode($result);

            return $json;
        }

        return false;
    }


    public function importJSON(Repository $repository, $contentTypeName, $data, $workspace = 'default', $language = 'default', $viewName = 'exchange')
    {

    }
}