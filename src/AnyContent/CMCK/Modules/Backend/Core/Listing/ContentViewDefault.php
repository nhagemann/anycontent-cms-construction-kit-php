<?php

namespace AnyContent\CMCK\Modules\Backend\Core\Listing;

use AnyContent\Client\Repository;
use AnyContent\CMCK\Modules\Backend\Core\Application\Application;
use CMDL\Annotations\CustomAnnotation;
use Symfony\Component\HttpFoundation\Request;

class ContentViewDefault extends BaseContentView
{

    public function getTemplate()
    {
        return 'listing-contentview-default.twig';
    }


    public function doesProcessSearch()
    {
        return true;
    }


    public function apply($vars)
    {
        $app = $this->app;

        /** @var Request $request */
        $request = $app['request'];

        $repository = $this->getRepository();

        $contentTypeAccessHash = $this->getContentTypeAccessHash();

        $contentTypeDefinition = $this->getContentTypeDefinition();

        // reset chained save operations (e.g. 'save-insert') to 'save' only upon listing of a content type
        if (key($app['context']->getCurrentSaveOperation()) != 'save-list')
        {
            $app['context']->setCurrentSaveOperation('save', 'Save');
        }

        if ($request->query->has('s'))
        {
            $app['context']->setCurrentSortingOrder($request->query->get('s'));
        }



        if ($request->get('_route') == 'listRecordsReset')
        {
            $app['context']->setCurrentSortingOrder('id', false);
            $app['context']->setCurrentSearchTerm('');
        }

        $page = $app['context']->getCurrentListingPage();
        $itemsPerPage = $app['context']->getCurrentItemsPerPage();


        $filter = null;

        $searchTerm         = $app['context']->getCurrentSearchTerm();
        $vars['searchTerm'] = $searchTerm;
        if ($searchTerm != '')
        {
            $filter = FilterUtil::normalizeFilterQuery($app, $searchTerm, $contentTypeDefinition);
        }

        $vars['records'] = $this->getRecords($app, $repository, $contentTypeAccessHash, null, 'default', $itemsPerPage, $page, $filter);

        $count = $repository->countRecords($app['context']->getCurrentWorkspace(), 'default', $app['context']->getCurrentLanguage(), $app['context']->getCurrentSortingOrder(), array(), $itemsPerPage, $page, $filter, null, $app['context']->getCurrentTimeShift());

        $vars['pager'] = $app['pager']->renderPager($count, $itemsPerPage, $page, 'listRecords', array( 'contentTypeAccessHash' => $contentTypeAccessHash ));




        return $vars;
    }


    protected function getRecords($app, Repository $repository, $contentTypeAccessHash, $orderBy = null, $viewName = 'default', $itemsPerPage = null, $page = 1, $filter = null, $subset = null)
    {
        $records = array();

        if (!$orderBy)
        {
            $orderBy = $app['context']->getCurrentSortingOrder();
        }

        /** @var Record $record */
        foreach ($repository->getRecords($app['context']->getCurrentWorkspace(), $viewName, $app['context']->getCurrentLanguage(), $orderBy, array(), $itemsPerPage, $page, $filter, $subset, $app['context']->getCurrentTimeShift()) AS $record)
        {
            $item                     = array();
            $item['record']           = $record;
            $item['name']             = $record->getName();
            $item['id']               = $record->getID();
            $item['editUrl']          = $app['url_generator']->generate('editRecord', array( 'contentTypeAccessHash' => $contentTypeAccessHash, 'recordId' => $record->getID(), 'workspace' => $app['context']->getCurrentWorkspace(), 'language' => $app['context']->getCurrentLanguage() ));
            $item['deleteUrl']        = $app['url_generator']->generate('deleteRecord', array( 'contentTypeAccessHash' => $contentTypeAccessHash, 'recordId' => $record->getID(), 'workspace' => $app['context']->getCurrentWorkspace(), 'language' => $app['context']->getCurrentLanguage() ));
            $item['status']['label']  = $record->getStatusLabel();
            $item['subtype']['label'] = $record->getSubtypeLabel();
            $item['position']         = $record->getPosition();
            $item['level']            = $record->getLevelWithinSortedTree();

            /** @var UserInfo $userInfo */
            $userInfo         = $record->getLastChangeUserInfo();
            $item['username'] = $userInfo->getName();
            $date             = new \DateTime();
            $date->setTimestamp($userInfo->getTimestamp());
            $item['lastChangeDate'] = $date->format('d.m.Y H:i:s');
            $item['gravatar']       = '<img src="https://www.gravatar.com/avatar/' . md5(trim($userInfo->getUsername())) . '?s=40" height="40" width="40"/>';

            $records[] = $item;
        }

        return $records;
    }


}