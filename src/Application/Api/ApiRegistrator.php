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

    private $prototype;
    private $getPrototype;
    private $postPrototype;
    private $putPrototype;
    private $deletePrototype;

    public function __construct(MethodEnum $methodEnum, UrlPatternValidator $urlPatternValidator) {
        $this->prototype = new Resource($methodEnum, $urlPatternValidator);
        $this->getPrototype = $this->prototype->withHttpMethod('GET');
        $this->postPrototype = $this->prototype->withHttpMethod('POST');
    }

    public function registerApi(ResourceRegistryInterface $registry): void {

        ### auth ###

        $registry->register($this->postPrototype->withUrlPattern('/auth/v1/logout'));
        $registry->register($this->postPrototype->withUrlPattern('/auth/v1/login'));

        ### www ###

        $registry->register($this->getPrototype->withUrlPattern('/www/last'));
        $registry->register($this->getPrototype->withUrlPattern('/www/item/:langCode/:uid'));
        $registry->register($this->getPrototype->withUrlPattern('/www/searchresult'));
        $registry->register($this->getPrototype->withUrlPattern('/'));

        ### api ###
        #### UserActionController ####
        $registry->register($this->getPrototype->withUrlPattern('/api/v1/useraction/app/:app'));

        #### PresentationController ####
        $registry->register($this->postPrototype->withUrlPattern('/api/v1/presentation/language'));
        $registry->register($this->postPrototype->withUrlPattern('/api/v1/presentation/uid'));
        $registry->register($this->postPrototype->withUrlPattern('/api/v1/presentation/edit_layout'));
        $registry->register($this->postPrototype->withUrlPattern('/api/v1/presentation/edit_article'));

        #### PaperController ####
        $registry->register($this->postPrototype->withUrlPattern('/api/v1/paper'));
        $registry->register($this->postPrototype->withUrlPattern('/api/v1/paper/:paperId/headline'));
        $registry->register($this->postPrototype->withUrlPattern('/api/v1/paper/:paperId/perex'));
        $registry->register($this->postPrototype->withUrlPattern('/api/v1/paper/:paperId/contents'));
        $registry->register($this->postPrototype->withUrlPattern('/api/v1/paper/:paperId/contents/:contentId'));
        $registry->register($this->postPrototype->withUrlPattern('/api/v1/paper/:paperId/contents/:contentId/toggle'));
        $registry->register($this->postPrototype->withUrlPattern('/api/v1/paper/:paperId/contents/:contentId/actual'));
        $registry->register($this->postPrototype->withUrlPattern('/api/v1/paper/:paperId/contents/:contentId/up'));
        $registry->register($this->postPrototype->withUrlPattern('/api/v1/paper/:paperId/contents/:contentId/down'));
        $registry->register($this->postPrototype->withUrlPattern('/api/v1/paper/:paperId/contents/:contentId/add_above'));
        $registry->register($this->postPrototype->withUrlPattern('/api/v1/paper/:paperId/contents/:contentId/add_below'));
        $registry->register($this->postPrototype->withUrlPattern('/api/v1/paper/:paperId/contents/:contentId/trash'));
        $registry->register($this->postPrototype->withUrlPattern('/api/v1/paper/:paperId/contents/:contentId/restore'));
        $registry->register($this->postPrototype->withUrlPattern('/api/v1/paper/:paperId/contents/:contentId/delete'));

        #### EditItemController ####
        $registry->register($this->postPrototype->withUrlPattern('/api/v1/menu'));
        $registry->register($this->postPrototype->withUrlPattern('/api/v1/menu/:menuItemUidFk/toggle'));
        $registry->register($this->postPrototype->withUrlPattern('/api/v1/menu/:menuItemUidFk/title'));
        $registry->register($this->postPrototype->withUrlPattern('/api/v1/menu/:menuItemUidFk/type'));
        $registry->register($this->postPrototype->withUrlPattern('/api/v1/menu/:menuItemUidFk/cut'));

        #### HierarchyController ####
        $registry->register($this->postPrototype->withUrlPattern('/api/v1/hierarchy/:uid/add'));
        $registry->register($this->postPrototype->withUrlPattern('/api/v1/hierarchy/:uid/addchild'));
        $registry->register($this->postPrototype->withUrlPattern('/api/v1/hierarchy/:uid/paste/:pasteduid'));
        $registry->register($this->postPrototype->withUrlPattern('/api/v1/hierarchy/:uid/pastechild/:pasteduid'));
        $registry->register($this->postPrototype->withUrlPattern('/api/v1/hierarchy/:uid/delete'));
        $registry->register($this->postPrototype->withUrlPattern('/api/v1/hierarchy/:uid/trash'));

        ### component ###
        #### ComponentController ####
        $registry->register($this->getPrototype->withUrlPattern('/component/namedpaper/:name'));
        $registry->register($this->getPrototype->withUrlPattern('/component/presentedpaper'));
    }
}
