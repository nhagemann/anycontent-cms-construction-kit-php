<?php

namespace AnyContent\CMCK\Modules\Backend\View\CustomList;

use AnyContent\CMCK\Modules\Backend\Core\Listing\BaseContentView;
use AnyContent\CMCK\Modules\Backend\Core\Listing\ContentViewDefault;
use AnyContent\CMCK\Modules\Backend\Core\Listing\ListingRecord;
use AnyContent\CMCK\Modules\Backend\Core\Pager\PagingHelper;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class ContentViewCustomList extends BaseContentView
{

    /** @var  CellRenderer */
    protected $cellRenderer;


    /**
     * @return Request
     */
    protected function getRequest()
    {
        return $this->app['request'];
    }


    /**
     * @return PagingHelper
     */
    protected function getPager()
    {
        return $this->app['pager'];
    }


    public function getTemplate()
    {
        return 'listing-contentview-list.twig';
    }


    public function apply($vars)
    {

        // reset chained save operations (e.g. 'save-insert') to 'save' only upon listing of a content type
        if (key($this->getContext()->getCurrentSaveOperation()) != 'save-list')
        {
            $this->getContext()->setCurrentSaveOperation('save', 'Save');
        }

        // store sorting order
        if ($this->getRequest()->query->has('s'))
        {
            $this->getContext()->setCurrentSortingOrder($this->getRequest()->query->get('s'));
        }

        // reset sorting order and search query if listing button has been pressed inside a listing
        if ($this->getRequest()->get('_route') == 'listRecordsReset')
        {
            $this->getContext()->setCurrentSortingOrder('name', false);
            $this->getContext()->setCurrentSearchTerm('');
        }

        // apply filter
        $filter = null;

        $searchTerm         = $this->getContext()->getCurrentSearchTerm();
        $vars['searchTerm'] = $searchTerm;
        if ($searchTerm != '')
        {
            $filter = FilterUtil::normalizeFilterQuery($this->app, $searchTerm, $this->getContentTypeDefinition());
        }

        $count = $this->countRecords($filter);

        $vars['pager'] = $this->getPager()->renderPager($count, $this->getContext()
                                                                     ->getCurrentItemsPerPage(), $this->getContext()
                                                                                                      ->getCurrentListingPage(), 'listRecords', array( 'contentTypeAccessHash' => $this->getContentTypeAccessHash() ));
        $records       = $this->getRecords($filter);

        $columns = $this->getColumnsDefinition();

        $table = $this->buildTable($columns, $records);

        $vars['table'] = $table;

        return $vars;
    }


    protected function getColumnsDefinition()
    {
        $annotation = $this->getCustomAnnotation();
        $definition = $this->getContentTypeDefinition();

        $list = $annotation->getList(1);

        $columns = [ ];

        foreach ($list as $key => $title)
        {
            if ($definition->hasProperty($key))
            {
                $formelementDefinition = $definition->getViewDefinition('default')->getFormElementDefinition($key);

                switch ($formelementDefinition->getFormElementType())
                {
                    case 'selection':
                        $column = new SelectionColumn();
                        break;
                    default:
                        $column = new PropertyColumn();
                        break;
                }
                $column->setProperty($key);
                $column->setFormElementDefinition($formelementDefinition);
            }
            else
            {
                $column = new AttributeColumn();
                $column->setAttribute($key);
            }

            $column->setTitle($title);

            $column->setRenderer($this->getCellRenderer());

            if ($key == 'name')
            {
                $column->setLinkToRecord(true);
            }

            $columns[] = $column;

        }

        // Add Edit/Delete-Buttons
        $buttonColumn = new ButtonColumn();
        $buttonColumn->setEditButton(true);
        $buttonColumn->setDeleteButton(true);
        $buttonColumn->setRenderer($this->getCellRenderer());
        $columns[] = $buttonColumn;

        return $columns;
    }


    protected function buildTable($columns, $records)
    {
        $table = [ ];

        foreach ($columns as $column)
        {
            $table['header'][] = $column;
        }

        foreach ($records as $record)
        {
            $line = [ ];
            foreach ($columns as $column)
            {
                $line[] = $column->formatValue($record);
            }
            $table['body'][] = $line;
        }

        return $table;
    }


    /**
     * @return CellRenderer
     */
    public function getCellRenderer()
    {
        if (!$this->cellRenderer)
        {
            $this->cellRenderer = new CellRenderer($this->app);
        }

        return $this->cellRenderer;
    }


    public function countRecords($filter)
    {
        $repository = $this->getRepository();

        $page         = $this->getContext()->getCurrentListingPage();
        $itemsPerPage = $this->getContext()->getCurrentItemsPerPage();
        $viewName     = 'default';

        $count = $repository->countRecords($this->getContext()->getCurrentWorkspace(), $viewName, $this->getContext()
                                                                                                       ->getCurrentLanguage(), $this->getContext()
                                                                                                                                    ->getCurrentSortingOrder(), array(), $itemsPerPage, $page, $filter, null, $this->getContext()
                                                                                                                                                                                                                   ->getCurrentTimeShift());

        return $count;
    }


    public function getRecords($filter)
    {
        $repository = $this->getRepository();

        $page         = $this->getContext()->getCurrentListingPage();
        $itemsPerPage = $this->getContext()->getCurrentItemsPerPage();
        $viewName     = 'default';

        return $repository->getRecords($this->getContext()->getCurrentWorkspace(), $viewName, $this->getContext()
                                                                                                   ->getCurrentLanguage(), $this->getContext()
                                                                                                                                ->getCurrentSortingOrder(), array(), $itemsPerPage, $page, $filter, null, $this->getContext()->getCurrentTimeShift());

    }

}