<?php

namespace AnyContent\CMCK\Modules\Backend\Edit\Exchange;

use AnyContent\Client\Repository;
use AnyContent\Client\Record;
use CMDL\Util;

class Importer
{

    protected $generateNewIDs = false;

    protected $truncateRecords = false;

    protected $newerRevisionUpdateProtection = false;

    protected $propertyChangesCheck = false;

    protected $count = 0;

    protected $output;

    protected $records = null;

    protected $error = false;


    public function importJSON(Repository $repository, $contentTypeName, $data, $workspace = 'default', $language = 'default', $viewName = 'exchange')
    {
        $this->records = null;
        $this->count   = 0;
        $this->error   = false;

        $repository->selectContentType($contentTypeName);

        // Select view and fallback if necessary
        $contentTypeDefinition = $repository->getContentTypeDefinition();
        $viewDefinition        = $contentTypeDefinition->getExchangeViewDefinition($viewName);
        $viewName              = $viewDefinition->getName();

        $data = json_decode($data, true);

        if (json_last_error() != 0)
        {
            $this->writeln('Error parsing JSON data.');
            $this->error = true;

            return;
        }

        if (array_key_exists('records', $data))
        {
            if ($this->isTruncateRecords())
            {
                $this->deleteEffectiveRecords($repository, $workspace, $viewName, $language);
            }

            $rows = $data['records'];

            foreach ($rows as $row)
            {
                $id         = $row['id'];
                $properties = $row['properties'];

                if ($this->isGenerateNewIDs())
                {
                    $id = null;
                }

                $record = new Record($contentTypeDefinition, 'Imported Record', $viewName, $workspace, $language);
                $record->setProperties($properties);
                $record->setID($id);

                $msg = $this->saveRecord($repository, $record, $workspace, $viewName, $language);

                $this->writeln($msg);
            }
        }

        return !$this->error;
    }


    public function importXLSX(Repository $repository, $contentTypeName, $filename, $workspace = 'default', $language = 'default', $viewName = 'exchange')
    {
        $this->count   = 0;
        $this->records = null;
        $this->error   = false;

        $repository->selectContentType($contentTypeName);

        // Select view and fallback if necessary
        $contentTypeDefinition = $repository->getContentTypeDefinition();
        $viewDefinition        = $contentTypeDefinition->getExchangeViewDefinition($viewName);
        $viewName              = $viewDefinition->getName();

        $objPHPExcel = \PHPExcel_IOFactory::load($filename);

        if ($objPHPExcel)
        {
            if ($this->isTruncateRecords())
            {
                $this->deleteEffectiveRecords($repository, $workspace, $viewName, $language);
            }

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

            for ($row = 2; $row <= $highestRow; ++$row)
            {
                $id = null;
                if ($idColumnIndex !== false && !$this->isGenerateNewIDs())
                {
                    $id = $objWorksheet->getCellByColumnAndRow($idColumnIndex, $row)->getValue();
                }
                $properties = array();
                foreach ($propertiesColumnIndices as $property => $col)
                {
                    $value                 = $objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
                    $properties[$property] = $value;
                }

                $record = new Record($contentTypeDefinition, 'Imported Record', $viewName, $workspace, $language);
                $record->setProperties($properties);
                $record->setID($id);
                $msg = $this->saveRecord($repository, $record, $workspace, $viewName, $language);

                $this->writeln($msg);

            }
        }
        else
        {
            $this->writeln('Error parsing Excel file.');
            $this->error = true;
        }

        return !$this->error;
    }


