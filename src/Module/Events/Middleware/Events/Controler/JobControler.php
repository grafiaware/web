<?php 

namespace Events\Middleware\Events\Controler;

use Site\ConfigurationCache;
use FrontControler\FrontControlerAbstract;
use Auth\Model\Entity\LoginAggregateFullInterface;

use Pes\View\Renderer\PhpTemplateRendererInterface;
/** @var PhpTemplateRendererInterface $this */

use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusFlashRepo;
use Status\Model\Repository\StatusPresentationRepo;

use Events\Model\Repository\RepresentativeRepoInterface;
use Events\Model\Repository\CompanyRepoInterface;
use Events\Model\Repository\PozadovaneVzdelaniRepoInterface;
use Events\Model\Repository\JobRepoInterface;
use Events\Model\Repository\JobToTagRepoInterface;
use Events\Model\Repository\JobTagRepoInterface;

use Events\Model\Entity\RepresentativeInterface;
use Events\Model\Entity\Representative;
use Events\Model\Entity\JobInterface;
use Events\Model\Entity\Job;
use Events\Model\Entity\JobToTag;
use Events\Model\Entity\JobToTagInterface;
use Events\Model\Entity\JobTag;
use Events\Model\Entity\JobTagInterface;
use Events\Model\Entity\PozadovaneVzdelaniInterface;
use Events\Model\Entity\PozadovaneVzdelani;


use Status\Model\Enum\FlashSeverityEnum;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use Pes\Http\Helper\RequestStatus;
use Pes\Http\Request\RequestParams;
use Pes\Http\Factory\ResponseFactory;
use Pes\Http\Response;

use Mail\Mail;
use Mail\MessageFactory\HtmlMessage;
use Mail\Params;
use Mail\Params\Content;
use Mail\Params\Attachment; 
use Mail\Params\StringAttachment;
use Mail\Params\Party;


/**
 * 
 */
class JobControler extends FrontControlerAbstract {        
    /**
     * 
     * @var CompanyRepoInterface
     */
    private $companyRepo;
    /**
     * 
     * @var RepresentativeRepoInterface
     */
    private $representativeRepo;    
    /**
     * 
     * @var JobRepoInterface
     */
    private $jobRepo;
     /**
     * 
     * @var PozadovaneVzdelaniRepoInterface
     */
    private $pozadovaneVzdelaniRepo;
    /**
     * 
     * @var JobToTagRepoInterface
     */
    private $jobToTagRepo;
    /**
     * 
     * @var JobTagRepoInterface
     */
    private $jobTagRepo;

                  
   
    /**
     * 
     * @param StatusSecurityRepo $statusSecurityRepo
     * @param StatusFlashRepo $statusFlashRepo
     * @param StatusPresentationRepo $statusPresentationRepo
     * @param CompanyRepoInterface $companyRepo
     * @param RepresentativeRepoInterface $representativeRepo
     * @param PozadovaneVzdelaniRepoInterface $pozadovaneVzdelaniRepo
     * @param JobRepoInterface $jobRepo
     * @param JobToTagInterface $jobToTagRepo
     * @param JobTagInterface $jobTagRepo
     */
    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusFlashRepo $statusFlashRepo,
            StatusPresentationRepo $statusPresentationRepo,
            
