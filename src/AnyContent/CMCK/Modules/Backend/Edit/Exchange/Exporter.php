<?php

namespace AnyContent\CMCK\Modules\Backend\Edit\Exchange;

use AnyContent\Client\Repository;
use AnyContent\Client\Record;

class Exporter
{
    protected $output;




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
            $result['info']['content_type']=$contentTypeName;
            $result['info']['workspace']=$workspace;
            $result['info']['view']=$viewName;
            $result['info']['language']=$language;
            $result['info']['count']=(string)count($records);

            $result['records']=array();

            foreach ($records as $record)
            {
                $result['records'][$record->getID()]=array('id'=>$record->getID(),'properties'=>$record->getProperties());
                $this->writeln('Fetching record '.$record->getID().' - '.$record->getName());
            }

            $json = json_encode($result);

            return $json;
        }

        return false;
    }


    public function importJSON(Repository $repository, $contentTypeName, $data, $workspace = 'default', $language = 'default', $viewName = 'exchange')
    {
        $repository->selectContentType($contentTypeName);

        // Select view and fallback if necessary
        $contentTypeDefinition = $repository->getContentTypeDefinition();
        $viewDefinition        = $contentTypeDefinition->getExchangeViewDefinition($viewName);
        $viewName              = $viewDefinition->getName();

        $data = json_decode($data,true);

        if (json_last_error()!=0)
        {
            $this->writeln('Error parsing JSON data.');
            return;
        }

        if (array_key_exists('records',$data))
        {
            $rows = $data['records'];

            foreach ($rows as $row)
            {
                $id = $row['id'];
                $properties = $row['properties'];

                $msg = 'Importing record '.$id.' - '.$properties['name'];

                $record = new Record($contentTypeDefinition,'Imported Record',$viewName,$workspace,$language);
                $record->setProperties($properties);
                $record->setID($id);
                $id = $repository->saveRecord($record);

                if ($id)
                {
                    $msg .= ' ['.$id.']';
                }
                else{
                    $msg .= ' [ERROR]';
                }
                $this->writeln($msg);

            }
        }
    }


    /**
     * @param mixed $output
     */
    public function setOutput($output)
    {
        $this->output = $output;
    }



    protected function writeln($msg)
    {
        if ($this->output)
        {
            $this->output->writeln($msg);
        }
    }
}