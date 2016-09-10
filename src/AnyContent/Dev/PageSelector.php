<?php

namespace AnyContent\Dev;

use AnyContent\CMCK\Modules\Backend\Edit\SelectionFormElements\FormElementSelection;

class PageSelector extends FormElementSelection
{
    protected $autocompleteThreshold = 0;


    protected function getSelectionType()
    {
        return 'autocomplete';
    }


    protected function getOptionsForSelectBox()
    {
        return [];
    }


    protected function getOptionsForAutocomplete()
    {
        $options = [];

        $options[]=['label' => '#001: Home', 'value' => 'hahn_air_lines,1','category'=>'Hahn Air Lines'];
        $options[]=['label' => '#002: John', 'value' => 'hahn_air_lines,2','category'=>'Hahn Air Lines'];
        $options[]=['label' => '#003: Doe', 'value' => 'hahn_air_lines,3','category'=>'Hahn Air Lines'];

        $options[]=['label' => '#001: Home', 'value' => 'public,1','category'=>'Public'];
        $options[]=['label' => '#002: John', 'value' => 'public,2','category'=>'Public'];
        $options[]=['label' => '#003: Doe', 'value' => 'public,3','category'=>'Public'];

        return $options;
    }

    protected function getInitalLabelForAutoComplete()
    {
        $label   = '#002: John';
        return $label;
    }

}