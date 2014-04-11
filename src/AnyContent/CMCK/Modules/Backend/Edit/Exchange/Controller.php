<?php

namespace AnyContent\CMCK\Modules\Backend\Edit\Exchange;

use AnyContent\Client\ContentFilter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

use AnyContent\CMCK\Modules\Backend\Core\Application\Application;

use AnyContent\Client\Repository;
use AnyContent\Client\Record;
use AnyContent\Client\UserInfo;

use AnyContent\CMCK\Modules\Backend\Edit\Export\Module;

class Controller
{

    public static function exportRecords(Application $app, Request $request, $contentTypeAccessHash, Module $module=null)
    {

        /** @var Repository $repository */
        $repository = $app['repos']->getRepositoryByContentTypeAccessHash($contentTypeAccessHash);

        if ($repository)
        {
            $contentTypeDefinition = $repository->getContentTypeDefinition();
            $app['context']->setCurrentRepository($repository);
            $app['context']->setCurrentContentType($contentTypeDefinition);

            $viewName = $contentTypeDefinition->getExchangeViewDefinition()->getName();

            // Create new PHPExcel object
            $objPHPExcel = new \PHPExcel();

            // Set document properties
            $objPHPExcel->getProperties()->setCreator("AnyContent CMCK")
                ->setLastModifiedBy("AnyContent CMCK")
                ->setTitle("Full Export from content type " . $contentTypeDefinition->getTitle())
                ->setSubject("Office 2007 XLSX Test Document")
                ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
                ->setKeywords("office 2007 openxml php")
                ->setCategory("Test result file");

            $worksheet = $objPHPExcel->setActiveSheetIndex(0);
            $worksheet->setTitle('Export');

            $worksheet->setCellValueByColumnAndRow(0, 1, 'record.id');
            $worksheet->getStyleByColumnAndRow(0, 1)->getFont()->setBold(false)->setItalic(true);
            $worksheet->setCellValueByColumnAndRow(1, 1, 'record.lastchange');
            $worksheet->getStyleByColumnAndRow(1, 1)->getFont()->setBold(false)->setItalic(true);
            $worksheet->getColumnDimension('B')->setWidth(15);

            $row    = 1;
            $column = 2;
            foreach ($contentTypeDefinition->getProperties($viewName) as $property)
            {
                $worksheet->setCellValueByColumnAndRow($column, $row, $property);
                $worksheet->getStyleByColumnAndRow($column, $row)->getFont()->setBold(true);

                $column++;
            }
            //$worksheet->setCellValueByColumnAndRow($column++, $row, 'record.revision');
            //$worksheet->setCellValueByColumnAndRow($column++, $row, 'record.parent_id');
            //$worksheet->setCellValueByColumnAndRow($column++, $row, 'record.position');
            $row++;

            //  setBreakByColumnAndRow($pColumn = 0, $pRow = 1, $pBreak = PHPExcel_Worksheet::BREAK_NONE)

            //$this->workbook->getActiveSheet()->getStyle($cell)->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_TEXT );

            /** @var Record $record */
            foreach ($repository->getRecords($app['context']->getCurrentWorkspace(), $viewName, $app['context']->getCurrentLanguage(), 'id', array(), null, 1, null, null, $app['context']->getCurrentTimeShift()) AS $record)
            {
                /** @var UserInfo $userInfo */
                $userInfo = $record->getLastChangeUserInfo();

                $worksheet->setCellValueByColumnAndRow(0, $row, $record->getID());
                $date = new \DateTime();
                $date->setTimestamp($userInfo->getTimestamp());
                $dateExcel = \PHPExcel_Shared_Date::PHPToExcel($date);
                $worksheet->setCellValueByColumnAndRow(1, $row, $dateExcel);
                $worksheet->getStyleByColumnAndRow(1, $row)->getNumberFormat()
                    ->setFormatCode($module->getOption('FormatCode.DateTime'));

                $column = 2;
                foreach ($contentTypeDefinition->getProperties($viewName) as $property)
                {
                    $worksheet->setCellValueByColumnAndRow($column, $row, $record->getProperty($property));
                    //$worksheet->getStyleByColumnAndRow($column, $row)->getNumberFormat()
                    //    ->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                    $column++;
                }

                /*$item                     = array();
                $item['record']           = $record;
                $item['name']             = $record->getName();
                $item['id']               = $record->getID();
                $item['editUrl']          = $app['url_generator']->generate('editRecord', array( 'contentTypeAccessHash' => $contentTypeAccessHash, 'recordId' => $record->getID() ));
                $item['deleteUrl']        = $app['url_generator']->generate('deleteRecord', array( 'contentTypeAccessHash' => $contentTypeAccessHash, 'recordId' => $record->getID() ));
                $item['status']['label']  = $record->getStatusLabel();
                $item['subtype']['label'] = $record->getSubtypeLabel();
                $item['position']         = $record->getPosition();
                $item['level']            = $record->getLevelWithinSortedTree();


                $item['username'] = $userInfo->getName();
                $date             = new \DateTime();
                $date->setTimestamp($userInfo->getTimestamp());
                $item['lastChangeDate'] = $date->format('d.m.Y H:i:s');
                $item['gravatar']       = '<img src="https://www.gravatar.com/avatar/' . md5(trim($userInfo->getUsername())) . '?s=40" height="40" width="40"/>';

                */
                $row++;
            }

            // Set active sheet index to the first sheet, so Excel opens this as the first sheet
            $objPHPExcel->setActiveSheetIndex(0);

            // Redirect output to a client’s web browser (Excel5)
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="01simple.xls"');
            header('Cache-Control: max-age=0');
            // If you're serving to IE 9, then the following may be needed
            header('Cache-Control: max-age=1');

            // If you're serving to IE over SSL, then the following may be needed
            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
            header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
            header('Pragma: public'); // HTTP/1.0

            $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
            die();

        }
        die();

    }

}