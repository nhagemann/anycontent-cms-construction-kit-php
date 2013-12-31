<?php

namespace Anycontent\CMCK\Modules\Core\Menu;

use Knp\Menu\ItemInterface;
use Knp\Menu\Matcher\MatcherInterface;

use Knp\Menu\Renderer\RendererInterface;

/**
 * Renders MenuItem tree as unordered list
 */
class NavBarListRenderer extends \Knp\Menu\Renderer\ListRenderer implements RendererInterface
{

    public function render(ItemInterface $item, array $options = array())
    {
        $options = array_merge($this->defaultOptions, $options);

        $attributes = array('class'=>'dropdown-menu');

        $html = $this->renderList($item, $attributes, $options);

        if ($options['clear_matcher']) {
            $this->matcher->clear();
        }

        return $html;
    }


    protected function renderSpanElement(ItemInterface $item, array $options)
    {
        return sprintf('%s', $this->renderLabel($item, $options));
    }


    /**
     * don't render class-attribute, if no class is given
     * don't add menu_level class
     */
    protected function renderItem(ItemInterface $item, array $options)
    {
        // if we don't have access or this item is marked to not be shown
        if (!$item->isDisplayed())
        {
            return '';
        }

        // create an array than can be imploded as a class list
        $class = (array)$item->getAttribute('class');

        if ($this->matcher->isCurrent($item))
        {
            $class[] = $options['currentClass'];
        }
        elseif ($this->matcher->isAncestor($item, $options['depth']))
        {
            $class[] = $options['ancestorClass'];
        }

        if ($item->actsLikeFirst())
        {
            $class[] = $options['firstClass'];
        }
        if ($item->actsLikeLast())
        {
            $class[] = $options['lastClass'];
        }

        // retrieve the attributes and put the final class string back on it
        $attributes = $item->getAttributes();
        if (!empty($class))
        {
            $attributes['class'] = implode(' ', $class);
        }

        if ($attributes['class'] == '')
        {
            unset ($attributes['class']);
        }

        // opening li tag
        $html = $this->format('<li' . $this->renderHtmlAttributes($attributes) . '>', 'li', $item->getLevel(), $options);

        // render the text/link inside the li tag
        //$html .= $this->format($item->getUri() ? $item->renderLink() : $item->renderLabel(), 'link', $item->getLevel());
        $html .= $this->renderLink($item, $options);

        // renders the embedded ul
        $childrenClass = (array)$item->getChildrenAttribute('class');

        $childrenAttributes          = $item->getChildrenAttributes();
        $childrenAttributes['class'] = implode(' ', $childrenClass);

        if ($childrenAttributes['class'] == '')
        {
            unset ($childrenAttributes['class']);
        }
        $html .= $this->renderList($item, $childrenAttributes, $options);

        // closing li tag
        $html .= $this->format('</li>', 'li', $item->getLevel(), $options);

        return $html;
    }
}