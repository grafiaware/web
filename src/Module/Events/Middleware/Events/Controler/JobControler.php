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
use Events\Model\Repository\RepresentativeRepo;
use Events\Model\Repository\CompanyRepoInterface;
use Events\Model\Repository\VisitorJobRequestRepo;
use Events\Model\Repository\JobRepoInterface;
use Events\Model\Repository\DocumentRepoInterface;

use Events\Model\Entity\RepresentativeInterface;
use Events\Model\Entity\Representative;
use Events\Model\Entity\Company;
use Events\Model\Entity\Job;
use Events\Model\Entity\VisitorJobRequest;
use Events\Model\Entity\VisitorJobRequestInterface;


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
     * @var VisitorJobRequestRepoInterface
     */
    private $visitorJobRequestRepo;

    /**
     * @var DocumentRepoInterface
     */
    private $documentRepo;
    
       
    /**
     * 
     * @param StatusSecurityRepo $statusSecurityRepo
     * @param StatusFlashRepo $statusFlashRepo
     * @param StatusPresentationRepo $statusPresentationRepo
     * @param CompanyRepoInterface $companyRepo
     * @param RepresentativeRepoInterface $representativeRepo
     * @param VisitorJobRequestRepo $visitorJobRequestRepo
     * @param JobRepoInterface $jobRepo
     * @param DocumentRepoInterface $documentRepo
     */
    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusFlashRepo $statusFlashRepo,
            StatusPresentationRepo $statusPresentationRepo,
            
            CompanyRepoInterface $companyRepo,           
            RepresentativeRepoInterface $representativeRepo,
            VisitorJobRequestRepo $visitorJobRequestRepo,            
            JobRepoInterface $jobRepo,
            DocumentRepoInterface $documentRepo
            
            ) {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo);
        $this->companyRepo = $companyRepo;     
        $this->representativeRepo = $representativeRepo;
        $this->visitorJobRequestRepo = $visitorJobRequestRepo;
        $this->jobRepo = $jobRepo;
        $this->documentRepo = $documentRepo;
    }
    
   
    /**
     * Representative odesílá mailem sobě.
     * 
     * @param ServerRequestInterface $request
     * @return type
     */
    public function sendJobRequest(ServerRequestInterface $request, $visitorLoginName, $jobId) {
        $isRepresentative = false;
        
        /** @var StatusSecurityRepo $statusSecurityRepo */
        $statusSecurity = $this->statusSecurityRepo->get();
        /** @var LoginAggregateFullInterface $loginAggregateCredentials */
        $loginAggregateCredentials = $statusSecurity->getLoginAggregate();                   
        
        if (!isset($loginAggregateCredentials)) {
            $response = (new ResponseFactory())->createResponse();
            return $response->withStatus(401);  // Unaathorized
        } else {
           
            $loginName = $loginAggregateCredentials->getLoginName();  //prihlaseny          
            $role = $loginAggregateCredentials->getCredentials()->getRole() ?? ''; 
            //$companyId = (new RequestParams())->getParsedBodyParam($request, "company-id");   //z POST  ---zjistit z jpbId
            
            if (isset($role) AND ($role==ConfigurationCache::loginLogoutController()['roleRepresentative'])   ) {                
                /**  @var  Job $job  */
                $job = $this->jobRepo->get($jobId);
                if ( $this->representativeRepo->get($loginName, $job->getCompanyId()) )  {                                          
                    $isRepresentative = true; 
                }    
            }                
            
            if ($isRepresentative) {        
                /** @var VisitorJobRequestInterface  $visitorDataJRqPost */
                $visitorDataJRqPost = $this->visitorJobRequestRepo->get($visitorLoginName, $jobId);
              
                if (!isset($visitorDataJRqPost)) {
                    $this->addFlashMessage("Pracovní údaje pro pozici  nenalezeny v databázi.");
                } else {
                    $positionName = $visitorDataJRqPost->getPositionName();
              /**/  $this->sendMail($positionName, $visitorDataJRqPost, $loginAggregateCredentials);
                    $this->addFlashMessage("Pracovní údaje pro pozici " .  $visitorDataJRqPost->getPositionName() . " neodeslány.**Momentálně se maily neposílají.**");
                }
            } else {
                $this->addFlashMessage("Pracovní údaje smí mailem odesílat pouze vystavovatel.");
            }
        }
        return $this->redirectSeeLastGet($request);
    }

    
    
    private function sendMail($positionName, 
                              VisitorJobRequestInterface $visitorDataPost,
                              LoginAggregateFullInterface $loginAggregateCredentials) {
        /** @var Mail $mail */
        $mail = $this->container->get(Mail::class);
        /** @var HtmlMessage $mailMessageFactory */
        $mailMessageFactory = $this->container->get(HtmlMessage::class);
        $subject =  'Veletrh práce - pracovní údaje návštěvníka ';
        $body = $mailMessageFactory->create(__DIR__."/Messages/pracovni-udaje-navstevnika.php",
                                            [
                                                'positionName' => $positionName,
                                                'visitorDataPost' => $visitorDataPost]);
        
        $attachments=[];        
        // getCvDocument() dava id  tabulky document, a v ni je teprve ulozen document
        if ($visitorDataPost->getCvDocument()) {
            $cvDocument = $this->documentRepo->get($visitorDataPost->getCvDocument());
            if ($cvDocument) {
                $attachments[] = (new StringAttachment())
                            ->setStringAttachment($cvDocument->getDocument())                                       
                            ->setAltText($cvDocument->getDocumentFilename());
            }
        }
        if ($visitorDataPost->getLetterDocument()) {
            $letterDocument = $this->documentRepo->get($visitorDataPost->getLetterDocument());
            if ($letterDocument) {
                $attachments[] = (new StringAttachment())
                            ->setStringAttachment($letterDocument->getDocument())     
                            ->setAltText($letterDocument->getDocumentFilename());
            } 
        }
        
        $presenterLogiName = $loginAggregateCredentials->getCredentials()->getLoginNameFk();
        $params = (new Params())
                    ->setContent(  (new Content())
                                     ->setSubject($subject)
                                     ->setHtml($body)
                                     ->setAttachments($attachments)
                                )
                    ->setParty  (  (new Party())
                                     ->setFrom('it.grafia@gmail.com', 'veletrhprace.online')
                                     ->addTo($loginAggregateCredentials->getRegistration()->getEmail(), $presenterLogiName.' veletrhprace.online')
                                     ->addTo('svoboda@grafia.cz', $presenterLogiName.' veletrhprace.online')
                                );
        
        //$mail->mail($params); // posle mail
        $this->addFlashMessage("!!! Momentálně se maily neposílají !!!");
    }    
    
    
    
 }   