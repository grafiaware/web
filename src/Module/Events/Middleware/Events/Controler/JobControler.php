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

use Events\Model\Entity\RepresentativeInterface;
use Events\Model\Entity\Representative;
use Events\Model\Entity\JobInterface;
use Events\Model\Entity\Job;

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
     * @param StatusSecurityRepo $statusSecurityRepo
     * @param StatusFlashRepo $statusFlashRepo
     * @param StatusPresentationRepo $statusPresentationRepo
     * @param CompanyRepoInterface $companyRepo
     * @param RepresentativeRepoInterface $representativeRepo
     * @param PozadovaneVzdelaniRepoInterface $pozadovaneVzdelaniRepo
     * @param JobRepoInterface $jobRepo
     */
    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusFlashRepo $statusFlashRepo,
            StatusPresentationRepo $statusPresentationRepo,
            
            CompanyRepoInterface $companyRepo,           
            RepresentativeRepoInterface $representativeRepo,
            PozadovaneVzdelaniRepoInterface $pozadovaneVzdelaniRepo,            
            JobRepoInterface $jobRepo
            
            ) {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo);
        $this->companyRepo = $companyRepo;     
        $this->representativeRepo = $representativeRepo;
        $this->pozadovaneVzdelaniRepo = $pozadovaneVzdelaniRepo;
        $this->jobRepo = $jobRepo;
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
                
                $this->jobRepo->add($job);
                
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
            
    
    
    
    
    
    
    
    
 }   