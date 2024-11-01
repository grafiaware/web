<?php

namespace Events\Middleware\Events\Controler;

use Site\ConfigurationCache;
use FrontControler\PresentationFrontControlerAbstract;
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
class VisitorJobRequestControler extends PresentationFrontControlerAbstract {

    const UPLOADED_KEY_CV = "visitor-cv";
    const UPLOADED_KEY_LETTER = "visitor-letter";

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
            JobRepo $jobRepo
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
    public function jobRequest(ServerRequestInterface $request, $jobId) {
        $statusSecurity = $this->statusSecurityRepo->get();
        $loginAggregateCredentials = $statusSecurity->getLoginAggregate();
        if (!isset($loginAggregateCredentials)) {
            $response = (new ResponseFactory())->createResponse();
            $response = $response->withStatus(401);  // Unaathorized
        } else {
            $loginName = $loginAggregateCredentials->getLoginName();

            // POST data prisly
            //$shortName = (new RequestParams())->getParsedBodyParam($request, 'short-name');
            // $positionName = (new RequestParams())->getParsedBodyParam($request, 'position-name');
            
            /** @var Job  $job */                     
            $job = $this->jobRepo->get($jobId);

            $visitorProfileData = $this->visitorProfileRepo->get($loginName); // existuje vzdy
            $visitorJobRequestDataExist = $this->visitorJobRequestRepo->get($loginName, $jobId /*$shortName, $positionName*/ );             
                                               
            // ################################### 
            // neexistuje jobrequest pro tento job, a to vzdy, protoze
            // Pozn.: existuje-li jobrequest - tak tudy vubec nejdu.... zadost se da poslat jen 1x, pak tam neni uz tlacitko 
            // ######################
            //-- kdyz je document v profileData  - i tak vyrobit document novy+add do documentRepo a v jobrequestu updatovat id a
            //   do noveho documentu presunout data ze stavajiciho dokumentu , ktery je v profilu
            //-- kdyz neni dokument v profileData - vyrobit document novy+add do documentRepo, a do jobRequestu (id, tj. letter_document, cv_document) 
               
            if (!isset($visitorJobRequestDataExist)) {   //plati vzdy                  
                $visitorJobRequestData = $this->container->get(VisitorJobRequest::class);  //new VisitorJobRequest();
                $visitorJobRequestData->setLoginLoginName($loginName);
                $visitorJobRequestData->setJobId($jobId);
                $visitorJobRequestData->setPositionName( $job->getNazev() /* $positionName */ );
                $this->visitorJobRequestRepo->add($visitorJobRequestData);
                
                // z post dat
                $files = $request->getUploadedFiles();  
                /** @var UploadedFileInterface $file */
                if(isset($files) AND $files) {  //pro uploadovane soubory vyrobi documenty z post dat a zaradi do visitorJobRequest
                        $this->processingDocs($files, $visitorJobRequestData);  
                }
                // ve $visitorJobRequestData je id příslušného documentu, byl-li zadan ve formulari
                                                
                // pokud neni jiz naplneno z Post dat, documenty z VisitorProfile, pokud existuji, -  pridat "kopie" do visitorJobRequest
                //$pomcv = $visitorJobRequestData->getCvDocument();                     
                if (!$visitorJobRequestData->getCvDocument()) {    // neni naplneno z uploadu? - naplnit z profilu                                                                            
                    if  ($visitorProfileData->getCvDocument() ) {  // cv je v profilu? -ano
                        /** @var DocumentInterface $cvDocument */
                        $cvDocument = $this->container->get(Document::class);    // pozn. potřebuju  každý document jiný -getFactoriesDefinitions
                        $this->documentRepo->add($cvDocument);   
                        
                        $cvDocumentIdFromProfile = $visitorProfileData->getCvDocument();
                        $cvDocumentFromProfile = $this->documentRepo->get($cvDocumentIdFromProfile);                  

                        $cvDocument->setContent($cvDocumentFromProfile->getContent());
                        $cvDocument->setDocumentFilename($cvDocumentFromProfile->getDocumentFilename());
                        $cvDocument->setDocumentMimetype($cvDocumentFromProfile->getDocumentMimetype());

                        $visitorJobRequestData->setCvDocument($cvDocument->getId());
                    }    
                } 
                
                //$pomletter = $visitorJobRequestData->getLetterDocument();
                if ( !$visitorJobRequestData->getLetterDocument() ) {  // neni naplneno z uploadu? - naplnit z profilu
                    if ($visitorProfileData->getLetterDocument() ) {
                        /** @var DocumentInterface $letterDocument */
                        $letterDocument =  $this->container->get(Document::class);     // pozn. potřebuju  každý document jiný -getFactoriesDefinitions
                        $this->documentRepo->add($letterDocument);                    
                        
                        $letterDocumentIdFromProfile = $visitorProfileData->getLetterDocument();                    
                        $letterDocumentFromProfile = $this->documentRepo->get($letterDocumentIdFromProfile);     

                        $letterDocument->setContent($letterDocumentFromProfile->getContent());
                        $letterDocument->setDocumentFilename($letterDocumentFromProfile->getDocumentFilename());
                        $letterDocument->setDocumentMimetype($letterDocumentFromProfile->getDocumentMimetype());                    

                        $visitorJobRequestData->setLetterDocument($letterDocument->getId());
                    }
                }             
                           
                // POST data
                $visitorJobRequestData->setPrefix((new RequestParams())->getParsedBodyParam($request, 'prefix'));
                $visitorJobRequestData->setName((new RequestParams())->getParsedBodyParam($request, 'name'));
                $visitorJobRequestData->setSurname((new RequestParams())->getParsedBodyParam($request, 'surname'));
                $visitorJobRequestData->setPostfix((new RequestParams())->getParsedBodyParam($request, 'postfix'));
                $visitorJobRequestData->setEmail((new RequestParams())->getParsedBodyParam($request, 'email'));
                $visitorJobRequestData->setPhone((new RequestParams())->getParsedBodyParam($request, 'phone'));
                $visitorJobRequestData->setCvEducationText((new RequestParams())->getParsedBodyParam($request, 'cv-education-text'));
                $visitorJobRequestData->setCvSkillsText((new RequestParams())->getParsedBodyParam($request, 'cv-skills-text'));

                $this->addFlashMessage("Pracovní údaje odeslány pro pozici" . $job->getNazev(). "." );
            }
            
            return $this->redirectSeeLastGet($request);
        }
    }

    
 
    
    /**
     * Naplnit novy document daty z uploadu souborů($files) a naplnit VisitorJobRequest                                                                                
     * 
     * Nic nehlídá - chyby při uploadu NEHLÁSÍ - pokud nevyvolají výjimku, výjimky nejsou ošetřeny!
     *
     * @param ServerRequestInterface $request
     * @return type
     */
    private function processingDocs($files, VisitorJobRequestInterface $visitorJobRequest) {

        //TODO: self::UPLOADED_KEY -rozlišit uploady z jednotlivých metod
        //".doc", ".docx", ".dot", ".odt", "pages", ".xls", ."xlsx", ".ods", ".txt", ".pdf"

        $statusSecurity = $this->statusSecurityRepo->get();
        $loginAggregateCredentials = $statusSecurity->getLoginAggregate();
        if (isset($loginAggregateCredentials)) {
            $userHash = $loginAggregateCredentials->getLoginNameHash();
            $uploadError = '';
           
            /** @var UploadedFileInterface $file */
            if(isset($files) AND $files) {
                                  
                foreach ($files as $uploadedFileName => $file) {                                       
                    // naplnit novy document daty z uploadu a naplnit VisitorJobRequest                                                                                
                    if ($file->getError()===UPLOAD_ERR_OK) {                                               
                        switch ($uploadedFileName) {
                            case self::UPLOADED_KEY_CV.$userHash:
                                 /** @var Document $documentCv */
                                $documentCv = $this->container->get(Document::class);    //   new Document();
                                $this->documentRepo->add($documentCv);
                                                               
                                $this->hydrateVisitorJobRequestDataByFile($file, self::UPLOADED_KEY_CV, $documentCv, $visitorJobRequest);
                                break;
                            
                            case self::UPLOADED_KEY_LETTER.$userHash:
                                /** @var Document $documentLetter */
                                $documentLetter = $this->container->get(Document::class);      //   new Document();
                                $this->documentRepo->add($documentLetter);
                                
                                $this->hydrateVisitorJobRequestDataByFile($file, self::UPLOADED_KEY_LETTER, $documentLetter, $visitorJobRequest);
                                break;
                            default:
                                $uploadError = "Neznámé jméno souboru, neodpovídá žádnému očekávanému jménu. Soubor {$file->getClientFilename()} neuložen.";
                                break;
                        }
                    } else {
                        $uploadError = $this->uploadErrorMessage($file->getError());
                    }
                }
            }

        }
        return $uploadError;
    }

    
    private function hydrateVisitorJobRequestDataByFile($fileForSave, $type, $document, VisitorJobRequestInterface $visitorJobRequest) {
    //        $time = str_replace(",", "-", $request->getServerParams()["REQUEST_TIME_FLOAT"]); // stovky mikrosekund
    //        $timestamp = (new \DateTime("now"))->getTimestamp();  // sekundy

        // file move to temp
        $clientFileName = $fileForSave->getClientFilename();
        $clientMime = $fileForSave->getClientMediaType();
        $size = $fileForSave->getSize();  // v bytech
        $ext = pathinfo($clientFileName,  PATHINFO_EXTENSION );
        $uploadedFileTemp = tempnam(sys_get_temp_dir(), hash('sha256', $clientFileName)).'.'.$ext;
        $fileForSave->moveTo($uploadedFileTemp);
             
        $document->setDocument(file_get_contents($uploadedFileTemp));
        $document->setDocumentMimetype($clientMime);
        $document->setDocumentFilename($clientFileName);       
        switch ($type) {
            case self::UPLOADED_KEY_CV:
                        $documentId = $document->getId();  
                        $visitorJobRequest->setCvDocument($document->getId());                                               
                break;                
            case self::UPLOADED_KEY_LETTER:                     
                        $documentId = $document->getId();  
                        $visitorJobRequest->setLetterDocument($document->getId());          
                break;
            default:
                break;
        }
        
        $flashMessage = "Uloženo $size bytů.";
        $this->addFlashMessage($flashMessage);
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
            return $response->withStatus(401);  // Unathorized
        } else {
            $loginName = $loginAggregateCredentials->getLoginName();  //prihlaseny          
            $role = $loginAggregateCredentials->getCredentials()->getRoleFk() ?? ''; 
            //$companyId = (new RequestParams())->getParsedBodyParam($request, "company-id");   //z POST  ---zjistit z jpbId
            
            if (isset($role) AND ($role==ConfigurationCache::auth()['roleRepresentative'])   ) {                
                /**  @var  Job $job  */
                $job = $this->jobRepo->get($jobId);
                if ( $this->representativeRepo->get($loginName, $job->getCompanyId()) )  {                                          
                    $isRepresentative = true; 
                }    
            }                
            
            if ($isRepresentative) {        
                /** @var VisitorJobRequestInterface  $visitorJobRequest */
                $visitorJobRequest = $this->visitorJobRequestRepo->get($visitorLoginName, $jobId);
              
                if (!isset($visitorJobRequest)) {
                    $this->addFlashMessage("Pracovní údaje pro pozici  nenalezeny v databázi.");
                } else {
                    $positionName = $visitorJobRequest->getPositionName();
                    $this->sendMail($positionName, $visitorJobRequest, $loginAggregateCredentials);
                    $this->addFlashMessage("Pracovní údaje pro pozici " .  $visitorJobRequest->getPositionName() . " neodeslány.");
                }
            } else {
                $this->addFlashMessage("Pracovní údaje smí mailem odesílat pouze vystavovatel.");
            }
        }
        return $this->redirectSeeLastGet($request);
    }

    
    
    private function sendMail($positionName, 
                              VisitorJobRequestInterface $visitorJobRequest,
                              LoginAggregateFullInterface $loginAggregateCredentials) {
        /** @var Mail $mail */
        $mail = $this->container->get(Mail::class);
        /** @var HtmlMessage $mailMessageFactory */
        $mailMessageFactory = $this->container->get(HtmlMessage::class);
        $subject =  'Veletrh práce - pracovní údaje návštěvníka ';
        $body = $mailMessageFactory->create(__DIR__."/Messages/pracovni-udaje-navstevnika.php",
                                            [
                                                'positionName' => $positionName,
                                                'visitorJobRequest' => $visitorJobRequest]);
        
        $attachments=[];        
        if ($visitorJobRequest->getCvDocument()) {
            $cvDocument = $this->documentRepo->get($visitorJobRequest->getCvDocument());
            if ($cvDocument) {
                $attachments[] = (new StringAttachment())
                            ->setStringAttachment($cvDocument->getContent())                                       
                            ->setAltText($cvDocument->getDocumentFilename());
            }
        }
        if ($visitorJobRequest->getLetterDocument()) {
            $letterDocument = $this->documentRepo->get($visitorJobRequest->getLetterDocument());
            if ($letterDocument) {
                $attachments[] = (new StringAttachment())
                            ->setStringAttachment($letterDocument->getContent())     
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
                                     ->setFrom('info@najdidi.cz', 'web veletrhprace')
                                     ->addTo($loginAggregateCredentials->getRegistration()->getEmail(), $presenterLogiName.' veletrhprace.online')
                                     ->addTo('svoboda@grafia.cz', $presenterLogiName.' veletrhprace.online')
                                );
        
        $mail->mail($params); // posle mail
//        $this->addFlashMessage("!!! Momentálně se maily neposílají !!!");
    }    
    
    protected function uploadErrorMessage($error) {
        switch ($error) {
            case UPLOAD_ERR_OK:
                $response = 'There is no error, the file uploaded with success.';
                break;
            case UPLOAD_ERR_INI_SIZE:
                $response = 'The uploaded file exceeds the upload_max_filesize directive in php.ini.';
                break;
            case UPLOAD_ERR_FORM_SIZE:
                $response = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.';
                break;
            case UPLOAD_ERR_PARTIAL:
                $response = 'The uploaded file was only partially uploaded.';
                break;
            case UPLOAD_ERR_NO_FILE:
                $response = 'No file was uploaded.';
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $response = 'Missing a temporary folder.';
                break;
            case UPLOAD_ERR_CANT_WRITE:
                $response = 'Failed to write file to disk.';
                break;
            case UPLOAD_ERR_EXTENSION:
                $response = 'File upload stopped by extension.';
                break;
            default:
                $response = 'Unknown upload error.';
                break;
        }
        return $response;
    }
}


