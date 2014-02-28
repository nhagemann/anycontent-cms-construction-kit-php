<?php

namespace AnyContent\CMCK\Modules\Backend\Edit\NumberFormElement;

class FormElementNumber extends \AnyContent\CMCK\Modules\Backend\Core\Edit\FormElementDefault
{

    public function render($layout)
    {
        $outputFormatter = $this->getNumberFormatter();

        $leftAddOn  = '#';
        $rightAddOn = null;

        if ($this->definition->getUnit())
        {
            $leftAddOn  = null;
            $rightAddOn = $this->definition->getUnit();
        }

        $this->vars['leftAddOn']  = $leftAddOn;
        $this->vars['rightAddOn'] = $rightAddOn;

        $this->vars['value'] = $outputFormatter->format($this->vars['value']);

        return $this->twig->render('formelement-number.twig', $this->vars);
    }


    public function parseFormInput($input)
    {
        $value = $this->getNumberFormatter()->parse($input);
        $value = number_format($value, $this->definition->getDigits());

        return $value;
    }


    protected function getNumberFormatter()
    {
        $numberFormatter = new \NumberFormatter(\Locale::getDefault(), \NumberFormatter::DECIMAL);
        $numberFormatter->setAttribute(\NumberFormatter::MIN_FRACTION_DIGITS, $this->definition->getDigits());
        $numberFormatter->setAttribute(\NumberFormatter::MAX_FRACTION_DIGITS, $this->definition->getDigits());

        return $numberFormatter;
    }
}