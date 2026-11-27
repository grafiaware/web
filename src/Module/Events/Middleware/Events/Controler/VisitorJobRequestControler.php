<?php

namespace Events\Middleware\Events\Controler;

use Site\ConfigurationCache;
use Mail\MailSettings;
use FrontControler\FrontControlerAbstract;
use Auth\Model\Entity\LoginAggregateFullInterface;

//use Red\Model\Entity\LoginAggregateFullInterface;
use Pes\View\Renderer\PhpTemplateRendererInterface;
/** @var PhpTemplateRendererInterface $this */

use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusFlashRepo;
use Status\Model\Repository\StatusPresentationRepo;

use Events\Model\Repository\VisitorProfileRepo;
use Events\Model\Repository\VisitorProfileRepoInterface;

use Events\Model\Repository\VisitorJobRequestRepo;
use Events\Model\Repository\VisitorJobRequestRepoInterface;

use Events\Model\Repository\DocumentRepo;
use Events\Model\Repository\DocumentRepoInterface;

use Events\Model\Repository\RepresentativeRepoInterface;
//use Events\Model\Repository\RepresentativeRepo;

use Events\Model\Entity\VisitorProfile;
use Events\Model\Entity\VisitorProfileIntertface;
use Events\Model\Entity\Document;
use Events\Model\Entity\DocumentInterface;
use Events\Model\Entity\VisitorJobRequest;
use Events\Model\Entity\VisitorJobRequestInterface;
use Events\Model\Repository\JobRepo;
use Events\Model\Repository\JobRepoInterface;
use Events\Model\Entity\Job;
use Events\Model\Entity\JobInterface;

use Status\Model\Enum\FlashSeverityEnum;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use Pes\Http\Helper\RequestStatus;
use Pes\Http\Request\RequestParams;
use Pes\Http\Factory\ResponseFactory;
use Pes\Http\Response;

use Pes\Mail\MailInterface;
use Mail\MessageFactory\HtmlMessage;
use Pes\Mail\Assembly;
use Pes\Mail\Assembly\Content;
use Pes\Mail\Assembly\Attachment; 
use Pes\Mail\Assembly\StringAttachment;
use Pes\Mail\Assembly\Party;

use Access\Enum\RoleEnum;
use Events\Service\JobRequest\JobRequestDocuments;
use Events\Service\JobRequest\JobRequestMailer;
use Events\Service\Lottery\AuthLookup;



/**
 * 
 */
class VisitorJobRequestControler extends FrontControlerAbstract {

    const UPLOADED_KEY_CV = JobRequestDocuments::UPLOADED_KEY_CV;
    const UPLOADED_KEY_LETTER = JobRequestDocuments::UPLOADED_KEY_LETTER;

//    private $multiple = false;
//    private $accept;

    /**
     * 
     * @var VisitorProfileRepoInterface
     */
    private $visitorProfileRepo;
    /**
     * 
     * @var VisitorJobRequestRepoInterface
     */
    private $visitorJobRequestRepo;
    /**
     * 
     * @var DocumentRepoInterface
     */
    private $documentRepo;
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
     * @param StatusSecurityRepo $statusSecurityRepo
     * @param StatusFlashRepo $statusFlashRepo
     * @param StatusPresentationRepo $statusPresentationRepo
     * @param VisitorProfileRepoInterface $visitorProfileRepo
     * @param VisitorJobRequestRepoInterface $visitorJobRequesRepo
     * @param DocumentRepoInterface $documentRepo
     * @param RepresentativeRepoInterface $representativeRepo
     */
    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusFlashRepo $statusFlashRepo,
            StatusPresentationRepo $statusPresentationRepo,
            