            CompanyRepoInterface $companyRepo,           
            RepresentativeRepoInterface $representativeRepo,
            PozadovaneVzdelaniRepoInterface $pozadovaneVzdelaniRepo,            
            JobRepoInterface $jobRepo,
            JobToTagRepoInterface $jobToTagRepo,
            JobTagRepoInterface $jobTagRepo
            
            ) {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo);
        $this->companyRepo = $companyRepo;     
        $this->representativeRepo = $representativeRepo;
        $this->pozadovaneVzdelaniRepo = $pozadovaneVzdelaniRepo;
        $this->jobRepo = $jobRepo;
        $this->jobToTagRepo = $jobToTagRepo;
        $this->jobTagRepo = $jobTagRepo;
    }
    
   
    
     
    
    /**
     * 
     * @param ServerRequestInterface $request
     * @param type $idCompany
     * @return type
     */
    public function addJob (ServerRequestInterface $request, $idCompany) {                 
        $isRepresentative = false;
        
        /** @var StatusSecurityRepo $statusSecurityRepo */
        $statusSecurity = $this->statusSecurityRepo->get();
        /** @var LoginAggregateFullInterface $loginAggregateCredentials */
        $loginAggregateCredentials = $statusSecurity->getLoginAggregate();                           
        if (!isset($loginAggregateCredentials)) {
            $response = (new ResponseFactory())->createResponse();
            return $response->withStatus(401);  // Unaathorized
        } else {  
            $loginName = $loginAggregateCredentials->getLoginName();            
            $role = $loginAggregateCredentials->getCredentials()->getRole() ?? ''; 
            
            if(isset($role) AND ($role==ConfigurationCache::loginLogoutController()['roleRepresentative']) 
                            AND  $this->representativeRepo->get($loginName, $idCompany) )  {
                $isRepresentative = true; 
            }
                        
            if ($isRepresentative) {
                /** @var JobInterface $job */
                $job = $this->container->get(Job::class); //new $job
                
                $job->setCompanyId($idCompany);
                // POST formularovadata                
                $job->setPozadovaneVzdelaniStupen((new RequestParams())->getParsedBodyParam($request, 'pozadovane-vzdelani-stupen'));
                $job->setNazev((new RequestParams())->getParsedBodyParam($request, 'nazev'));
                $job->setMistoVykonu((new RequestParams())->getParsedBodyParam($request, 'misto-vykonu'));
                $job->setPopisPozice((new RequestParams())->getParsedBodyParam($request, 'popis-pozice'));
                $job->setPozadujeme((new RequestParams())->getParsedBodyParam($request, 'pozadujeme'));
                $job->setNabizime((new RequestParams())->getParsedBodyParam($request, 'nabizime'));         
                
                $this->jobRepo->add($job);
                
            } else {
                $this->addFlashMessage("Údaje o kontaktech vyvstavovatele smí editovat pouze representant vystavovatele.");
            }
            
        }
        return $this->redirectSeeLastGet($request);
    }
    
  
    
    /**
     * 
     * @param ServerRequestInterface $request
     * @param type $idCompany
     * @param type $idJob
     * @return type
     */
    public function updateJob (ServerRequestInterface $request, $idCompany, $idJob) {                   
        $isRepresentative = false;
        
        /** @var StatusSecurityRepo $statusSecurityRepo */
        $statusSecurity = $this->statusSecurityRepo->get();
        /** @var LoginAggregateFullInterface $loginAggregateCredentials */
        $loginAggregateCredentials = $statusSecurity->getLoginAggregate();                           
        if (!isset($loginAggregateCredentials)) {
            $response = (new ResponseFactory())->createResponse();
            return $response->withStatus(401);  // Unauthorized
        } else {                                   
            $loginName = $loginAggregateCredentials->getLoginName();            
            $role = $loginAggregateCredentials->getCredentials()->getRole() ?? '';         
            
            if(isset($role) AND ($role==ConfigurationCache::loginLogoutController()['roleRepresentative']) ) {               
                if ( $this->representativeRepo->get($loginName, $idCompany ) )   {
                            $isRepresentative = true; 
                }
            }            
            if ($isRepresentative) {
                /** @var JobInterface $job */
                $job = $this->jobRepo->get( $idJob );
                
                // POST formularova data                                                            
                $job->setPozadovaneVzdelaniStupen((new RequestParams())->getParsedBodyParam($request, 'pozadovane-vzdelani-stupen'));
                $job->setNazev((new RequestParams())->getParsedBodyParam($request, 'nazev'));
                $job->setMistoVykonu((new RequestParams())->getParsedBodyParam($request, 'misto-vykonu'));
                $job->setPopisPozice((new RequestParams())->getParsedBodyParam($request, 'popis-pozice'));
                $job->setPozadujeme((new RequestParams())->getParsedBodyParam($request, 'pozadujeme'));
                $job->setNabizime((new RequestParams())->getParsedBodyParam($request, 'nabizime'));   
                
                //$this->jobRepo->add($job);
                
            } else {
                $this->addFlashMessage("Údaje o kontaktech vystavovatele smí editovat pouze representant vystavovatele.");
            }
            
        }
        return $this->redirectSeeLastGet($request);
    }

                
                
   
    /**
     * 
     * @param ServerRequestInterface $request
     * @param type $idCompany
     * @param type $idJob
     * @return type
     */
    public function removeJob (ServerRequestInterface $request, $idCompany, $idJob) {                   
        $isRepresentative = false;
                
        /** @var StatusSecurityRepo $statusSecurityRepo */
        $statusSecurity = $this->statusSecurityRepo->get();
        /** @var LoginAggregateFullInterface $loginAggregateCredentials */
        $loginAggregateCredentials = $statusSecurity->getLoginAggregate();                           
        if (!isset($loginAggregateCredentials)) {
            $response = (new ResponseFactory())->createResponse();
            return $response->withStatus(401);  // Unaathorized
        } else {                                   
            $loginName = $loginAggregateCredentials->getLoginName();            
            $role = $loginAggregateCredentials->getCredentials()->getRole() ?? '';           
            
            if(isset($role) AND ($role==ConfigurationCache::loginLogoutController()['roleRepresentative']) ) {               
                if ( $this->representativeRepo->get($loginName, $idCompany ) )   {
                            $isRepresentative = true; 
                }
            }          
                
            if ($isRepresentative) {       
                /** @var JobInterface $job */
                $job = $this->jobRepo->get( $idJob );                                
                $this->jobRepo->remove( $job ); 
                                
            } else {
                $this->addFlashMessage("Údaje o kontaktech vystavovatele smí mazat pouze representant vystavovatele.");
            }
            
        }
        return $this->redirectSeeLastGet($request);
    }
            
    
    
    
        
    /**
     * 
     * @param ServerRequestInterface $request
     * @param type $idJob
     * @return type
     */
    public function processingJobToTag (ServerRequestInterface $request, $idJob) {                   
        $isRepresentative = false;
        
        /** @var StatusSecurityRepo $statusSecurityRepo */
        $statusSecurity = $this->statusSecurityRepo->get();
        /** @var LoginAggregateFullInterface $loginAggregateCredentials */
        $loginAggregateCredentials = $statusSecurity->getLoginAggregate();                           
        if (!isset($loginAggregateCredentials)) {
            $response = (new ResponseFactory())->createResponse();
            return $response->withStatus(401);  // Unauthorized
        } else {                                   
            $loginName = $loginAggregateCredentials->getLoginName();            
            $role = $loginAggregateCredentials->getCredentials()->getRole() ?? '';   
            
            $job = $this->jobRepo->get($idJob);            
            
            if(isset($role) AND ($role==ConfigurationCache::loginLogoutController()['roleRepresentative']) ) {               
                if ( $this->representativeRepo->get($loginName, $job->getCompanyId( ) ))   {
                            $isRepresentative = true; 
                }
            }            
            if ($isRepresentative) {
                               
                $arrayForJob = [];
                $allJobToTagForJob = $this->jobToTagRepo->findByJobId($idJob);
                /** @var JobToTagInterface $jobToTag */
                foreach ($allJobToTagForJob as $jobToTag) {   
                    $arrayForJob[]= $jobToTag->getJobTagTag();
                }
                                
                $allTags = $this->jobTagRepo->findAll(); //vsechny tag co existuji
                /** @var JobTagInterface $tag */
                foreach ($allTags as $tagEntity) {
                    // $postTag - tento tag je zaskrtnut ve form
                    $postTag = (new RequestParams())->getParsedBodyParam($request, $tagEntity->getTag() );
                    
                    if (isset ($postTag) ) { // je zaskrtnut ve form
                        //je-li v jobToTag - ok, nic
                        //neni-li v jobToTag - zapsat do jobToTag 
                        if (!(in_array($postTag,  $arrayForJob))) {                                                                            
                            /** @var JobToTag $newJobToTag */
                            $newJobToTag = $this->container->get(JobToTag::class); //new 
                            $newJobToTag->setJobId($idJob); 
                            $newJobToTag->setJobTagTag($postTag);
                            $this->jobToTagRepo->add($newJobToTag);
                        }                        
                    }
                    else { // neni zaskrtnut ve form                                      
                        //je-li v jobToTag  - vymazat z jobToTag
                        //neni-li v jobToTag   - ok, nic
                        if (in_array($tagEntity->getTag(), $arrayForJob)) {   
                           $jobToTagEntity = $this->jobToTagRepo->get($idJob, $tagEntity->getTag());
                           $this->jobToTagRepo->remove($jobToTagEntity);
                        }
                    }
                 
                }                                                                
                
            } else {
                $this->addFlashMessage("Údaje o typech nabízených pozic smí editovat pouze representant vystavovatele.");
            }
            
        }
        return $this->redirectSeeLastGet($request);
    }
    
    
    
    
    
    /**
     * 
     * @param ServerRequestInterface $request
     * @return type
     */
    public function addJobTag (ServerRequestInterface $request) {                 
//        $isRepresentative = false;
//        
//        /** @var StatusSecurityRepo $statusSecurityRepo */
//        $statusSecurity = $this->statusSecurityRepo->get();
//        /** @var LoginAggregateFullInterface $loginAggregateCredentials */
//        $loginAggregateCredentials = $statusSecurity->getLoginAggregate();                           
//        if (!isset($loginAggregateCredentials)) {
//            $response = (new ResponseFactory())->createResponse();
//            return $response->withStatus(401);  // Unaathorized
//        } else {  
//            $loginName = $loginAggregateCredentials->getLoginName();            
//            $role = $loginAggregateCredentials->getCredentials()->getRole() ?? ''; 
//            
//            if(isset($role) AND ($role==ConfigurationCache::loginLogoutController()['roleRepresentative']) 
//                            AND  $this->representativeRepo->get($loginName, $idCompany) )  {
//                $isRepresentative = true; 
//            }                        
//            if ($isRepresentative) {

                
                /** @var JobTagInterface $tag */
                $tag = $this->container->get(JobTag::class); //new       

                $tag->setTag((new RequestParams())->getParsedBodyParam($request, 'tag') );              
                $this->jobTagRepo->add($tag);             
               
//            } else {
//                $this->addFlashMessage("Možné typy nabízených pozic smí přidávat pouze ...");
//            }
//        }   
        
        return $this->redirectSeeLastGet($request);
    }
    
    
    /**
     * 
     * @param ServerRequestInterface $request
     * @param type $tag
     */
    public function updateJobTag (ServerRequestInterface $request, $tag) {                    
//        $isRepresentative = false;
//        
//        /** @var StatusSecurityRepo $statusSecurityRepo */
//        $statusSecurity = $this->statusSecurityRepo->get();
//        /** @var LoginAggregateFullInterface $loginAggregateCredentials */
//        $loginAggregateCredentials = $statusSecurity->getLoginAggregate();                           
//        if (!isset($loginAggregateCredentials)) {
//            $response = (new ResponseFactory())->createResponse();
//            return $response->withStatus(401);  // Unaathorized
//        } else {  
//            $loginName = $loginAggregateCredentials->getLoginName();            
//            $role = $loginAggregateCredentials->getCredentials()->getRole() ?? ''; 
//            
//            if(isset($role) AND ($role==ConfigurationCache::loginLogoutController()['roleRepresentative']) 
//                            AND  $this->representativeRepo->get($loginName, $idCompany) )  {
//                $isRepresentative = true; 
//            }                        
//            if ($isRepresentative) {
        
        
            /** @var JobTagInterface $tag */
            $tag = $this->jobTagRepo->get($tag);       
            $tag->setTag((new RequestParams())->getParsedBodyParam($request, 'tag') );                        
        
        
//            } else {
//                $this->addFlashMessage("Možné typy nabízených pozic smí editovat pouze ...");
//            }
//        }           
        
        return $this->redirectSeeLastGet($request);

    }    
      

    /**
     * 
     * @param ServerRequestInterface $request
     * @param type $tag
     * @return type
     */
    public function removeJobTag (ServerRequestInterface $request, $tag) {                   
//        $isRepresentative = false;
//                
//        /** @var StatusSecurityRepo $statusSecurityRepo */
//        $statusSecurity = $this->statusSecurityRepo->get();
//        /** @var LoginAggregateFullInterface $loginAggregateCredentials */
//        $loginAggregateCredentials = $statusSecurity->getLoginAggregate();                           
//        if (!isset($loginAggregateCredentials)) {
//            $response = (new ResponseFactory())->createResponse();
//            return $response->withStatus(401);  // Unaathorized
//        } else {                                   
//            $loginName = $loginAggregateCredentials->getLoginName();            
//            $role = $loginAggregateCredentials->getCredentials()->getRole() ?? '';           
//            
//            if(isset($role) AND ($role==ConfigurationCache::loginLogoutController()['roleRepresentative']) ) {               
//                if ( $this->representativeRepo->get($loginName, $idCompany ) )   {
//                            $isRepresentative = true; 
//                }
//            }                          
//            if ($isRepresentative) {       
                    
           
                /** @var JobTagInterface $job */
                $tag = $this->jobTagRepo->get( $tag );                                
                $this->jobTagRepo->remove( $tag );         
                                                
//            } else {
//                $this->addFlashMessage("Možné typy nabízených pozic  smí odstraňovat pouze ....");
//            }           
//        }
                
        return $this->redirectSeeLastGet($request);
    }
            
    
    
    
        
    /**
     * 
     * @param ServerRequestInterface $request
     * @return type
     */
    public function addPozadovaneVzdelani (ServerRequestInterface $request) {                 
//        $isRepresentative = false;
//        
//        /** @var StatusSecurityRepo $statusSecurityRepo */
//        $statusSecurity = $this->statusSecurityRepo->get();
//        /** @var LoginAggregateFullInterface $loginAggregateCredentials */
//        $loginAggregateCredentials = $statusSecurity->getLoginAggregate();                           
//        if (!isset($loginAggregateCredentials)) {
//            $response = (new ResponseFactory())->createResponse();
//            return $response->withStatus(401);  // Unaathorized
//        } else {  
//            $loginName = $loginAggregateCredentials->getLoginName();            
//            $role = $loginAggregateCredentials->getCredentials()->getRole() ?? ''; 
//            
//            if(isset($role) AND ($role==ConfigurationCache::loginLogoutController()['roleRepresentative']) 
//                            AND  $this->representativeRepo->get($loginName, $idCompany) )  {
//                $isRepresentative = true; 
//            }                        
//            if ($isRepresentative) {

                
                /** @var PozadovaneVzdelaniInterface $pozadovaneVzdelani */
               // $pozadovaneVzdelani = $this->container->get(PozadovaneVzdelani::class); //new       
                $pozadovaneVzdelani = new PozadovaneVzdelani() ; //new    

                $pozadovaneVzdelani->setStupen((new RequestParams())->getParsedBodyParam($request, 'stupen') );
                $pozadovaneVzdelani->setVzdelani((new RequestParams())->getParsedBodyParam($request, 'vzdelani') );              

                $this->pozadovaneVzdelaniRepo->add($pozadovaneVzdelani);             
               
//            } else {
//                $this->addFlashMessage("Možné typy nabízených pozic smí přidávat pouze ...");
//            }
//        }   
        
        return $this->redirectSeeLastGet($request);
    }
    
    
  
    /**
     * 
     * @param ServerRequestInterface $request
     * @param type $stupen
     * @return type
     */
    public function updatePozadovaneVzdelani (ServerRequestInterface $request, $stupen) {                    
//        $isRepresentative = false;
//        
//        /** @var StatusSecurityRepo $statusSecurityRepo */
//        $statusSecurity = $this->statusSecurityRepo->get();
//        /** @var LoginAggregateFullInterface $loginAggregateCredentials */
//        $loginAggregateCredentials = $statusSecurity->getLoginAggregate();                           
//        if (!isset($loginAggregateCredentials)) {
//            $response = (new ResponseFactory())->createResponse();
//            return $response->withStatus(401);  // Unaathorized
//        } else {  
//            $loginName = $loginAggregateCredentials->getLoginName();            
//            $role = $loginAggregateCredentials->getCredentials()->getRole() ?? ''; 
//            
//            if(isset($role) AND ($role==ConfigurationCache::loginLogoutController()['roleRepresentative']) 
//                            AND  $this->representativeRepo->get($loginName, $idCompany) )  {
//                $isRepresentative = true; 
//            }                        
//            if ($isRepresentative) {
        
                   
            /** @var PozadovaneVzdelaniInterface $pozadovaneVzdelani */
            $pozadovaneVzdelani = $this->pozadovaneVzdelaniRepo->get($stupen);            
            $pozadovaneVzdelani->setVzdelani((new RequestParams())->getParsedBodyParam($request, 'vzdelani') );
            $pozadovaneVzdelani->setStupen((new RequestParams())->getParsedBodyParam($request, 'stupen') ); 
           
        
//            } else {
//                $this->addFlashMessage("Možné typy nabízených pozic smí editovat pouze ...");
//            }
//        }           
        
        return $this->redirectSeeLastGet($request);

    }    
      

    /**
     * 
     * @param ServerRequestInterface $request
     * @param type $stupen
     * @return type
     */
    public function removePozadovaneVzdelani (ServerRequestInterface $request, $stupen) {                   
//        $isRepresentative = false;
//                
//        /** @var StatusSecurityRepo $statusSecurityRepo */
//        $statusSecurity = $this->statusSecurityRepo->get();
//        /** @var LoginAggregateFullInterface $loginAggregateCredentials */
//        $loginAggregateCredentials = $statusSecurity->getLoginAggregate();                           
//        if (!isset($loginAggregateCredentials)) {
//            $response = (new ResponseFactory())->createResponse();
//            return $response->withStatus(401);  // Unaathorized
//        } else {                                   
//            $loginName = $loginAggregateCredentials->getLoginName();            
//            $role = $loginAggregateCredentials->getCredentials()->getRole() ?? '';           
//            
//            if(isset($role) AND ($role==ConfigurationCache::loginLogoutController()['roleRepresentative']) ) {               
//                if ( $this->representativeRepo->get($loginName, $idCompany ) )   {
//                            $isRepresentative = true; 
//                }
//            }                          
//            if ($isRepresentative) {       
                    
           
            /** @var PozadovaneVzdelaniInterface $pozadovaneVzdelani */
            $pozadovaneVzdelani = $this->pozadovaneVzdelaniRepo->get($stupen);            
            $this->pozadovaneVzdelaniRepo->remove($pozadovaneVzdelani);

                                                
//            } else {
//                $this->addFlashMessage("Možné typy nabízených pozic  smí odstraňovat pouze ....");
//            }           
//        }
                
        return $this->redirectSeeLastGet($request);
    }
            
    
  
 }   