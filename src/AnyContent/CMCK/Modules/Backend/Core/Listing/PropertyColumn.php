<?php

namespace AnyContent\CMCK\Modules\Backend\Core\Listing;

use AnyContent\Client\Record;
use CMDL\FormElementDefinition;

class PropertyColumn
{

    protected $type = 'Property';

    protected $title;

    protected $property;

    protected $width;

    protected $badge = false;

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }


    /**
     * @var FormElementDefinition
     */
    protected $formElementDefinition;

    /** @var  ColumnRenderer */
    protected $renderer;

    /**
     * @var bool
     */
    protected $linkToRecord = false;


    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }


    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }


    /**
     * @return mixed
     */
    public function getProperty()
    {
        return $this->property;
    }


    /**
     * @param mixed $property
     */
    public function setProperty($property)
    {
        $this->property = $property;
    }


    /**
     * @return mixed
     */
    public function getWidth()
    {
        return $this->width;
    }


    /**
     * @param mixed $width
     */
    public function setWidth($width)
    {
        $this->width = $width;
    }


    public function getValue(Record $record)
    {
        return ($record->getProperty($this->getProperty()));
    }


    public function formatValue(Record $record)
    {
        return $this->getRenderer()->render($this, $record);
    }


    /**
     * @param FormElementDefinition $formElementDefinition
     */
    public function setFormElementDefinition($formElementDefinition)
    {
        $this->formElementDefinition = $formElementDefinition;
    }


    /**
     * @return FormElementDefinition
     */
    public function getFormElementDefinition()
    {
        return $this->formElementDefinition;
    }


    /**
     * @return boolean
     */
    public function isBadge()
    {
        return $this->badge;
    }


    /**
     * @param boolean $badge
     */
    public function setBadge($badge)
    {
        $this->badge = $badge;
    }


    /**
     * @return CellRenderer
     */
    public function getRenderer()
    {
        return $this->renderer;
    }


    /**
     * @param CellRenderer $renderer
     */
    public function setRenderer($renderer)
    {
        $this->renderer = $renderer;
    }


    /**
     * @return boolean
     */
    public function isLinkToRecord()
    {
        return $this->linkToRecord;
    }


    /**
     * @param boolean $linkToRecord
     */
    public function setLinkToRecord($linkToRecord)
    {
        $this->linkToRecord = $linkToRecord;
    }


}