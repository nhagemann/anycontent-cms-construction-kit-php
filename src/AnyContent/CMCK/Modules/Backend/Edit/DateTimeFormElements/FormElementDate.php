<?php

namespace AnyContent\CMCK\Modules\Backend\Edit\DateTimeFormElements;

use AnyContent\CMCK\Modules\Backend\Core\Edit\FormElementDefault;
use CMDL\FormElementDefinition;

class FormElementDate extends \AnyContent\CMCK\Modules\Backend\Core\Edit\FormElementDefault
{

    public function render($layout)
    {
        $layout->addJsFile('fe-datetime.js');

        $this->vars['month']='';
        $this->vars['day']='';

        //var_dump($this->getOption('Format.Long'));
        //var_dump($this->definition->getType());
        //var_dump($this->definition->getInit());
        //var_dump($this->value);
        //short
        //long
        //datetime
        // ISO 8601
        // pulldownlösung bei short

        if ($this->getValue() != null)
        {

            try
            {

                $date  = null;
                $value = trim($this->getValue());

                //var_dump($value);
                if (strlen($value) <= 5)
                {

                    $value = date('Y') . '-' . $value;
                }
                if (strlen($value) <= 10)
                {

                    $value .= ' 00:00';
                }

                //var_dump($value);
                //$date = new \DateTime();
                //$t    = strtotime($value);
                //$date->setTimestamp($t);

                $date = \DateTime::createFromFormat('Y-m-d h:i', $value);

                switch ($this->definition->getType())
                {
                    case 'short':
                        $this->vars['day']   = date('d', $date->getTimestamp());
                        $this->vars['month'] = date('n', $date->getTimestamp());

                        break;
                    case 'datetime':
                        $format = $this->getOption('Format.DateTime.Frontend');
                        break;
                    case 'full':
                        $format = $this->getOption('Format.Full.Frontend');
                        break;
                    case 'long':
                    default:


                        break;
                }

                if ($date)
                {

                    $this->vars['value'] = $date->format($this->getPHPConvertFormat());
                       //var_dump($this->vars['value']);
                }

            }
            catch (\Exception $e)
            {
                $this->vars['value'] = '';
            }

        }

        $this->vars['type']   = $this->definition->getType();
        $this->vars['format'] = $this->getFrontendFormat();

        return $this->twig->render('formelement-datetime.twig', $this->vars);
    }


    protected function getFrontendFormat()
    {
        switch ($this->definition->getType())
        {
            case 'short':
                $format = $this->getOption('Format.Short.Frontend');
                break;
            case 'datetime':
                $format = $this->getOption('Format.DateTime.Frontend');
                break;
            case 'full':
                $format = $this->getOption('Format.Full.Frontend');
                break;
            case 'long':
            default:
                $format = $this->getOption('Format.Long.Frontend');
                break;
        }
        return $format;
    }


    protected function getPHPConvertFormat()
    {
        switch ($this->definition->getType())
        {
            case 'short':
                $format = $this->getOption('Format.Short.PHPConvert');
                break;
            case 'datetime':
                $format = $this->getOption('Format.DateTime.PHPConvert');
                break;
            case 'full':
                $format = $this->getOption('Format.Full.PHPConvert');
                break;
            case 'long':
            default:
                $format = $this->getOption('Format.Long.PHPConvert');
                break;
        }
        return $format;
    }


    public function parseFormInput($input)
    {

        $value = '';
        try
        {
            switch ($this->definition->getType())
            {
                case 'short':
                    if (is_array($input) AND count($input) == 2)
                    {
                        $value = $input[1] . '-' . $input[0];
                    }
                    break;
                case 'datetime':
                    break;
                case 'full':

                    break;
                case 'long':
                default:
                    $date = \DateTime::createFromFormat($this->getOption('Format.Long.PHPConvert'), $input);
                    if ($date)
                    {
                        $value = $date->format('Y-m-d');
                    }
                    break;
            }
        }
        catch (\Exception $e)
        {
            $value = null;
        }

        //var_dump($value);
        //die();
        return $value;
    }
}