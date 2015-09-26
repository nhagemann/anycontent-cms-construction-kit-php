<?php

namespace AnyContent\CMCK\Modules\Backend\Core\Listing;

class ContentViewDefault extends BaseContentView
{

    /** @var  CellRenderer */
    protected $cellRenderer;


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
        $this->getLayout()->addJsFile('listing.js');

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

        // store items per page
        if ($this->getRequest()->query->has('c'))
        {
            $this->getContext()->setCurrentItemsPerPage($this->getRequest()->query->get('c'));
        }

        // reset sorting order and search query if listing button has been pressed inside a listing
        if ($this->getRequest()->get('_route') == 'listRecordsReset')
        {
            $this->getContext()->setCurrentSortingOrder('name', false);
            $this->getContext()->setCurrentSearchTerm('');
        }

        $vars['searchTerm']   = $this->getContext()->getCurrentSearchTerm();
        $vars['itemsPerPage'] = $this->getContext()->getCurrentItemsPerPage();

        $filter = $this->getFilter();

        $vars['table']  = false;
        $vars['pager']  = false;
        $vars['filter'] = false;

        $records = $this->getRecords($filter);

        if (count($records) > 0)
        {
            $columns = $this->getColumnsDefinition();

            $vars['table'] = $this->buildTable($columns, $records);

            $count = $this->countRecords($filter);

            $vars['pager'] = $this->getPager()->renderPager($count, $this->getContext()
                                                                         ->getCurrentItemsPerPage(), $this->getContext()
                                                                                                          ->getCurrentListingPage(), 'listRecords', array( 'contentTypeAccessHash' => $this->getContentTypeAccessHash() ));

        }

        return $vars;
    }


    protected function getFilter()
    {
        $filter = null;

        $searchTerm = $this->getContext()->getCurrentSearchTerm();
        if ($searchTerm != '')
        {
            $filter = FilterUtil::normalizeFilterQuery($this->app, $searchTerm, $this->getContentTypeDefinition());
        }

        return $filter;
    }


    protected function getColumnsDefinition()
    {
        $contentTypeDefinition = $this->getContentTypeDefinition();

        $columns = [ ];

        $column = new AttributeColumn();
        $column->setTitle('ID');
        $column->setAttribute('id');
        $column->setLinkToRecord(true);
        $columns[] = $column;

        if ($contentTypeDefinition->hasSubtypes())
        {
            $column = new SubtypeColumn();
            $column->setTitle('Subtype');
            $column->setFormElementDefinition($contentTypeDefinition->getViewDefinition('default')
                                                                    ->getFormElementDefinition('subtype'));
            $columns[] = $column;
        }

        $column = new PropertyColumn();
        $column->setTitle('Name');
        $column->setProperty('name');
        $column->setLinkToRecord(true);
        $column->setFormElementDefinition($contentTypeDefinition->getViewDefinition('default')
                                                                ->getFormElementDefinition('name'));
        $columns[] = $column;

        $column = new AttributeColumn();
        $column->setTitle('Last Change');
        $column->setAttribute('lastchange');
        $columns[] = $column;

        if ($contentTypeDefinition->hasStatusList())
        {
            $column = new StatusColumn();
            $column->setTitle('Status');
            $column->setFormElementDefinition($contentTypeDefinition->getViewDefinition('default')
                                                                    ->getFormElementDefinition('status'));
            $columns[] = $column;
        }

        if ($contentTypeDefinition->isSortable())
        {
            $column = new AttributeColumn();
            $column->setTitle('Pos');
            $column->setAttribute('position');
            $columns[] = $column;
        }

        // Add Edit/Delete-Buttons
        $buttonColumn = new ButtonColumn();
        $buttonColumn->setEditButton(true);
        $buttonColumn->setDeleteButton(true);
        $buttonColumn->setRenderer($this->getCellRenderer());
        $columns[] = $buttonColumn;

        foreach ($columns as $column)
        {
            $column->setRenderer($this->getCellRenderer());
        }

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
                                                                                                                                ->getCurrentSortingOrder(), array(), $itemsPerPage, $page, $filter, null, $this->getContext()
                                                                                                                                                                                                               ->getCurrentTimeShift());

    }

}