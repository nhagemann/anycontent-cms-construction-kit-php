<?php

namespace AnyContent\CMCK\Modules\Backend\Core\Config;

use AnyContent\CMCK\Modules\Backend\Core\Application\Application;

use CMDL\ContentTypeDefinition;
use CMDL\ViewDefinition;

use AnyContent\Client\Repository;
use AnyContent\Client\Record;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class Controller
{

    public static function editConfig(Application $app, $configTypeAccessHash)
    {
        $vars = array();

        $vars['menu_mainmenu'] = $app['menus']->renderMainMenu();

        /** @var Repository $repository */
        $repository = $app['repos']->getRepositoryByConfigTypeAccessHash($configTypeAccessHash);

        $configTypeDefinition = $app['repos']->getConfigTypeDefinitionByConfigTypeAccessHash($configTypeAccessHash);

        if ($repository)
        {
            $app['context']->setCurrentRepository($repository);
            $app['context']->setCurrentConfigType($configTypeDefinition);

            $app['form']->setDataTypeDefinition($configTypeDefinition);

            /** @var Config $record */
            $record = $repository->getConfig($configTypeDefinition->getName(),$app['context']->getCurrentWorkspace(), $app['context']->getCurrentLanguage(),$app['context']->getCurrentTimeShift());

            //$app['layout']->addCssFile('listing.css');
            $app['layout']->addJsFile('editrecord.js');

            if ($record)
            {
                $app['context']->setCurrentConfig($record);
                $vars['record'] = $record;

                $vars['definition'] = $configTypeDefinition;

                /* @var ViewDefinition */
                $viewDefinition = $configTypeDefinition->getViewDefinition('default');

                $vars['form'] = $app['form']->renderFormElements('form_edit', $viewDefinition->getFormElementDefinitions(), $record->getProperties());

                $vars['links']['timeshift']  = $app['url_generator']->generate('timeShiftEditConfig', array( 'configTypeAccessHash' => $configTypeAccessHash ));
                $vars['links']['workspaces'] = $app['url_generator']->generate('changeWorkspaceEditConfig', array( 'configTypeAccessHash' => $configTypeAccessHash ));
                $vars['links']['languages']  = $app['url_generator']->generate('changeLanguageEditConfig', array( 'configTypeAccessHash' => $configTypeAccessHash ));

                return $app->renderPage('editconfig.twig', $vars);

            }
        }

        return $app->renderPage('config-not-found.twig', $vars);

    }


    public static function saveConfig(Application $app, Request $request, $configTypeAccessHash)
    {

        /** @var Repository $repository */
        $repository = $app['repos']->getRepositoryByConfigTypeAccessHash($configTypeAccessHash);

        $configTypeDefinition = $app['repos']->getConfigTypeDefinitionByConfigTypeAccessHash($configTypeAccessHash);

        if ($repository)
        {
            $app['context']->setCurrentRepository($repository);
            $app['context']->setCurrentConfigType($configTypeDefinition);

            $app['form']->setDataTypeDefinition($configTypeDefinition);

            /** @var Config $record */
            $record = $repository->getConfig($configTypeDefinition->getName(),$app['context']->getCurrentWorkspace(), $app['context']->getCurrentLanguage(),$app['context']->getCurrentTimeShift());


            if ($record)
            {
                $app['context']->setCurrentConfig($record);
                /* @var ViewDefinition */
                $viewDefinition = $configTypeDefinition->getViewDefinition('default');

                $values = $app['form']->extractFormElementValuesFromPostRequest($request, $viewDefinition->getFormElementDefinitions(), $record->getProperties());

                foreach ($values as $property => $value)
                {
                    $record->setProperty($property, $value);
                }

                $result = $repository->saveConfig($record, $app['context']->getCurrentWorkspace(), $app['context']->getCurrentLanguage());

                $app['context']->resetTimeShift();
                if ($result)
                {
                    $app['context']->addSuccessMessage('Config saved.');
                }
                else
                {
                    $app['context']->addErrorMessage('Could not save config.');

                }

                return new RedirectResponse($app['url_generator']->generate('editConfig', array( 'configTypeAccessHash' => $configTypeAccessHash )), 303);

            }
            else
            {
                return $app['layout']->render('config-notfound.twig');
            }
        }
    }

}