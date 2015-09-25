<?php

namespace AnyContent\CMCK\Modules\Backend\Core\Listing;

use AnyContent\Client\Repository;
use AnyContent\CMCK\Modules\Backend\Core\Application\Application;
use AnyContent\CMCK\Modules\Backend\Core\Layout\LayoutManager;
use CMDL\Annotations\CustomAnnotation;
use CMDL\ContentTypeDefinition;
use Symfony\Component\Routing\Generator\UrlGenerator;

class BaseContentView
{

    protected $nr;

    /**
     * @var Application
     */
    protected $app;

    /**
     * @var Repository
     */

    protected $repository;
    /**
     * @var ContentTypeDefinition
     */
    protected $contentTypeDefinition;

    /**
     * @var string
     */
    protected $contentTypeAccessHash;
    /**
     * @var CustomAnnotation
     */
    protected $customAnnotation;


    /**
     * @return UrlGenerator
     */
    protected function getUrlGenerator()
    {
        return $this->app['url_generator'];
    }


    /**
     * @return LayoutManager
     */
    protected function getLayout()
    {
        return $this->app['layout'];
    }


    /**
     * @return Repository
     */
    public function getRepository()
    {
        return $this->repository;
    }


    /**
     * @return ContentTypeDefinition
     */
    public function getContentTypeDefinition()
    {
        return $this->contentTypeDefinition;
    }


    /**
     * @return string
     */
    public function getContentTypeAccessHash()
    {
        return $this->contentTypeAccessHash;
    }


    public function __construct($nr, Application $app, Repository $repository, ContentTypeDefinition $contentTypeDefinition, $contentTypeAccessHash, CustomAnnotation $customAnnotation = null)
    {
        $this->nr                    = $nr;
        $this->app                   = $app;
        $this->repository            = $repository;
        $this->contentTypeDefinition = $contentTypeDefinition;
        $this->contentTypeAccessHash = $contentTypeAccessHash;
        $this->customAnnotation      = $customAnnotation;
    }


    public function getUrl()
    {
        return $this->getUrlGenerator()
                    ->generate('listRecords', array( 'contentTypeAccessHash' => $this->contentTypeAccessHash, 'nr' => $this->nr ));
    }


    public function getTitle()
    {
        return 'List';
    }


    public function getTemplate()
    {
        return 'template.twig';
    }


    public function doesProcessSearch()
    {
        return false;
    }


    public function apply($vars)
    {
        return $vars;
    }
}