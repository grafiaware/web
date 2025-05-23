<?php

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
        $this->putPrototype = $this->prototype->withHttpMethod('PUT');
    }

    public function registerApi(ResourceRegistryInterface $registry): void {

    ### auth module ###
    #
        $registry->register($this->postPrototype->withUrlPattern('/auth/v1/logout'));
        $registry->register($this->postPrototype->withUrlPattern('/auth/v1/login'));
        $registry->register($this->getPrototype->withUrlPattern('/auth/v1/static/:staticName'));        
        $registry->register($this->postPrototype->withUrlPattern('/auth/v1/register'));
        $registry->register($this->getPrototype->withUrlPattern('/auth/v1/registerapplication/:loginname'));
        $registry->register($this->postPrototype->withUrlPattern('/auth/v1/register1'));
        $registry->register($this->getPrototype->withUrlPattern('/auth/v1/confirm/:uid'));
        $registry->register($this->postPrototype->withUrlPattern('/auth/v1/forgottenpassword'));
        $registry->register($this->postPrototype->withUrlPattern('/auth/v1/changepassword'));
        
        $registry->register($this->postPrototype->withUrlPattern('/auth/v1/credentials/:loginnamefk'));
        $registry->register($this->postPrototype->withUrlPattern('/auth/v1/role'));
        $registry->register($this->postPrototype->withUrlPattern('/auth/v1/role/:role'));
        $registry->register($this->postPrototype->withUrlPattern('/auth/v1/role/:role/remove'));
        $registry->register($this->getPrototype->withUrlPattern('/auth/v1/component/:name'));        
        
        $registry->register($this->postPrototype->withUrlPattern('/auth/v1/mailCompletRegistrationRepre'));  
        
        #### SynchroControler  auth ####
        $registry->register($this->postPrototype->withUrlPattern('/auth/v1/synchro'));
        $registry->register($this->postPrototype->withUrlPattern('/auth/v1/ladimsynchro'));
        
        
    ### web module ###
    #
        #### PageControler ####
        $registry->register($this->getPrototype->withUrlPattern('/'));
        $registry->register($this->getPrototype->withUrlPattern('/web/v1/page/item/:uid'));
        $registry->register($this->getPrototype->withUrlPattern('/web/v1/page/block/:name'));
        $registry->register($this->getPrototype->withUrlPattern('/web/v1/page/searchresult'));
        $registry->register($this->getPrototype->withUrlPattern('/web/v1/component/:name'));
        $registry->register($this->getPrototype->withUrlPattern('/web/v1/flash'));

    ### red module ###
    #
        #### ComponentControler ####
        $registry->register($this->getPrototype->withUrlPattern('/red/v1/service/:name'));
        $registry->register($this->getPrototype->withUrlPattern('/red/v1/component/:name'));
        $registry->register($this->getPrototype->withUrlPattern('/red/v1/driver/:uid'));
        $registry->register($this->getPrototype->withUrlPattern('/red/v1/presenteddriver/:uid'));
        $registry->register($this->getPrototype->withUrlPattern('/red/v1/root/:menuItemId'));
        $registry->register($this->getPrototype->withUrlPattern('/red/v1/empty/:menuItemId'));
        $registry->register($this->getPrototype->withUrlPattern('/red/v1/static/:name'));
        $registry->register($this->getPrototype->withUrlPattern('/red/v1/select/:menuItemId'));
        $registry->register($this->getPrototype->withUrlPattern('/red/v1/paper/:menuItemId'));
        $registry->register($this->getPrototype->withUrlPattern('/red/v1/article/:menuItemId'));
        $registry->register($this->getPrototype->withUrlPattern('/red/v1/multipage/:menuItemId'));
        $registry->register($this->getPrototype->withUrlPattern('/red/v1/unknown'));

        #### TemplateControler ####

        $registry->register($this->getPrototype->withUrlPattern('/red/v1/templateslist/:type'));
        $registry->register($this->getPrototype->withUrlPattern('/red/v1/papertemplate/:folder'));
        $registry->register($this->getPrototype->withUrlPattern('/red/v1/articletemplate/:folder'));
        $registry->register($this->getPrototype->withUrlPattern('/red/v1/multipagetemplate/:folder'));
        $registry->register($this->getPrototype->withUrlPattern('/red/v1/authortemplate/:name'));

        #### UserActionControler ####

        #### PresentationActionControler ####
        $registry->register($this->postPrototype->withUrlPattern('/red/v1/presentation/language'));
        $registry->register($this->postPrototype->withUrlPattern('/red/v1/presentation/editoraction'));
        $registry->register($this->putPrototype->withUrlPattern('/red/v1/itemaction/:itemId/add'));
        $registry->register($this->putPrototype->withUrlPattern('/red/v1/itemaction/:itemId/remove'));
        //TODO: POST version
        $registry->register($this->postPrototype->withUrlPattern('/red/v1/itemaction/:itemId/add'));
        $registry->register($this->postPrototype->withUrlPattern('/red/v1/itemaction/:itemId/remove'));
        
        #### PaperControler ####
        $registry->register($this->postPrototype->withUrlPattern('/red/v1/paper'));
        $registry->register($this->postPrototype->withUrlPattern('/red/v1/paper/:paperId'));
        $registry->register($this->postPrototype->withUrlPattern('/red/v1/paper/:paperId/headline'));
        $registry->register($this->postPrototype->withUrlPattern('/red/v1/paper/:paperId/perex'));
        $registry->register($this->postPrototype->withUrlPattern('/red/v1/paper/:paperId/template'));
        $registry->register($this->postPrototype->withUrlPattern('/red/v1/paper/:paperId/templateremove'));

        #### ArticleControler ####
        $registry->register($this->postPrototype->withUrlPattern('/red/v1/article/:articleId'));
        $registry->register($this->postPrototype->withUrlPattern('/red/v1/article/:articleId/template'));

        #### MultipageControler ####
        $registry->register($this->postPrototype->withUrlPattern('/red/v1/multipage/:multipageId/template'));
        $registry->register($this->postPrototype->withUrlPattern('/red/v1/multipage/:multipageId/templateremove'));

        #### SectionControler ####
        $registry->register($this->postPrototype->withUrlPattern('/red/v1/paper/:paperId/section'));
        $registry->register($this->postPrototype->withUrlPattern('/red/v1/section/:sectionId'));
        $registry->register($this->postPrototype->withUrlPattern('/red/v1/section/:sectionId/toggle'));
        $registry->register($this->postPrototype->withUrlPattern('/red/v1/section/:sectionId/actual'));
        $registry->register($this->postPrototype->withUrlPattern('/red/v1/section/:sectionId/event'));
        $registry->register($this->postPrototype->withUrlPattern('/red/v1/section/:sectionId/up'));
        $registry->register($this->postPrototype->withUrlPattern('/red/v1/section/:sectionId/down'));
        $registry->register($this->postPrototype->withUrlPattern('/red/v1/section/:sectionId/cut'));
        $registry->register($this->postPrototype->withUrlPattern('/red/v1/section/:sectionId/copy'));
        $registry->register($this->postPrototype->withUrlPattern('/red/v1/section/:sectionId/cutescape'));
        $registry->register($this->postPrototype->withUrlPattern('/red/v1/section/:sectionId/pasteabove'));
        $registry->register($this->postPrototype->withUrlPattern('/red/v1/section/:sectionId/pastebelow'));
        $registry->register($this->postPrototype->withUrlPattern('/red/v1/section/:sectionId/addabove'));
        $registry->register($this->postPrototype->withUrlPattern('/red/v1/section/:sectionId/addbelow'));
        $registry->register($this->postPrototype->withUrlPattern('/red/v1/section/:sectionId/trash'));
        $registry->register($this->postPrototype->withUrlPattern('/red/v1/section/:sectionId/restore'));
        $registry->register($this->postPrototype->withUrlPattern('/red/v1/section/:sectionId/delete'));

        #### EditItemControler ####
        $registry->register($this->putPrototype->withUrlPattern('/red/v1/menu/:menuItemUidFk/toggle'));
        //TODO: POST version
        $registry->register($this->postPrototype->withUrlPattern('/red/v1/menu/:menuItemUidFk/toggle'));
        $registry->register($this->postPrototype->withUrlPattern('/red/v1/menu/:menuItemUidFk/title'));
        $registry->register($this->postPrototype->withUrlPattern('/red/v1/menu/:menuItemUidFk/type'));

        #### HierarchyControler ####
        $registry->register($this->putPrototype->withUrlPattern('/red/v1/hierarchy/:uid/add'));
        $registry->register($this->putPrototype->withUrlPattern('/red/v1/hierarchy/:uid/addchild'));
        $registry->register($this->putPrototype->withUrlPattern('/red/v1/hierarchy/:uid/cut'));
        $registry->register($this->putPrototype->withUrlPattern('/red/v1/hierarchy/:uid/copy'));
        $registry->register($this->putPrototype->withUrlPattern('/red/v1/hierarchy/:uid/cutcopyescape'));
        $registry->register($this->putPrototype->withUrlPattern('/red/v1/hierarchy/:uid/paste'));
        $registry->register($this->putPrototype->withUrlPattern('/red/v1/hierarchy/:uid/pastechild'));
        $registry->register($this->putPrototype->withUrlPattern('/red/v1/hierarchy/:uid/delete'));
        $registry->register($this->putPrototype->withUrlPattern('/red/v1/hierarchy/:uid/trash'));
        //TODO: POST version
        $registry->register($this->postPrototype->withUrlPattern('/red/v1/hierarchy/:uid/add'));
        $registry->register($this->postPrototype->withUrlPattern('/red/v1/hierarchy/:uid/addchild'));
        $registry->register($this->postPrototype->withUrlPattern('/red/v1/hierarchy/:uid/cut'));
        $registry->register($this->postPrototype->withUrlPattern('/red/v1/hierarchy/:uid/copy'));
        $registry->register($this->postPrototype->withUrlPattern('/red/v1/hierarchy/:uid/cutcopyescape'));
        $registry->register($this->postPrototype->withUrlPattern('/red/v1/hierarchy/:uid/paste'));
        $registry->register($this->postPrototype->withUrlPattern('/red/v1/hierarchy/:uid/pastechild'));
        $registry->register($this->postPrototype->withUrlPattern('/red/v1/hierarchy/:uid/delete'));
        $registry->register($this->postPrototype->withUrlPattern('/red/v1/hierarchy/:uid/trash'));
        
        #### TinyUploadImagesControler
        $registry->register($this->postPrototype->withUrlPattern('/red/v1/upload/image'));
        $registry->register($this->postPrototype->withUrlPattern('/red/v1/upload/attachment'));
        
    ### events module ###
    #
        #### EventsStaticControler ####
        $registry->register($this->getPrototype->withUrlPattern('/events/v1/static/:staticName'));

        #### ComponentControler ####
        $registry->register($this->getPrototype->withUrlPattern('/events/v1/component/:name'));
        $registry->register($this->getPrototype->withUrlPattern('/events/v1/data/:name'));
        $registry->register($this->getPrototype->withUrlPattern('/events/v1/data/:name/:id'));
        $registry->register($this->getPrototype->withUrlPattern('/events/v1/data/:parentName/:parentId/:name'));
        $registry->register($this->getPrototype->withUrlPattern('/events/v1/data/:parentName/:parentId/:name/:id'));
        
        #### RepesentationControler ####
        $registry->register($this->postPrototype->withUrlPattern('/events/v1/representation'));
        
        #### SynchroControler events ####
        $registry->register($this->postPrototype->withUrlPattern('/events/v1/synchro'));
        
        #### EventControler ####
        $registry->register($this->postPrototype->withUrlPattern('/events/v1/enroll'));
        
       
        
        #### CompanyControler  ####
        $registry->register($this->postPrototype->withUrlPattern('/events/v1/company'));
        $registry->register($this->postPrototype->withUrlPattern('/events/v1/company/:companyId'));
        $registry->register($this->postPrototype->withUrlPattern('/events/v1/company/:companyId/remove'));        
        
        $registry->register($this->postPrototype->withUrlPattern('/events/v1/company/:companyId/companycontact'));
        $registry->register($this->postPrototype->withUrlPattern('/events/v1/company/:companyId/companycontact/:companyContactId'));
        $registry->register($this->postPrototype->withUrlPattern('/events/v1/company/:companyId/companycontact/:companyContactId/remove'));
        
        $registry->register($this->postPrototype->withUrlPattern('/events/v1/company/:companyId/companyaddress'));
        $registry->register($this->postPrototype->withUrlPattern('/events/v1/company/:companyId/companyaddress/:companyIdA'));
        $registry->register($this->postPrototype->withUrlPattern('/events/v1/company/:companyId/companyaddress/:companyIdA/remove'));             
        
        $registry->register($this->postPrototype->withUrlPattern('/events/v1/company/:companyId/companyinfo'));
        $registry->register($this->postPrototype->withUrlPattern('/events/v1/company/:companyId/companyinfo/:companyIdA'));
        $registry->register($this->postPrototype->withUrlPattern('/events/v1/company/:companyId/companyinfo/:companyIdA/remove'));  
        
        $registry->register($this->postPrototype->withUrlPattern('/events/v1/representative'));
        $registry->register($this->postPrototype->withUrlPattern('/events/v1/representative/:loginLoginName/:companyId/remove'));
        
        #### FilterControler  #### 
        $registry->register($this->postPrototype->withUrlPattern('/events/v1/filterjob')); 
        $registry->register($this->postPrototype->withUrlPattern('/events/v1/cleanfilterjob'));
       
        #### JobControler  ####
        $registry->register($this->postPrototype->withUrlPattern('/events/v1/company/:companyId/job/:jobId'));
        $registry->register($this->postPrototype->withUrlPattern('/events/v1/company/:companyId/job'));
        $registry->register($this->postPrototype->withUrlPattern('/events/v1/company/:companyId/job/:jobId/remove'));   
                
        $registry->register($this->postPrototype->withUrlPattern('/events/v1/jobtag'));
        $registry->register($this->postPrototype->withUrlPattern('/events/v1/jobtag/:jobTagId'));
        $registry->register($this->postPrototype->withUrlPattern('/events/v1/jobtag/:jobTagId/remove'));
                
        $registry->register($this->postPrototype->withUrlPattern('/events/v1/job/:jobId/jobtotag'));   
       
        $registry->register($this->postPrototype->withUrlPattern('/events/v1/vzdelani'));
        $registry->register($this->postPrototype->withUrlPattern('/events/v1/vzdelani/:stupen'));
        $registry->register($this->postPrototype->withUrlPattern('/events/v1/vzdelani/:stupen/remove'));   
      
        #### VisitorJobRequestControler
        $registry->register($this->postPrototype->withUrlPattern('/events/v1/job/:jobId/jobrequest'));
        $registry->register($this->postPrototype->withUrlPattern('/events/v1/job/:jobId/jobrequest/:loginloginname'));
        $registry->register($this->postPrototype->withUrlPattern('/events/v1/job/:jobId/jobrequest/:loginloginname/send'));
        
        #### VisitorProfileControler ####
        $registry->register($this->postPrototype->withUrlPattern('/events/v1/visitorprofile'));        
        $registry->register($this->postPrototype->withUrlPattern('/events/v1/visitorprofile/:loginname'));        
        $registry->register($this->postPrototype->withUrlPattern('/events/v1/uploadvisitorfile'));
        
        #### DocumentControler
        $registry->register($this->postPrototype->withUrlPattern('/events/v1/visitorprofile/:parentId/doctype/:type'));
        $registry->register($this->postPrototype->withUrlPattern('/events/v1/visitorprofile/:parentId/doctype/:type/remove'));

        #### EventControler_2 ####
        $registry->register($this->postPrototype->withUrlPattern('/events/v1/institution'));
        $registry->register($this->postPrototype->withUrlPattern('/events/v1/institution/:institutionId'));
        $registry->register($this->postPrototype->withUrlPattern('/events/v1/institution/:institutionId/remove'));
        
        $registry->register($this->postPrototype->withUrlPattern('/events/v1/institutiontype'));
        $registry->register($this->postPrototype->withUrlPattern('/events/v1/institutiontype/:institutiontypeId'));
        $registry->register($this->postPrototype->withUrlPattern('/events/v1/institutiontype/:institutiontypeId/remove'));
        
        $registry->register($this->postPrototype->withUrlPattern('/events/v1/eventcontenttype'));
        $registry->register($this->postPrototype->withUrlPattern('/events/v1/eventcontenttype/:id'));
        $registry->register($this->postPrototype->withUrlPattern('/events/v1/eventcontenttype/:id/remove'));

        $registry->register($this->postPrototype->withUrlPattern('/events/v1/eventcontent'));
        $registry->register($this->postPrototype->withUrlPattern('/events/v1/eventcontent/:idContent'));
        $registry->register($this->postPrototype->withUrlPattern('/events/v1/eventcontent/:idContent/remove'));

        $registry->register($this->postPrototype->withUrlPattern('/events/v1/eventlinkphase'));
        $registry->register($this->postPrototype->withUrlPattern('/events/v1/eventlinkphase/:eventLinkPhaseId'));
        $registry->register($this->postPrototype->withUrlPattern('/events/v1/eventlinkphase/:eventLinkPhaseId/remove'));

        $registry->register($this->postPrototype->withUrlPattern('/events/v1/eventlink'));
        $registry->register($this->postPrototype->withUrlPattern('/events/v1/eventlink/:eventLinkId'));
        $registry->register($this->postPrototype->withUrlPattern('/events/v1/eventlink/:eventLinkId/remove'));      
        
    ### mail module ###
    #
    
        // TEST
        $registry->register($this->postPrototype->withUrlPattern('/auth/v1/testmail'));     
        
        
        
        #### MailControler ######
        $registry->register($this->postPrototype->withUrlPattern('/sendmail/v1/validate/:campaign'));
        $registry->register($this->postPrototype->withUrlPattern('/sendmail/v1/campaign/:campaign'));
        $registry->register($this->postPrototype->withUrlPattern('/sendmail/v1/send/:campaign'));

    ### build module ###
    #
        #### BuildControler ####
        $registry->register($this->getPrototype->withUrlPattern('/build'));
        $registry->register($this->postPrototype->withUrlPattern('/build/listconfig'));
        $registry->register($this->postPrototype->withUrlPattern('/build/createdb'));
        $registry->register($this->postPrototype->withUrlPattern('/build/dropdb'));
        $registry->register($this->postPrototype->withUrlPattern('/build/createusers'));
        $registry->register($this->postPrototype->withUrlPattern('/build/dropusers'));
        $registry->register($this->postPrototype->withUrlPattern('/build/droptables'));
        $registry->register($this->postPrototype->withUrlPattern('/build/convert'));
        $registry->register($this->postPrototype->withUrlPattern('/build/make'));
        $registry->register($this->postPrototype->withUrlPattern('/build/import'));

    ### consent module ###
    #
        #### LogControler ####    
        $registry->register($this->postPrototype->withUrlPattern('/consent/v1/log'));
    #
    }
    

    
}
