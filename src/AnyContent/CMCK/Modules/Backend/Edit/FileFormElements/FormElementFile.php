<?php

namespace AnyContent\CMCK\Modules\Backend\Edit\FileFormElements;

class FormElementFile extends \AnyContent\CMCK\Modules\Backend\Core\Edit\FormElementDefault
{

    protected $template = 'formelement-file.twig';


    public function render($layout)
    {
        $layout->addJsFile('fe-file.js');

        //$this->vars['types'] = $this->definition->getFileTypes();

        $info = pathinfo($this->getValue());

        if (isset($info['dirname']))
        {
            $path = $info['dirname'];
        }
        else
        {
            $path = $this->definition->getPath();
        }

        $path = '/' . trim($path, '/');

        $this->vars['url_modal'] = $this->app['url_generator']->generate('formElementFileModal', array( 'repositoryAccessHash' => $this->getCurrentRepositoryAccessHash(), 'path' => $path ));

        $this->vars['url_view'] = rtrim($this->app['url_generator']->generate('viewFile', array( 'repositoryAccessHash' =>  $this->getCurrentRepositoryAccessHash(), 'id' => '/' )),'/');

        $this->vars['url_view'] = rtrim($this->app['url_generator']->generate('viewFile', array( 'repositoryAccessHash' =>  $this->getCurrentRepositoryAccessHash(), 'id' => '/Agencies/Proofs/2014/12/397bdd5f-de89-e411-828a-005056bd1c15.pdf' )),'/');

        $this->vars['url_download'] = rtrim($this->app['url_generator']->generate('downloadFile', array( 'repositoryAccessHash' =>  $this->getCurrentRepositoryAccessHash(), 'id' => '/' )),'/');

        return $this->twig->render($this->template, $this->vars);
    }




}