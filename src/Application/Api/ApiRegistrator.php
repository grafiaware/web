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
        $registry->register($this->postPrototype->withUrlPattern('/auth/v1/register'));
        $registry->register($this->getPrototype->withUrlPattern('/auth/v1/registerapplication/:loginname'));
        $registry->register($this->postPrototype->withUrlPattern('/auth/v1/register1'));
        $registry->register($this->getPrototype->withUrlPattern('/auth/v1/confirm/:uid'));
        $registry->register($this->postPrototype->withUrlPattern('/auth/v1/forgottenpassword'));
        $registry->register($this->postPrototype->withUrlPattern('/auth/v1/changepassword'));

        ### www ###

        $registry->register($this->getPrototype->withUrlPattern('/www/item/:langCode/:uid'));
        $registry->register($this->getPrototype->withUrlPattern('/www/subitem/:langCode/:uid'));
        $registry->register($this->getPrototype->withUrlPattern('/www/block/:name'));
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
        $registry->register($this->postPrototype->withUrlPattern('/api/v1/presentation/edit_menu'));

        #### PaperController ####
        $registry->register($this->postPrototype->withUrlPattern('/api/v1/paper'));
        $registry->register($this->postPrototype->withUrlPattern('/api/v1/paper/:paperId/template'));
        $registry->register($this->postPrototype->withUrlPattern('/api/v1/paper/:paperId/headline'));
        $registry->register($this->postPrototype->withUrlPattern('/api/v1/paper/:paperId/perex'));

        #### ContentController ####
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

        #### HierarchyController ####
        $registry->register($this->postPrototype->withUrlPattern('/api/v1/hierarchy/:uid/add'));
        $registry->register($this->postPrototype->withUrlPattern('/api/v1/hierarchy/:uid/addchild'));
        $registry->register($this->postPrototype->withUrlPattern('/api/v1/hierarchy/:uid/cut'));
        $registry->register($this->postPrototype->withUrlPattern('/api/v1/hierarchy/:uid/cutescape'));
        $registry->register($this->postPrototype->withUrlPattern('/api/v1/hierarchy/:uid/paste'));
        $registry->register($this->postPrototype->withUrlPattern('/api/v1/hierarchy/:uid/pastechild'));
        $registry->register($this->postPrototype->withUrlPattern('/api/v1/hierarchy/:uid/delete'));
        $registry->register($this->postPrototype->withUrlPattern('/api/v1/hierarchy/:uid/trash'));

        $registry->register($this->postPrototype->withUrlPattern('/api/v1/upload/editorimages'));

        $registry->register($this->postPrototype->withUrlPattern('/event/v1/enroll'));
        $registry->register($this->postPrototype->withUrlPattern('/event/v1/visitor'));
        $registry->register($this->postPrototype->withUrlPattern('/event/v1/uploadvisitorfile'));
        $registry->register($this->postPrototype->withUrlPattern('/event/v1/visitorpost'));
        $registry->register($this->postPrototype->withUrlPattern('/event/v1/sendvisitorpost'));
        #### MailController ######
        $registry->register($this->getPrototype->withUrlPattern('/api/v1/sendmail/:campaign'));

        #### TemplateController ####
        $registry->register($this->getPrototype->withUrlPattern('/component/v1/papertemplate/:folder'));
        $registry->register($this->getPrototype->withUrlPattern('/component/v1/authortemplate/:folder/:name'));

        #### ComponentController ####
        $registry->register($this->getPrototype->withUrlPattern('/component/v1/flash'));
        $registry->register($this->getPrototype->withUrlPattern('/component/v1/service/:name'));
        $registry->register($this->getPrototype->withUrlPattern('/component/v1/static/:name'));
        $registry->register($this->getPrototype->withUrlPattern('/component/v1/itempaper/:menuItemId'));
        $registry->register($this->getPrototype->withUrlPattern('/component/v1/itempapereditable/:menuItemId'));

        #### BuildController ####
        $registry->register($this->getPrototype->withUrlPattern('/build/createdb'));
        $registry->register($this->getPrototype->withUrlPattern('/build/dropdb'));
        $registry->register($this->getPrototype->withUrlPattern('/build/createusers'));
        $registry->register($this->getPrototype->withUrlPattern('/build/dropusers'));
        $registry->register($this->getPrototype->withUrlPattern('/build/droptables'));
        $registry->register($this->getPrototype->withUrlPattern('/build/convert'));
        $registry->register($this->getPrototype->withUrlPattern('/build/make'));
    }
}