            VisitorProfileRepoInterface $visitorProfileRepo,
            VisitorJobRequestRepoInterface $visitorJobRequesRepo,          
            DocumentRepoInterface $documentRepo,
            RepresentativeRepoInterface $representativeRepo,
            JobRepoInterface $jobRepo,
            private JobRequestDocuments $jobRequestDocuments,
            private JobRequestMailer $jobRequestMailer,
            private AuthLookup $authLookup
            ) {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo);
        $this->visitorProfileRepo = $visitorProfileRepo;
        $this->visitorJobRequestRepo = $visitorJobRequesRepo;        
        $this->documentRepo = $documentRepo;
        $this->representativeRepo = $representativeRepo;
        $this->jobRepo =  $jobRepo;
    }

        
    
    
    
    /**
     * Visitor 'odesílá' data pro zaměstnavatele - vytváří se visitorJobRequest
     *     
     * @param ServerRequestInterface $request
     * @param type $jobId
     * @return type
     */
    public function addRequest(ServerRequestInterface $request, $jobId) {
        $loginAggregateCredentials = $this->statusSecurityRepo->get()->getLoginAggregate();
        if (!isset($loginAggregateCredentials)) {
            return (new ResponseFactory())->createResponse()->withStatus(401);  // Unathorized
        }
        $loginName = $loginAggregateCredentials->getLoginName();
        if ($loginAggregateCredentials->getCredentials()?->getRoleFk() !== RoleEnum::VISITOR) {
            $this->addFlashMessage("Zájem o pozici může odeslat jen přihlášený návštěvník.", FlashSeverityEnum::WARNING);
            return $this->redirectSeeLastGet($request);
        }
        /** @var JobInterface  $job */
        $job = $this->jobRepo->get($jobId);
        if (!isset($job)) {
            $this->addFlashMessage("Pozice nenalezena.", FlashSeverityEnum::WARNING);
            return $this->redirectSeeLastGet($request);
        }
        $visitorProfile = $this->visitorProfileRepo->get($loginName);
        if (!isset($visitorProfile)) {
            $this->addFlashMessage("Zájem o pozici může odeslat jen návštěvník s vyplněným profilem.", FlashSeverityEnum::WARNING);
            return $this->redirectSeeLastGet($request);
        }
        if (null !== $this->visitorJobRequestRepo->get($loginName, $jobId)) {
            $this->addFlashMessage("Zájem o tuto pozici byl již odeslán dříve.", FlashSeverityEnum::WARNING);
            return $this->redirectSeeLastGet($request);
        }

        $jobRequest = new VisitorJobRequest();
        // loginName a jobId je složený id
        $jobRequest->setLoginLoginName($loginName);
        $jobRequest->setJobId($jobId);
        $this->hydrateJobRequest($request, $jobRequest);
        $visitorEmail = $this->authLookup->emailOf($loginName);
        if (isset($visitorEmail)) {
            $jobRequest->setEmail($visitorEmail);   // e-mail z registrace, ne z formuláře (pole je jen pro čtení)
        }
        // #174: dokumenty – nahraný soubor má přednost, jinak kopie z profilu; před add(), zájem se ukládá hned
        $documentMessages = $this->jobRequestDocuments->attach($jobRequest, $visitorProfile,
                $request->getUploadedFiles() ?? [], (string) $loginAggregateCredentials->getLoginNameHash());
        $this->visitorJobRequestRepo->add($jobRequest);

        $this->addFlashMessage("Zájem o pozici {$job->getNazev()} odeslán.", FlashSeverityEnum::SUCCESS);
        foreach ($documentMessages as $message) {
            $this->addFlashMessage($message, FlashSeverityEnum::WARNING);
        }
        if (\Application\Feature\FeatureFlags::fromConfiguration()->isEnabled('vp.zajem-email')) {
            $this->notifyJobRequest($jobRequest, $job, $visitorEmail);
        }
        return $this->redirectSeeLastGet($request);
    }

    /**
     * #173: e-mail se zájmem o pozici všem zástupcům firmy (adresa z registrace) a potvrzení návštěvníkovi.
     * Zájem je uložený i při chybě odeslání – zástupce ho vidí v profilu, může si ho poslat znovu (#172).
     */
    private function notifyJobRequest(VisitorJobRequestInterface $jobRequest, JobInterface $job, ?string $visitorEmail): void {
        $failed = false;
        foreach ($this->representativeRepo->findByCompany($job->getCompanyId()) as $representative) {
            $representativeLogin = $representative->getLoginLoginName();
            $email = $this->authLookup->emailOf($representativeLogin);
            if (isset($email)) {
                try {
                    $this->jobRequestMailer->sendToRepresentative($jobRequest, $job, $email, $representativeLogin);
                } catch (\Throwable $e) {
                    $failed = true;
                }
            }
        }
        if ($failed) {
            $this->addFlashMessage("E-mail zaměstnavateli se nepodařilo odeslat. Zájem je uložen, zaměstnavatel ho uvidí na webu.", FlashSeverityEnum::WARNING);
        }
        if (isset($visitorEmail)) {
            try {
                $this->jobRequestMailer->sendConfirmationToVisitor($jobRequest, $job, $visitorEmail);
            } catch (\Throwable $e) {
                $this->addFlashMessage("Potvrzení na váš e-mail se nepodařilo odeslat.", FlashSeverityEnum::WARNING);
            }
        }
    }

    public function updateRequest(ServerRequestInterface $request, $jobId, $loginloginname) {
        $statusSecurity = $this->statusSecurityRepo->get();
        $loginAggregateCredentials = $statusSecurity->getLoginAggregate();
        if (!isset($loginAggregateCredentials)) {
            $response = (new ResponseFactory())->createResponse()->withStatus(401);  // Unathorized
        } else {
            $loginName = $loginAggregateCredentials->getLoginName();         
            /** @var JobInterface  $job */                     
            $job = $this->jobRepo->get($jobId);
            $jobRequest = $this->visitorJobRequestRepo->get($loginName, $jobId);             

            if (!isset($jobRequest)) {
                $this->addFlashMessage("Zájem o tuto pozici byl již odeslán dříve.", FlashSeverityEnum::WARNING);  // error
            } else {
                $this->hydrateJobRequest($request, $jobRequest);
                
//                // z post dat
//                $files = $request->getUploadedFiles();  
//                /** @var UploadedFileInterface $file */
//                if(isset($files) AND $files) {  //pro uploadovane soubory vyrobi documenty z post dat a zaradi do visitorJobRequest
//                        $this->processingDocs($files, $visitorJobRequestData);  
//                }
//                // ve $visitorJobRequestData je id příslušného documentu, byl-li zadan ve formulari
//                                                
//                // pokud neni jiz naplneno z Post dat, documenty z VisitorProfile, pokud existuji, -  pridat "kopie" do visitorJobRequest
//                //$pomcv = $visitorJobRequestData->getCvDocument();                     
//                if (!$visitorJobRequestData->getCvDocument()) {    // neni naplneno z uploadu? - naplnit z profilu                                                                            
//                    if  ($visitorProfileData->getCvDocument() ) {  // cv je v profilu? -ano
//                        /** @var DocumentInterface $cvDocument */
//                        $cvDocument = $this->container->get(Document::class);    // pozn. potřebuju  každý document jiný -getFactoriesDefinitions
//                        $this->documentRepo->add($cvDocument);   
//                        
//                        $cvDocumentIdFromProfile = $visitorProfileData->getCvDocument();
//                        $cvDocumentFromProfile = $this->documentRepo->get($cvDocumentIdFromProfile);                  
//
//                        $cvDocument->setContent($cvDocumentFromProfile->getContent());
//                        $cvDocument->setDocumentFilename($cvDocumentFromProfile->getDocumentFilename());
//                        $cvDocument->setDocumentMimetype($cvDocumentFromProfile->getDocumentMimetype());
//
//                        $visitorJobRequestData->setCvDocument($cvDocument->getId());
//                    }    
//                } 
//                
//                //$pomletter = $visitorJobRequestData->getLetterDocument();
//                if ( !$visitorJobRequestData->getLetterDocument() ) {  // neni naplneno z uploadu? - naplnit z profilu
//                    if ($visitorProfileData->getLetterDocument() ) {
//                        /** @var DocumentInterface $letterDocument */
//                        $letterDocument =  $this->container->get(Document::class);     // pozn. potřebuju  každý document jiný -getFactoriesDefinitions
//                        $this->documentRepo->add($letterDocument);                    
//                        
//                        $letterDocumentIdFromProfile = $visitorProfileData->getLetterDocument();                    
//                        $letterDocumentFromProfile = $this->documentRepo->get($letterDocumentIdFromProfile);     
//
//                        $letterDocument->setContent($letterDocumentFromProfile->getContent());
//                        $letterDocument->setDocumentFilename($letterDocumentFromProfile->getDocumentFilename());
//                        $letterDocument->setDocumentMimetype($letterDocumentFromProfile->getDocumentMimetype());                    
//
//                        $visitorJobRequestData->setLetterDocument($letterDocument->getId());
//                    }
//                }             
                           
                $this->addFlashMessage("Pracovní údaje odeslány pro pozici" . $job->getNazev(). "." );
            }            
            
            return $this->redirectSeeLastGet($request);
        }
    }    
    private function hydrateJobRequest(ServerRequestInterface $request, VisitorJobRequestInterface $jobRequest) {
                $requestParams = new RequestParams();        
                $jobRequest->setPrefix($requestParams->getParsedBodyParam($request, 'prefix'));
                $jobRequest->setName($requestParams->getParsedBodyParam($request, 'name'));
                $jobRequest->setSurname($requestParams->getParsedBodyParam($request, 'surname'));
                $jobRequest->setPostfix($requestParams->getParsedBodyParam($request, 'postfix'));
                $jobRequest->setEmail($requestParams->getParsedBodyParam($request, 'email'));
                $jobRequest->setPhone($requestParams->getParsedBodyParam($request, 'phone'));
                $jobRequest->setCvEducationText($requestParams->getParsedBodyParam($request, 'cv-education-text'));
                $jobRequest->setCvSkillsText($requestParams->getParsedBodyParam($request, 'cv-skills-text'));                                        
    }
    
     /**
     * Representative odesílá mailem sobě.    
     * @param ServerRequestInterface $request
     * @param type $visitorLoginName
     * @param type $jobId
     * @return type
     */
    public function sendJobRequest(ServerRequestInterface $request, $jobId, $visitorLoginName) {   // #172: pořadí podle routy /job/:jobId/jobrequest/:loginloginname/send          
        $isRepresentative = false;
        
        /** @var StatusSecurityRepo $statusSecurityRepo */
        $statusSecurity = $this->statusSecurityRepo->get();
        /** @var LoginAggregateFullInterface $loginAggregateFull */
        $loginAggregateFull = $statusSecurity->getLoginAggregate();                   
        
        if (!isset($loginAggregateFull)) {
            $response = (new ResponseFactory())->createResponse();
            return $response->withStatus(401);  // Unathorized
        } else {
            $namePrihlasenehoRepresentanta = $loginAggregateFull->getLoginName();  //prihlaseny          
            $role = $loginAggregateFull->getCredentials()->getRoleFk() ?? ''; 
            
            if (isset($role) AND ($role==ConfigurationCache::auth()['roleRepresentative'])   ) {                
                /**  @var  Job $job  */
                $job = $this->jobRepo->get($jobId);
                if ( isset($job) AND $this->representativeRepo->get($namePrihlasenehoRepresentanta, $job->getCompanyId()) )  {                                          
                    $isRepresentative = true;                                           
                    $mailPrihlasenehoRepresentanta = $loginAggregateFull->getRegistration()->getEmail();            
                }    
            }                         
                        
            if ($isRepresentative) {        
                /** @var VisitorJobRequestInterface  $visitorJobRequest */
                $visitorJobRequest = $this->visitorJobRequestRepo->get($visitorLoginName, $jobId);
                /** @var JobInterface  $job */                     
                $job = $this->jobRepo->get($jobId);
                if (!isset($visitorJobRequest)) {
                    $this->addFlashMessage("Pracovní údaje návštěvníka " . $visitorLoginName . "  nenalezeny v databázi.");
                } else {                                                                                   
                    //$this->addFlashMessage("Budu mailem odesílat.");  
                    try {
                        $this->jobRequestMailer->sendToRepresentative($visitorJobRequest, $job,
                                $mailPrihlasenehoRepresentanta, $namePrihlasenehoRepresentanta);
                    } catch (\Throwable $e) {
                        $this->addFlashMessage("E-mail se nepodařilo odeslat.", FlashSeverityEnum::WARNING);
                        return $this->redirectSeeLastGet($request);
                    }
                    $this->addFlashMessage("Pracovní údaje pro pozici " .  $job->getNazev() . " odeslány.");
                }                                                                                                                           
                
            } else {
                $this->addFlashMessage("Pracovní údaje smí mailem odesílat pouze vystavovatel.");
            }        
        
        }        
                                        
        return $this->redirectSeeLastGet($request);
    }
}
