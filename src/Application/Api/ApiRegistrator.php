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

        ### auth module ###

        $registry->register($this->postPrototype->withUrlPattern('/auth/v1/logout'));
        $registry->register($this->postPrototype->withUrlPattern('/auth/v1/login'));
        $registry->register($this->postPrototype->withUrlPattern('/auth/v1/register'));
        $registry->register($this->getPrototype->withUrlPattern('/auth/v1/registerapplication/:loginname'));
        $registry->register($this->postPrototype->withUrlPattern('/auth/v1/register1'));
        $registry->register($this->getPrototype->withUrlPattern('/auth/v1/confirm/:uid'));
        $registry->register($this->postPrototype->withUrlPattern('/auth/v1/forgottenpassword'));
        $registry->register($this->postPrototype->withUrlPattern('/auth/v1/changepassword'));

        ### web module ###
        #
        #### PageController ####
        $registry->register($this->getPrototype->withUrlPattern('/'));
        $registry->register($this->getPrototype->withUrlPattern('/web/v1/page/item/:uid'));
        $registry->register($this->getPrototype->withUrlPattern('/web/v1/page/subitem/:uid'));
        $registry->register($this->getPrototype->withUrlPattern('/web/v1/page/block/:name'));
        $registry->register($this->getPrototype->withUrlPattern('/web/v1/page/searchresult'));




        ### red module ###
        #
        #### ComponentController ####
        $registry->register($this->getPrototype->withUrlPattern('/red/v1/flash'));
        $registry->register($this->getPrototype->withUrlPattern('/red/v1/service/:name'));
        $registry->register($this->getPrototype->withUrlPattern('/red/v1/static/:name'));
        $registry->register($this->getPrototype->withUrlPattern('/red/v1/empty/:menuItemId'));
        $registry->register($this->getPrototype->withUrlPattern('/red/v1/paper/:menuItemId'));
        $registry->register($this->getPrototype->withUrlPattern('/red/v1/article/:menuItemId'));
        $registry->register($this->getPrototype->withUrlPattern('/red/v1/multipage/:menuItemId'));
        $registry->register($this->getPrototype->withUrlPattern('/red/v1/unknown'));

        #### TemplateController ####

        $registry->register($this->getPrototype->withUrlPattern('/red/v1/templateslist/:type'));
        $registry->register($this->getPrototype->withUrlPattern('/red/v1/papertemplate/:folder'));
        $registry->register($this->getPrototype->withUrlPattern('/red/v1/articletemplate/:folder'));
        $registry->register($this->getPrototype->withUrlPattern('/red/v1/multipagetemplate/:folder'));
        $registry->register($this->getPrototype->withUrlPattern('/red/v1/authortemplate/:name'));

        #### UserActionController ####

        #### PresentationActionControler ####
        $registry->register($this->postPrototype->withUrlPattern('/red/v1/presentation/language'));
        $registry->register($this->postPrototype->withUrlPattern('/red/v1/presentation/edit_mode'));
        $registry->register($this->postPrototype->withUrlPattern('/red/v1/presentation/edit_content'));
        $registry->register($this->postPrototype->withUrlPattern('/red/v1/presentation/edit_menu'));
        $registry->register($this->postPrototype->withUrlPattern('/red/v1/itemaction/:typeFk/:itemId/add'));
        $registry->register($this->postPrototype->withUrlPattern('/red/v1/itemaction/:typeFk/:itemId/remove'));

        #### PaperController ####
        $registry->register($this->postPrototype->withUrlPattern('/red/v1/paper'));
        $registry->register($this->postPrototype->withUrlPattern('/red/v1/paper/:paperId'));
        $registry->register($this->postPrototype->withUrlPattern('/red/v1/paper/:paperId/headline'));
        $registry->register($this->postPrototype->withUrlPattern('/red/v1/paper/:paperId/perex'));
        $registry->register($this->postPrototype->withUrlPattern('/red/v1/paper/:paperId/template'));
        $registry->register($this->postPrototype->withUrlPattern('/red/v1/paper/:paperId/templateremove'));

        #### ArticleController ####
        $registry->register($this->postPrototype->withUrlPattern('/red/v1/article/:articleId'));
        $registry->register($this->postPrototype->withUrlPattern('/red/v1/article/:articleId/template'));

        #### MultipageControler ####
        $registry->register($this->postPrototype->withUrlPattern('/red/v1/multipage/:multipageId/template'));
        $registry->register($this->postPrototype->withUrlPattern('/red/v1/multipage/:multipageId/templateremove'));

        #### SectionController ####
        $registry->register($this->postPrototype->withUrlPattern('/red/v1/paper/:paperId/section'));
        $registry->register($this->postPrototype->withUrlPattern('/red/v1/section/:sectionId'));
        $registry->register($this->postPrototype->withUrlPattern('/red/v1/section/:sectionId/toggle'));
        $registry->register($this->postPrototype->withUrlPattern('/red/v1/section/:sectionId/actual'));
        $registry->register($this->postPrototype->withUrlPattern('/red/v1/section/:sectionId/event'));
        $registry->register($this->postPrototype->withUrlPattern('/red/v1/section/:sectionId/up'));
        $registry->register($this->postPrototype->withUrlPattern('/red/v1/section/:sectionId/down'));
        $registry->register($this->postPrototype->withUrlPattern('/red/v1/section/:sectionId/add_above'));
        $registry->register($this->postPrototype->withUrlPattern('/red/v1/section/:sectionId/add_below'));
        $registry->register($this->postPrototype->withUrlPattern('/red/v1/section/:sectionId/trash'));
        $registry->register($this->postPrototype->withUrlPattern('/red/v1/section/:sectionId/restore'));
        $registry->register($this->postPrototype->withUrlPattern('/red/v1/section/:sectionId/delete'));

        #### EditItemController ####
        $registry->register($this->postPrototype->withUrlPattern('/red/v1/menu/:menuItemUidFk/toggle'));
        $registry->register($this->postPrototype->withUrlPattern('/red/v1/menu/:menuItemUidFk/title'));
        $registry->register($this->postPrototype->withUrlPattern('/red/v1/menu/:menuItemUidFk/type'));

        #### HierarchyController ####
        $registry->register($this->postPrototype->withUrlPattern('/red/v1/hierarchy/:uid/add'));
        $registry->register($this->postPrototype->withUrlPattern('/red/v1/hierarchy/:uid/addchild'));
        $registry->register($this->postPrototype->withUrlPattern('/red/v1/hierarchy/:uid/cut'));
        $registry->register($this->postPrototype->withUrlPattern('/red/v1/hierarchy/:uid/copy'));
        $registry->register($this->postPrototype->withUrlPattern('/red/v1/hierarchy/:uid/cutcopyescape'));
        $registry->register($this->postPrototype->withUrlPattern('/red/v1/hierarchy/:uid/paste'));
        $registry->register($this->postPrototype->withUrlPattern('/red/v1/hierarchy/:uid/pastechild'));
        $registry->register($this->postPrototype->withUrlPattern('/red/v1/hierarchy/:uid/delete'));
        $registry->register($this->postPrototype->withUrlPattern('/red/v1/hierarchy/:uid/trash'));

        #### TinyUploadImagesController
        $registry->register($this->postPrototype->withUrlPattern('/red/v1/upload/editorimages'));

        ### events module ###
        #
        #### EventController ####
        $registry->register($this->postPrototype->withUrlPattern('/events/v1/enroll'));
        
        #### VisitorController ####
        $registry->register($this->postPrototype->withUrlPattern('/events/v1/visitor'));
        #### CompanyController ?? ####
        $registry->register($this->postPrototype->withUrlPattern('events/v1/company/:companyId/companycontact'));
        $registry->register($this->postPrototype->withUrlPattern('events/v1/company/:companyId/companycontact/:companyContactId'));
        $registry->register($this->postPrototype->withUrlPattern('events/v1/company/:companyId/companycontact/:companyContactId/remove'));
        
        $registry->register($this->postPrototype->withUrlPattern('events/v1/company/:companyId/companyaddress'));
        $registry->register($this->postPrototype->withUrlPattern('events/v1/company/:companyId/companyaddress/:companyAddressId'));
        $registry->register($this->postPrototype->withUrlPattern('events/v1/company/:companyId/companyaddress/:companyAddressId/remove'));             
        
        $registry->register($this->postPrototype->withUrlPattern('/events/v1/representative'));
        $registry->register($this->postPrototype->withUrlPattern('/events/v1/representative/:loginLoginName/:companyId/remove'));
        
        
        $registry->register($this->postPrototype->withUrlPattern('/events/v1/uploadvisitorfile'));
        #### JobRequestControler
        $registry->register($this->postPrototype->withUrlPattern('/events/v1/:jobId/jobrequest'));
        $registry->register($this->postPrototype->withUrlPattern('/events/v1/sendjobrequest/:visitorLoginName/:jobId'));
        #### VisitorDataController ####
        $registry->register($this->getPrototype->withUrlPattern('/events/v1/eventcontent/:staticName'));
        #### DocumentControler
        $registry->register($this->postPrototype->withUrlPattern('/events/v1/document/:id/remove'));

        ### mail module ###
        #
        #### MailController ######
        $registry->register($this->getPrototype->withUrlPattern('/sendmail/v1/sendmail/:campaign'));

        ### build module ###
        #
        #### BuildController #### '/build/listconfig'
        $registry->register($this->getPrototype->withUrlPattern('/build/listconfig'));
        $registry->register($this->getPrototype->withUrlPattern('/build/createdb'));
        $registry->register($this->getPrototype->withUrlPattern('/build/dropdb'));
        $registry->register($this->getPrototype->withUrlPattern('/build/createusers'));
        $registry->register($this->getPrototype->withUrlPattern('/build/dropusers'));
        $registry->register($this->getPrototype->withUrlPattern('/build/droptables'));
        $registry->register($this->getPrototype->withUrlPattern('/build/convert'));
        $registry->register($this->getPrototype->withUrlPattern('/build/make'));
    }
}
