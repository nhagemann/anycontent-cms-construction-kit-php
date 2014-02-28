<?php

namespace AnyContent\CMCK\Modules\Backend\Edit\SequenceFormElement;

use AnyContent\CMCK\Modules\Backend\Core\Application\Application;

use CMDL\ContentTypeDefinition;
use CMDL\ClippingDefinition;
use CMDL\FormElementDefinition;
use CMDL\InsertionDefinition;

use AnyContent\Client\Repository;
use AnyContent\Client\Record;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ParameterBag;

class Controller
{

    public static function editSequence(Application $app, Request $request, $contentTypeAccessHash, $recordId, $clippingName, $insertName, $property)
    {
        $vars                     = array();
        $vars['action']['submit'] = $app['url_generator']->generate('postSequence', array( 'contentTypeAccessHash' => $contentTypeAccessHash, 'clippingName' => 'default', 'insertName' => $insertName, 'recordId' => $recordId, 'property' => $property ));
        $vars['action']['add']    = $app['url_generator']->generate('addSequenceItem', array( 'contentTypeAccessHash' => $contentTypeAccessHash, 'clippingName' => 'default', 'insertName' => $insertName, 'property' => $property ));

        $vars['property'] = $property;

        /** @var Repository $repository */
        $repository = $app['repos']->getRepositoryByContentTypeAccessHash($contentTypeAccessHash);

        if ($repository)
        {
            $app['context']->setCurrentRepository($repository);
            $app['context']->setCurrentContentType($repository->getContentTypeDefinition());

            /** @var ContentTypeDefinition $contentTypeDefinition */
            $contentTypeDefinition = $repository->getContentTypeDefinition();

            $app['context']->setCurrentRepository($repository);
            $app['context']->setCurrentContentType($repository->getContentTypeDefinition());

            $vars['definition'] = $contentTypeDefinition;

            $app['context']->setCurrentContentType($contentTypeDefinition);

            $formElementDefinition = self::getFormElementDefinition($request, $contentTypeDefinition, $insertName, $property);

            if ($formElementDefinition)
            {

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

                    $vars['inserts'] = $inserts;

                    // silently render all potential inserts to add their Javascript-Files to the Layout
                    foreach ($inserts as $k => $v)
                    {

                        $insertionDefinition = $contentTypeDefinition->getInsertionDefinition($k);
                        $app['form']->renderFormElements('form_sequence', $insertionDefinition->getFormElementDefinitions(), array(), null);
                    }

                    $i = 0;
                    foreach ($sequence as $item)
                    {
                        $insert     = key($item);
                        $properties = array_shift($item);

                        if ($contentTypeDefinition->hasInsertionDefinition($insert)) // ignore eventually junk data after cmdl changes
                        {
                            $i++;

                            /** @var InsertionDefinition $insertionDefinition */
                            $insertionDefinition = $contentTypeDefinition->getInsertionDefinition($insert);
                            $item                = array();
                            $item['form']        = $app['form']->renderFormElements('form_sequence', $insertionDefinition->getFormElementDefinitions(), $properties, 'item_' . $i);
                            $item['type']        = $insert;
                            $item['title']       = $inserts[$insert];
                            $item['sequence']    = $i;
                            $vars['items'][]     = $item;
                        }

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

        /** @var Repository $repository */
        $repository = $app['repos']->getRepositoryByContentTypeAccessHash($contentTypeAccessHash);

        if ($repository)
        {
            $app['context']->setCurrentRepository($repository);
            $app['context']->setCurrentContentType($repository->getContentTypeDefinition());

            /** @var ContentTypeDefinition $contentTypeDefinition */
            $contentTypeDefinition = $repository->getContentTypeDefinition();

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
                    $item = $items[$nr];
                    $type = $types[$i];

                    $insertionDefinition = $contentTypeDefinition->getInsertionDefinition($type);

                    $bag = new ParameterBag();
                    $bag->add($item);

                    $item = $app['form']->extractFormElementValuesFromPostRequest($bag, $insertionDefinition->getFormElementDefinitions(), array());

                    $sequence[] = array( $type => $item );
                    $i++;
                }
            }

            return $app->json(array( 'sequence' => $sequence ));
        }

    }


    public static function addSequenceItem(Application $app, Request $request, $contentTypeAccessHash, $insertName, $property)
    {
        /** @var Repository $repository */
        $repository = $app['repos']->getRepositoryByContentTypeAccessHash($contentTypeAccessHash);

        if ($repository)
        {
            $app['context']->setCurrentRepository($repository);
            $app['context']->setCurrentContentType($repository->getContentTypeDefinition());

            /** @var ContentTypeDefinition $contentTypeDefinition */
            $contentTypeDefinition = $repository->getContentTypeDefinition();

            $app['context']->setCurrentContentType($contentTypeDefinition);

            $formElementDefinition = self::getFormElementDefinition($request, $contentTypeDefinition, $insertName, $property);

            if ($formElementDefinition)
            {
                if ($formElementDefinition->getFormElementType() == 'sequence')
                {
                    if ($request->query->has('insert') AND $request->query->has('count'))
                    {
                        $inserts = $formElementDefinition->getInserts();
                        $insert  = $request->query->get('insert');
                        $count   = $request->query->get('count');

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


    protected static function getFormElementDefinition(Request $request, $contentTypeDefinition, $insertName, $property)
    {
        $formElementDefinition = null;
        if ($insertName != '-')
        {
            $insertionDefinition   = $contentTypeDefinition->getInsertionDefinition($insertName);
            $formElementDefinition = $insertionDefinition->getFormElementDefinition($property);
            $formElementDefinition->setInsertedByInsert($insertName);

        }
        else
        {
            /* @var ClippingDefinition */
            $clippingDefinition = $contentTypeDefinition->getClippingDefinition('default');
            if ($clippingDefinition->hasProperty($property))
            {

                /** @var FormElementDefinition $formElementDefinition */
                $formElementDefinition = $clippingDefinition->getFormElementDefinition($property);
            }
        }

        return $formElementDefinition;
    }
}