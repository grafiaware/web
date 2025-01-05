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

use Events\Model\Entity\JobInterface;
use Events\Model\Entity\Job;
use Events\Model\Entity\JobToTag;
use Events\Model\Entity\JobToTagInterface;
use Events\Model\Entity\JobTag;
use Events\Model\Entity\JobTagInterface;
use Events\Model\Entity\PozadovaneVzdelaniInterface;
use Events\Model\Entity\PozadovaneVzdelani;


use Psr\Http\Message\ServerRequestInterface;
use Pes\Http\Helper\RequestStatus;
use Pes\Http\Request\RequestParams;
use Pes\Http\Factory\ResponseFactory;
use Pes\Http\Response;

use UnexpectedValueException;

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
    
    private function hasReprePermission($companyId) {
        $permitted = false; 
        /** @var StatusSecurityRepo $statusSecurityRepo */
        $statusSecurity = $this->statusSecurityRepo->get();        
        $representativeActions = $statusSecurity->getRepresentativeActions();
        if (isset($representativeActions)) {
            $representative = $representativeActions->getRepresentative();
            if (isset($representative)) {
                $permitted = ($representative->getCompanyId()==$companyId);
            }
        }
        return $permitted || $this->hasAdminPermission();
    }
    
    private function hasAdminPermission() {
        /** @var StatusSecurityRepo $statusSecurityRepo */
        $statusSecurity = $this->statusSecurityRepo->get();    
        /** @var LoginAggregateFullInterface $loginAggregateCredentials */
        $loginAggregateCredentials = $statusSecurity->getLoginAggregate();            
        $role = $loginAggregateCredentials->getCredentials()->getRoleFk() ?? '';   
        $permitted = ($role==ConfigurationCache::auth()['roleEventsAdministrator']);
        return $permitted;
    }
    
    private function createUnathorizedResponse() {
        $response = (new ResponseFactory())->createResponse();
        return $response->withStatus(401);  // Unauthorized        
    }
    
    /**
     * 
     * @param ServerRequestInterface $request
     * @param type $idCompany
     * @return type
     */
    public function addJob (ServerRequestInterface $request, $idCompany) {                 
        if ($this->hasReprePermission($idCompany)) {
            /** @var JobInterface $job */
            $job =  new Job(); //new $job
            $job->setCompanyId($idCompany);
            $this->hydrateJobData($request, $job);
            $this->jobRepo->add($job);
        } else {
            $this->addFlashMessage("Nemáte oprávnění měnit údaje o pracovní pozici.");
        }
        return $this->redirectSeeLastGet($request);        
    }
    
    /**
     * 
     * @param ServerRequestInterface $request
     * @param type $idCompany
     * @param type $jobId
     * @return type
     */
    public function updateJob (ServerRequestInterface $request, $idCompany, $jobId) {                          
        if ($this->hasReprePermission($idCompany)) {
            /** @var JobInterface $job */
            $job = $this->jobRepo->get( $jobId );
            $this->hydrateJobData($request, $job);
            $this->processJobToTag($request, $jobId);
        } else {
            $this->addFlashMessage("Nemáte oprávnění měnit údaje o pracovní pozici.");
        }
//        return $this->redirectSeeLastGet($request);
        return $this->createPutNoContentResponse();
    }

    private function hydrateJobData(ServerRequestInterface $request, JobInterface $job) {
        // POST formularova data         
        //not null
        $job->setPozadovaneVzdelaniStupen((new RequestParams())->getParsedBodyParam($request, 'pozadovane-vzdelani-stupen'));
        $job->setNazev((new RequestParams())->getParsedBodyParam($request, 'nazev'));
        $job->setMistoVykonu((new RequestParams())->getParsedBodyParam($request, 'misto-vykonu'));
        $job->setPopisPozice((new RequestParams())->getParsedBodyParam($request, 'popis-pozice'));
        $job->setPozadujeme((new RequestParams())->getParsedBodyParam($request, 'pozadujeme'));
        $job->setNabizime((new RequestParams())->getParsedBodyParam($request, 'nabizime'));           
    }
                
   
    /**
     * 
     * @param ServerRequestInterface $request
     * @param type $idCompany
     * @param type $idJob
     * @return type
     */
    public function removeJob (ServerRequestInterface $request, $idCompany, $idJob) {                           
        if ($this->hasReprePermission($idCompany)) {       
            /** @var JobInterface $job */
            $job = $this->jobRepo->get( $idJob );                                
            $this->jobRepo->remove( $job ); 
        } else {
            $this->addFlashMessage("Nemáte oprávnění měnit údaje o pracovní pozici.");
        }
        return $this->redirectSeeLastGet($request);        
    }
            
            
    /**
     * 
     * @param ServerRequestInterface $request
     * @param type $jobId
     * @return type
     */
    public function processingJobToTag (ServerRequestInterface $request, $jobId) {   
        /** @var JobInterface $job */
        $job = $this->jobRepo->get( $jobId );         
        if ($this->hasReprePermission($job->getCompanyId())) {
            $this->processJobToTag($request, $jobId);
            return $this->createPutNoContentResponse();
        } else {
            $this->addFlashMessage("Nemáte oprávnění měnit údaje o pracovní pozici.");
            return $this->redirectSeeLastGet($request);
        }        
    }
    
    private function processJobToTag(ServerRequestInterface $request, $jobId) {
        $arrayJobTagIds_ForJob = [];
        $allJobToTags_ForJob = $this->jobToTagRepo->findByJobId($jobId);
        /** @var JobToTagInterface $jobToTag */
        foreach ($allJobToTags_ForJob as $jobToTag) {   
            $arrayJobTagIds_ForJob[] = $jobToTag->getJobTagId() ; 
        }                                
        $allTags = $this->jobTagRepo->findAll(); //vsechny tag co existuji
        $data = (new RequestParams())->getParsedBodyParam($request, "data" );
        if(!is_array($data)) {
            throw new UnexpectedValueException("No suitable data from request.");
        }
        /** @var JobTagInterface $tag */
        foreach ($allTags as $tagEntity) {
            // $postTag - tento tag je zaskrtnut ve form
            $postTagId = $data[$tagEntity->getTag()];                                        
            if (isset ($postTagId) ) { // je zaskrtnut ve form
                //je-li v jobToTag - ok, nic    //neni-li v jobToTag - zapsat do jobToTag 
                if (!(in_array($postTagId,  $arrayJobTagIds_ForJob))) {                                                                            
                    /** @var JobToTag $newJobToTag */
                    $newJobToTag = new JobToTag(); //new 
                    $newJobToTag->setJobId($jobId); 
                    $newJobToTag->setJobTagId($postTagId);
                    $this->jobToTagRepo->add($newJobToTag);
                }                        
            }
            else { // neni zaskrtnut ve form                                      
                //je-li v jobToTag  - vymazat z jobToTag  //neni-li v jobToTag - ok, nic
                if (in_array($tagEntity->getId(), $arrayJobTagIds_ForJob)) {
                   $jobToTagEntity = $this->jobToTagRepo->get($jobId, $tagEntity->getId());
                   $this->jobToTagRepo->remove($jobToTagEntity);
                }
            }                 
        }                     
    }
    
    
    
    /**
     * 
     * @param ServerRequestInterface $request
     * @return type
     */
    public function addJobTag (ServerRequestInterface $request) {                              
        if ($this->hasAdminPermission()) {
            /** @var JobTagInterface $tag */
            $tag = new JobTag();  
            $tag->setTag((new RequestParams())->getParsedBodyParam($request, 'tag') );              
            $tag->setColor((new RequestParams())->getParsedBodyParam($request, 'color') );              
            $this->jobTagRepo->add($tag);             
        } else {
            $this->addFlashMessage("Nemáte oprávnění měnit údaje o pracovní pozici.");
        }
        return $this->redirectSeeLastGet($request);        
    }
    
    
    /**
     * 
     * @param ServerRequestInterface $request
     * @param type $id
     */
    public function updateJobTag (ServerRequestInterface $request, $id) {                    
        if ($this->hasAdminPermission()) {
            /** @var JobTagInterface $tag */
            $tag = $this->jobTagRepo->get($id);       
            $tag->setTag((new RequestParams())->getParsedBodyParam($request, 'tag') );                        
            $tag->setColor((new RequestParams())->getParsedBodyParam($request, 'color') );              
        } else {
            $this->addFlashMessage("Nemáte oprávnění měnit údaje o pracovní pozici.");
        }
        return $this->redirectSeeLastGet($request);        
    }    
      

    /**
     * 
     * @param ServerRequestInterface $request
     * @param type $tag
     * @return type
     */
    public function removeJobTag (ServerRequestInterface $request, $id) {                   
        if ($this->hasAdminPermission()) {
            /** @var JobTagInterface $tag */
            $tag = $this->jobTagRepo->get( $id );                                
            $this->jobTagRepo->remove( $tag );         
        } else {
            $this->addFlashMessage("Nemáte oprávnění měnit údaje o pracovní pozici.");
        }
        return $this->redirectSeeLastGet($request);        
    }
            
    
    
    
        
    /**
     * 
     * @param ServerRequestInterface $request
     * @return type
     */
    public function addPozadovaneVzdelani (ServerRequestInterface $request) {                 
        if ($this->hasAdminPermission()) {
            /** @var PozadovaneVzdelaniInterface $pozadovaneVzdelani */
            $pozadovaneVzdelani = new PozadovaneVzdelani() ; //new    
            $pozadovaneVzdelani->setStupen((new RequestParams())->getParsedBodyParam($request, 'stupen') );
            $pozadovaneVzdelani->setVzdelani((new RequestParams())->getParsedBodyParam($request, 'vzdelani') );              
            $this->pozadovaneVzdelaniRepo->add($pozadovaneVzdelani);             
        } else {
            $this->addFlashMessage("Nemáte oprávnění měnit údaje o pracovní pozici.");
        }
        return $this->redirectSeeLastGet($request);           
    }
    
    /**
     * 
     * @param ServerRequestInterface $request
     * @param type $stupen
     * @return type
     */
    public function updatePozadovaneVzdelani (ServerRequestInterface $request, $stupen) {                    
        if ($this->hasAdminPermission()) {
            /** @var PozadovaneVzdelaniInterface $pozadovaneVzdelani */
            $pozadovaneVzdelani = $this->pozadovaneVzdelaniRepo->get($stupen);            
            $pozadovaneVzdelani->setVzdelani((new RequestParams())->getParsedBodyParam($request, 'vzdelani') );
        } else {
            $this->addFlashMessage("Nemáte oprávnění měnit údaje o pracovní pozici.");
        }
        return $this->redirectSeeLastGet($request);               
    }    
      
    /**
     * 
     * @param ServerRequestInterface $request
     * @param type $stupen
     * @return type
     */
    public function removePozadovaneVzdelani (ServerRequestInterface $request, $stupen) {                   
        if ($this->hasAdminPermission()) {
            /** @var PozadovaneVzdelaniInterface $pozadovaneVzdelani */
            $pozadovaneVzdelani = $this->pozadovaneVzdelaniRepo->get($stupen);            
            $this->pozadovaneVzdelaniRepo->remove($pozadovaneVzdelani);
        } else {
            $this->addFlashMessage("Nemáte oprávnění měnit údaje o pracovní pozici.");
        }
        return $this->redirectSeeLastGet($request);        
    }
 }   