    protected function saveRecord(Repository $repository, Record $record, $workspace, $viewName, $language)
    {

        $msg = trim('Importing record ' . $record->getID()) . ' - ' . $record->getName();

        if ($this->isNewerRevisionUpdateProtection())
        {
            if ($this->gotNewerRevision($repository, $record, $workspace, $viewName, $language))
            {
                return 'Skipping record ' . $record->getID() . ' - ' . $record->getName() . (' (Newer Revision)');
            }
        }

        if ($this->isPropertyChangesCheck() == false || $this->hasChanged($repository, $record, $workspace, $viewName, $language))
        {
            $id = $repository->saveRecord($record, $workspace, $viewName, $language);

            if ($id)
            {
                $msg .= ' [' . $id . ']';
                $this->count++;
            }
            else
            {
                $msg .= ' [ERROR]';
            }
        }
        else
        {
            $msg = 'Skipping record ' . $record->getID() . ' - ' . $record->getName() . (' (No changes)');
        }

        return $msg;
    }


    protected function hasChanged(Repository $repository, Record $record, $workspace, $viewName, $language)
    {
        if ($record->getID() != null)
        {
            $records = $this->getRecords($repository, $workspace, $viewName, $language);

            if (isset ($records[$record->getID()]))
            {
                $effectiveRecord = $records[$record->getID()];
                foreach ($record->getProperties() as $property => $value)
                {
                    if ($effectiveRecord->getProperty($property) != $value)
                    {
                        return true;
                    }
                }

                return false;
            }
        }

        return true;
    }


    protected function gotNewerRevision(Repository $repository, Record $record, $workspace, $viewName, $language)
    {
        if ($record->getID() != null && $record->getRevision() != null)
        {
            $records = $this->getRecords($repository, $workspace, $viewName, $language);

            if (isset ($records[$record->getID()]))
            {
                /** @var Record $effectiveRecord */
                $effectiveRecord = $records[$record->getID()];

                if ($effectiveRecord->getRevision() > $record->getRevision())
                {
                    return true;
                }

            }
        }

        return false;
    }


    protected function deleteEffectiveRecords(Repository $repository, $workspace, $viewName, $language)
    {
        $this->writeln('');
        $this->writeln('Start deleting current effective records');

        $records = $this->getRecords($repository, $workspace, $viewName, $language);
        foreach ($records as $id => $record)
        {
            $this->writeln('Deleting record ' . $record->getID() . ' - ' . $record->getName());
            $repository->deleteRecord($id, $workspace, $language);
        }

        $this->records = null;
    }


    /**
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }


    /**
     * @return null
     */
    protected function getRecords(Repository $repository, $workspace, $viewName, $language)
    {
        if (!$this->records)
        {
            $this->writeln('');
            $this->writeln('Start fetching current effective records');
            $this->writeln('');
            $this->records = $repository->getRecords($workspace, $viewName, $language);
            if ($this->records===false)
            {
                throw new \Exception('Error fetching current effective records.');
            }
            $this->writeln('Done fetching current effective records');
            $this->writeln('');
        }

        return $this->records;
    }


    /**
     * @return boolean
     */
    public function isGenerateNewIDs()
    {
        return $this->generateNewIDs;
    }


    /**
     * @param boolean $generateNewIDs
     */
    public function setGenerateNewIDs($generateNewIDs)
    {
        $this->generateNewIDs = $generateNewIDs;
    }


    /**
     * @return boolean
     */
    public function isNewerRevisionUpdateProtection()
    {
        return $this->newerRevisionUpdateProtection;
    }


    /**
     * @param boolean $newerRevisionUpdateProtection
     */
    public function setNewerRevisionUpdateProtection($newerRevisionUpdateProtection)
    {
        $this->newerRevisionUpdateProtection = $newerRevisionUpdateProtection;
    }


    /**
     * @return boolean
     */
    public function isTruncateRecords()
    {
        return $this->truncateRecords;
    }


    /**
     * @param boolean $truncateRecords
     */
    public function setTruncateRecords($truncateRecords)
    {
        $this->truncateRecords = $truncateRecords;
    }


    /**
     * @return boolean
     */
    public function isPropertyChangesCheck()
    {
        return $this->propertyChangesCheck;
    }


    /**
     * @param boolean $propertyChangesCheck
     */
    public function setPropertyChangesCheck($propertyChangesCheck)
    {
        $this->propertyChangesCheck = $propertyChangesCheck;
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
            $this->writeln($msg);
        }
    }

}