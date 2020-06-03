<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Api;

use Pes\Router\MethodEnum;
use Pes\Router\UrlPatternValidator;

use Pes\Router\Resource\Resource;
use Pes\Router\Resource\ResourceRegistryInterface;

/**
 * Description of ApiFactory
 *
 * @author pes2704
 */
class ApiRegistrator {

    private $resourcePrototype;

    public function __construct(MethodEnum $methodEnum, UrlPatternValidator $urlPatternValidator) {
        $this->resourcePrototype = new Resource($methodEnum, $urlPatternValidator);
    }

    public function registerApi(ResourceRegistryInterface $registry): void {

        ### auth ###

        $registry->register($this->resourcePrototype->withHttpMethod('POST')->withUrlPattern('/auth/v1/logout'));
        $registry->register($this->resourcePrototype->withHttpMethod('POST')->withUrlPattern('/auth/v1/login'));

        ### www ###

        $registry->register($this->resourcePrototype->withHttpMethod('GET')->withUrlPattern('/www/last'));
        $registry->register($this->resourcePrototype->withHttpMethod('GET')->withUrlPattern('/www/item/:langCode/:uid'));
        $registry->register($this->resourcePrototype->withHttpMethod('GET')->withUrlPattern('/www/searchresult'));
        $registry->register($this->resourcePrototype->withHttpMethod('GET')->withUrlPattern('/'));

        ### api ###
        #### UserActionController ####
        $registry->register($this->resourcePrototype->withHttpMethod('GET')->withUrlPattern('/api/v1/useraction/app/:app'));

        #### PresentationController ####
        $registry->register($this->resourcePrototype->withHttpMethod('POST')->withUrlPattern('/api/v1/presentation/language'));
        $registry->register($this->resourcePrototype->withHttpMethod('POST')->withUrlPattern('/api/v1/presentation/uid'));
        $registry->register($this->resourcePrototype->withHttpMethod('POST')->withUrlPattern('/api/v1/useraction/edit_layout'));
        $registry->register($this->resourcePrototype->withHttpMethod('POST')->withUrlPattern('/api/v1/useraction/edit_article'));

        #### PaperController ####
        $registry->register($this->resourcePrototype->withHttpMethod('POST')->withUrlPattern('/api/v1/paper/:menuItemId/headline'));
        $registry->register($this->resourcePrototype->withHttpMethod('POST')->withUrlPattern('/api/v1/paper/:menuItemId/content/:id'));

        #### EditItemController ####
        $registry->register($this->resourcePrototype->withHttpMethod('POST')->withUrlPattern('/api/v1/menu/:menuItemId/toggle'));
        $registry->register($this->resourcePrototype->withHttpMethod('POST')->withUrlPattern('/api/v1/menu/:menuItemId/actual'));
        $registry->register($this->resourcePrototype->withHttpMethod('POST')->withUrlPattern('/api/v1/menu/:menuItemId/title'));

        #### HierarchyController ####
        $registry->register($this->resourcePrototype->withHttpMethod('POST')->withUrlPattern('/api/v1/hierarchy/:uid/add'));
        $registry->register($this->resourcePrototype->withHttpMethod('POST')->withUrlPattern('/api/v1/hierarchy/:uid/addchild'));
        $registry->register($this->resourcePrototype->withHttpMethod('POST')->withUrlPattern('/api/v1/hierarchy/:uid/delete'));
        $registry->register($this->resourcePrototype->withHttpMethod('POST')->withUrlPattern('/api/v1/hierarchy/:uid/trash'));
        $registry->register($this->resourcePrototype->withHttpMethod('POST')->withUrlPattern('/api/v1/hierarchy/:uid/move/:parentUid'));
    }
}
