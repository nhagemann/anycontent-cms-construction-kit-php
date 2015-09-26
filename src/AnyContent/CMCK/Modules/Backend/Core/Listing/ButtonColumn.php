<?php

namespace AnyContent\CMCK\Modules\Backend\Core\Listing;

use AnyContent\Client\Record;
use CMDL\FormElementDefinition;

class ButtonColumn extends PropertyColumn
{
    protected $type = 'Button';




    /**
     * @return boolean
     */
    public function isEditButton()
    {
        return $this->editButton;
    }


    /**
     * @param boolean $editButton
     */
    public function setEditButton($editButton)
    {
        $this->editButton = $editButton;
    }


    /**
     * @return boolean
     */
    public function isDeleteButton()
    {
        return $this->deleteButton;
    }


    /**
     * @param boolean $deleteButton
     */
    public function setDeleteButton($deleteButton)
    {
        $this->deleteButton = $deleteButton;
    }


    /**
     * @return boolean
     */
    public function isCustomButton()
    {
        return $this->customButton;
    }


    /**
     * @param boolean $customButton
     */
    public function setCustomButton($customButton)
    {
        $this->customButton = $customButton;
    }

}