<?php

namespace AnyContent\CMCK\Modules\Edit\SequenceFormElement;

use AnyContent\CMCK\Modules\Core\Application\Application;

use CMDL\ContentTypeDefinition;
use CMDL\ClippingDefinition;
use CMDL\FormElementDefinition;
use CMDL\InsertionDefinition;

use AnyContent\Client\Repository;
use AnyContent\Client\Record;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class Controller
{

    public static function editSequence(Application $app, $contentTypeAccessHash, $recordId, $property)
    {
        $vars                     = array();
        $vars['action']['submit'] = $app['url_generator']->generate('postSequence', array( 'contentTypeAccessHash' => $contentTypeAccessHash, 'recordId' => $recordId, 'property' => $property ));
        $vars['action']['add']    = $app['url_generator']->generate('addSequenceItem', array( 'contentTypeAccessHash' => $contentTypeAccessHash, 'property' => $property ));
        $vars['property']         = $property;

        /** @var Repository $repository */
        $repository = $app['repos']->getRepositoryByContentTypeAccessHash($contentTypeAccessHash);

        if ($repository)
        {
            /** @var ContentTypeDefinition $contentTypeDefinition */
            $contentTypeDefinition = $repository->getContentTypeDefinition();

            $vars['definition'] = $contentTypeDefinition;

            $app['context']->setCurrentContentType($contentTypeDefinition);

            /* @var ClippingDefinition */
            $clippingDefinition = $contentTypeDefinition->getClippingDefinition('default');

            if ($clippingDefinition->hasProperty($property))
            {
                /** @var FormElementDefinition $formElementDefinition */
                $formElementDefinition = $clippingDefinition->getFormElementDefinition($property);

                if ($formElementDefinition->getFormElementType() == 'sequence')
                {
                    $sequence = array();

                    if ($recordId)
                    {
                        /** @var Record $record */
                        $record   = $repository->getRecord($recordId, $app['context']->getCurrentWorkspace(), 'default', $app['context']->getCurrentLanguage(), $app['context']->getCurrentTimeShift());
                        $sequence = $record->getProperty($property, array());

                        $sequence = json_decode($sequence, true);
                        if (json_last_error() != JSON_ERROR_NONE OR !is_array($sequence))
                        {
                            $sequence = array();
                        }
                    }

                    $vars['count'] = count($sequence);
                    $vars['items'] = array();

                    $inserts = $formElementDefinition->getInserts();
                    $vars['inserts'] =  $inserts;

                    $i = 0;
                    foreach ($sequence as $item)
                    {
                        $i++;
                        $insert  = key($item);
                        $properties = array_shift($item);
                        /** @var InsertionDefinition $insertionDefinition */
                        $insertionDefinition = $contentTypeDefinition->getInsertionDefinition($insert);
                        $item                = array();
                        $item['form']        = $app['form']->renderFormElements('form_sequence', $insertionDefinition->getFormElementDefinitions(), $properties, 'item_' . $i);
                        $item['type']        = $insert;
                        $item['title']       = $inserts[$insert];
                        $item['sequence']    = $i;
                        $vars['items'][]     = $item;

                    }

                    $app['layout']->addJsFile('editsequence.js');
                    $app['layout']->addCssFile('editsequence.css');

                    return $app->renderPage('editsequence.twig', $vars);
                }
            }

        }

        return $app->renderPage('record-not-found.twig', $vars);

    }


    public static function postSequence(Application $app, Request $request, $contentTypeAccessHash, $recordId, $property)
    {

        $items = array();
        foreach ($request->request->getIterator() as $key => $value)
        {
            $split = explode('_', $key);

            if (count($split >= 3))
            {

                if ($split[0] == 'item')
                {
                    $nr                    = (int)($split[1]);
                    $l                     = strlen('item_' . $nr);
                    $property              = substr($key, $l + 1);
                    $items[$nr][$property] = $value;

                }
            }

        }
        $sequence = array();
        if ($request->request->has('sequence'))
        {
            $types = $request->get('type');
            $i     = 0;
            foreach ($request->get('sequence') as $nr)
            {
                $item       = $items[$nr];
                $type       = $types[$i];
                $sequence[] = array( $type => $item );
                $i++;
            }
        }

        return $app->json(array( 'sequence' => $sequence ));

    }


    public static function addSequenceItem(Application $app, Request $request, $contentTypeAccessHash, $property)
    {
        /** @var Repository $repository */
        $repository = $app['repos']->getRepositoryByContentTypeAccessHash($contentTypeAccessHash);

        if ($repository)
        {
            /** @var ContentTypeDefinition $contentTypeDefinition */
            $contentTypeDefinition = $repository->getContentTypeDefinition();

            $app['context']->setCurrentContentType($contentTypeDefinition);

            /* @var ClippingDefinition */
            $clippingDefinition = $contentTypeDefinition->getClippingDefinition('default');

            if ($clippingDefinition->hasProperty($property))
            {
                /** @var FormElementDefinition $formElementDefinition */
                $formElementDefinition = $clippingDefinition->getFormElementDefinition($property);

                if ($formElementDefinition->getFormElementType() == 'sequence')
                {
                    if ($request->query->has('insert') AND $request->query->has('count'))
                    {
                        $inserts = $formElementDefinition->getInserts();
                        $insert = $request->query->get('insert');
                        $count     = $request->query->get('count');

                        $insertionDefinition = $contentTypeDefinition->getInsertionDefinition($insert);
                        $item                = array();
                        $item['form']        = $app['form']->renderFormElements('form_sequence', $insertionDefinition->getFormElementDefinitions(), array(), 'item_' . $count);
                        $item['type']        = $insert;
                        $item['sequence']    = $count;
                        $item['title']       = $inserts[$insert];
                        $vars['item']        = $item;

                        $vars['inserts'] = $formElementDefinition->getInserts();

                        return $app['layout']->render('editsequence-additem.twig', $vars);
                    }
                }
            }
        }

        return '';
    }

}