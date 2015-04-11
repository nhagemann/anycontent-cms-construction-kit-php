<?php

namespace AnyContent\CMCK\Modules\Backend\Core\Listing;

use AnyContent\Client\ContentFilter;
use AnyContent\CMCK\Modules\Backend\Core\Application\Application;
use CMDL\ContentTypeDefinition;
use CMDL\Util;

class FilterUtil
{

    public static function normalizeFilterQuery(Application $app, $query, ContentTypeDefinition $contentTypeDefinition)
    {

        $query = self::escape($query);

        $filter = new ContentFilter($contentTypeDefinition);

        $blocks = explode('+', $query);

        foreach ($blocks as $block)
        {
            $filter->nextConditionsBlock();

            $conditions = explode(',', $block);
            foreach ($conditions as $conditionString)
            {

                $condition = self::parseCondition($conditionString);
                if (is_array($condition) && count($condition) == 3)
                {
                    $property = Util::generateValidIdentifier($condition[0]);
                    if ($contentTypeDefinition->hasProperty($property))
                    {
                        $filter->addCondition($property, $condition[1], $condition[2]);
                    }
                    else
                    {

                        $app['context']->addAlertMessage('Cannot filter by property ' . $property . '. Query has been adjusted.');
                    }

                }
                else
                {

                    $filter->addCondition('name', '><', trim($conditionString));

                }
            }
        }



        return $filter;
    }


    protected static function escape($s)
    {
        $s = str_replace('\\+', '&#43;', $s);
        $s = str_replace('\\,', '&#44;', $s);
        $s = str_replace('\\=', '&#61;', $s);

        return $s;
    }


    protected static function decode($s)
    {
        $s = str_replace('&#43;', '+', $s);
        $s = str_replace('&#44;', ',', $s);
        $s = str_replace('&#61;', '=', $s);

        // remove surrounding quotes
        if (substr($s, 0, 1) == '"')
        {

            $s = trim($s, '"');
        }
        else
        {

            $s = trim($s, "'");
        }

        return $s;
    }


    /**
     * http://stackoverflow.com/questions/4955433/php-multiple-delimiters-in-explode
     *
     * @param $s
     *
     * @return bool
     */
    protected static function parseCondition($s)
    {

        $match = preg_match("/([^>=|<=|<>|><|>|<|=)]*)(>=|<=|<>|><|>|<|=)(.*)/", $s, $matches);

        if ($match)
        {
            $condition   = array();
            $condition[] = self::decode(trim($matches[1]));
            $condition[] = trim($matches[2]);
            $condition[] = self::decode(trim($matches[3]));

            return $condition;
        }

        return false;
    }
}