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

        /** @var Record $record */
        $records = $repository->getRecords($workspace, $viewName, $language, 'id', array(), null, 1, null, null, 0);

        if ($records)
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

                $result['records'][$record->getID()] = array( 'id' => $record->getID(), 'properties' => $record->getProperties() );
            }

            $json = json_encode($result);

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

        /** @var Record $record */
        $records = $repository->getRecords($workspace, $viewName, $language, 'id', array(), null, 1, null, null, 0);

        if ($records)
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

            $row    = 1;
            $column = 1;
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

                $column = 1;
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


    public function importJSON(Repository $repository, $contentTypeName, $data, $workspace = 'default', $language = 'default', $viewName = 'exchange')
    {
        $repository->selectContentType($contentTypeName);

        // Select view and fallback if necessary
        $contentTypeDefinition = $repository->getContentTypeDefinition();
        $viewDefinition        = $contentTypeDefinition->getExchangeViewDefinition($viewName);
        $viewName              = $viewDefinition->getName();

        $data = json_decode($data, true);

        if (json_last_error() != 0)
        {
            $this->writeln('Error parsing JSON data.');

            return;
        }

        if (array_key_exists('records', $data))
        {
            $rows = $data['records'];

            foreach ($rows as $row)
            {
                $id         = $row['id'];
                $properties = $row['properties'];
                $name       = '';
                if (isset($properties['name']))
                {
                    $name = $properties['name'];
                }

                $msg = trim('Importing record ' . $id) . ' - ' . $name;

                $record = new Record($contentTypeDefinition, 'Imported Record', $viewName, $workspace, $language);
                $record->setProperties($properties);
                $record->setID($id);
                $id = $repository->saveRecord($record);

                if ($id)
                {
                    $msg .= ' [' . $id . ']';
                }
                else
                {
                    $msg .= ' [ERROR]';
                }
                $this->writeln($msg);

            }
        }
    }


    public function importXLSX(Repository $repository, $contentTypeName, $filename, $workspace = 'default', $language = 'default', $viewName = 'exchange')
    {
        $repository->selectContentType($contentTypeName);

        // Select view and fallback if necessary
        $contentTypeDefinition = $repository->getContentTypeDefinition();
        $viewDefinition        = $contentTypeDefinition->getExchangeViewDefinition($viewName);
        $viewName              = $viewDefinition->getName();

        $objPHPExcel = \PHPExcel_IOFactory::load($filename);

        if ($objPHPExcel)
        {
            $objWorksheet = $objPHPExcel->getActiveSheet();

            $highestRow    = $objWorksheet->getHighestRow(); // e.g. 10
            $highestColumn = $objWorksheet->getHighestColumn(); // e.g 'F'

            $highestColumnIndex = \PHPExcel_Cell::columnIndexFromString($highestColumn); // e.g. 5
            $idColumnIndex      = null;

            $propertiesColumnIndices = array();

            for ($i = 0; $i <= $highestColumnIndex; $i++)
            {
                $value = trim($objWorksheet->getCellByColumnAndRow($i, 1)->getValue());
                if ($value != '')
                {
                    if ($value == Util::generateValidIdentifier($value))
                    {
                        if ($contentTypeDefinition->hasProperty($value, $viewName))
                        {
                            $this->writeln('Detected valid property ' . $value);
                            $propertiesColumnIndices[$value] = $i;
                        }
                    }
                    else
                    {
                        if ($value == '.id')
                        {
                            $idColumnIndex = $i;
                        }
                    }

                }

            }

            $this->writeln('');

            for ($row = 2; $row <= $highestRow; ++$row)
            {
                $id = null;
                if ($idColumnIndex !== false)
                {
                    $id = $objWorksheet->getCellByColumnAndRow($idColumnIndex, $row)->getValue();
                }
                $properties = array();
                foreach ($propertiesColumnIndices as $property => $col)
                {
                    $value                 = $objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
                    $properties[$property] = $value;
                }
                $name = '';

                if (isset($propertiesColumnIndices['name']))
                {
                    $name = $objWorksheet->getCellByColumnAndRow($propertiesColumnIndices['name'], $row)->getValue();
                }

                $msg = trim('Importing record ' . $id) . ' - ' . $name;

                $record = new Record($contentTypeDefinition, 'Imported Record', $viewName, $workspace, $language);
                $record->setProperties($properties);
                $record->setID($id);
                $id = $repository->saveRecord($record);

                if ($id)
                {
                    $msg .= ' [' . $id . ']';
                }
                else
                {
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