<?php

namespace AnyContent\CMCK\Modules\Backend\Edit\Exchange;

use AnyContent\Client\Repository;
use AnyContent\Client\Record;
use CMDL\Util;

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

        $this->writeln('Connecting repository');
        $this->writeln('');

        $repository->selectWorkspace($workspace);
        $repository->selectLanguage($language);
        $repository->selectView($viewName);

        /** @var Record[] $records */
        $records = $repository->getRecords('','id');

        if ($records!==false)
        {
            $result                         = array();
            $result['info']['content_type'] = $contentTypeName;
            $result['info']['workspace']    = $workspace;
            $result['info']['view']         = $viewName;
            $result['info']['language']     = $language;
            $result['info']['count']        = (string)count($records);

            $result['records'] = array();

            foreach ($records as $record)
            {
                $this->writeln('Processing record ' . $record->getID() . ' - ' . $record->getName());

                $result['records'][$record->getID()] = array( 'id' => $record->getID(), 'revision' => $record->getRevision(), 'properties' => $record->getProperties() );
            }

            $json = json_encode($result, JSON_PRETTY_PRINT);

            return $json;

        }

        return false;

    }


    public function exportXLSX(Repository $repository, $contentTypeName, $workspace = 'default', $language = 'default', $viewName = 'exchange')
    {
        $repository->selectContentType($contentTypeName);

        // Select view and fallback if necessary
        $contentTypeDefinition = $repository->getContentTypeDefinition();
        $viewDefinition        = $contentTypeDefinition->getExchangeViewDefinition($viewName);
        $viewName              = $viewDefinition->getName();

        $this->writeln('Connecting repository');
        $this->writeln('');

        $repository->selectWorkspace($workspace);
        $repository->selectLanguage($language);
        $repository->selectView($viewName);

        /** @var Record[] $records */
        $records = $repository->getRecords('','.id',1);

        if ($records!==false)
        {
            // Create new PHPExcel object
            $objPHPExcel = new \PHPExcel();

            // use temp folder for processing of large files
            $cacheMethod   = \PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
            $cacheSettings = array( 'memoryCacheSize' => '12MB' );
            \PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

            // Set document properties
            $objPHPExcel->getProperties()->setCreator("AnyContent CMCK")
                ->setLastModifiedBy("AnyContent CMCK")
                ->setTitle("Full Export from content type " . $contentTypeDefinition->getTitle())
                ->setSubject("AnyContent Export")
                ->setDescription("");

            $worksheet = $objPHPExcel->setActiveSheetIndex(0);
            $worksheet->setTitle('Export');

            $worksheet->setCellValueByColumnAndRow(0, 1, '.id');
            $worksheet->getStyleByColumnAndRow(0, 1)->getFont()->setBold(false)->setItalic(true);

            $worksheet->setCellValueByColumnAndRow(1, 1, '.revision');
            $worksheet->getStyleByColumnAndRow(1, 1)->getFont()->setBold(false)->setItalic(true);

            $row    = 1;
            $column = 2;
            foreach ($contentTypeDefinition->getProperties($viewName) as $property)
            {
                $worksheet->setCellValueByColumnAndRow($column, $row, $property);
                $worksheet->getStyleByColumnAndRow($column, $row)->getFont()->setBold(true);
                $worksheet->getColumnDimensionByColumn($column)->setWidth(20);
                $column++;
            }

            $row++;

            foreach ($records as $record)
            {
                $this->writeln('Processing record ' . $record->getID() . ' - ' . $record->getName());

                $worksheet->setCellValueByColumnAndRow(0, $row, $record->getID());
                $worksheet->setCellValueByColumnAndRow(1, $row, $record->getRevision());

                $column = 2;
                foreach ($contentTypeDefinition->getProperties($viewName) as $property)
                {
                    $worksheet->setCellValueByColumnAndRow($column, $row, $record->getProperty($property));
                    $column++;
                }
                $row++;
            }

            $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            ob_start();
            $objWriter->save('php://output');
            $excelOutput = ob_get_clean();

            return $excelOutput;
        }

        return false;

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