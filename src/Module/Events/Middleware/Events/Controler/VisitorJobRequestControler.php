<?php

namespace Events\Middleware\Events\Controler;

use Site\ConfigurationCache;
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
class VisitorJobRequestControler extends FrontControlerAbstract {

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
    public function addRequest(ServerRequestInterface $request, $jobId) {
        $loginAggregateCredentials = $this->statusSecurityRepo->get()->getLoginAggregate();
        if (!isset($loginAggregateCredentials)) {
            $response = (new ResponseFactory())->createResponse()->withStatus(401);  // Unathorized
        } else {
            $loginName = $loginAggregateCredentials->getLoginName();
            /** @var Job  $job */                     
            $job = $this->jobRepo->get($jobId);
            $jobRequest = $this->visitorJobRequestRepo->get($loginName, $jobId);             
                                               
            // ################################### 
            // neexistuje jobrequest pro tento job, a to vzdy, protoze
            // Pozn.: existuje-li jobrequest - tak tudy vubec nejdu.... zadost se da poslat jen 1x, pak tam neni uz tlacitko 
            // ######################
            //-- kdyz je document v profileData  - i tak vyrobit document novy+add do documentRepo a v jobrequestu updatovat id a
            //   do noveho documentu presunout data ze stavajiciho dokumentu , ktery je v profilu
            //-- kdyz neni dokument v profileData - vyrobit document novy+add do documentRepo, a do jobRequestu (id, tj. letter_document, cv_document) 
               
            if (!isset($jobRequest)) {   //plati vzdy                  
                $jobRequest = new VisitorJobRequest();
                $jobRequest->setLoginLoginName($loginName);
                $jobRequest->setCompanyId($jobId);
                $jobRequest->setPositionName( $job->getNazev() /* $positionName */ );
                $this->visitorJobRequestRepo->add($jobRequest);
                
//            $visitorProfileData = $this->visitorProfileRepo->get($loginName); // existuje vzdy
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
                           
                $this->addFlashMessage("Zájem o tuto pozici odeslán pro {$job->getNazev()}." );
            } else {
                $this->addFlashMessage("Zájem o tuto pozici byl již odeslán dříve.", FlashSeverityEnum::WARNING);  // error
            }            
            return $this->redirectSeeLastGet($request);
        }
    }

    public function updateRequest(ServerRequestInterface $request, $jobId, $loginloginname) {
        $statusSecurity = $this->statusSecurityRepo->get();
        $loginAggregateCredentials = $statusSecurity->getLoginAggregate();
        if (!isset($loginAggregateCredentials)) {
            $response = (new ResponseFactory())->createResponse()->withStatus(401);  // Unathorized
        } else {
            $loginName = $loginAggregateCredentials->getLoginName();         
            /** @var Job  $job */                     
            $job = $this->jobRepo->get($jobId);
            $jobRequest = $this->visitorJobRequestRepo->get($loginName, $jobId);             

//            $visitorProfileData = $this->visitorProfileRepo->get($loginName); // existuje vzdy
                                               
            // ################################### 
            // neexistuje jobrequest pro tento job, a to vzdy, protoze
            // Pozn.: existuje-li jobrequest - tak tudy vubec nejdu.... zadost se da poslat jen 1x, pak tam neni uz tlacitko 
            // ######################
            //-- kdyz je document v profileData  - i tak vyrobit document novy+add do documentRepo a v jobrequestu updatovat id a
            //   do noveho documentu presunout data ze stavajiciho dokumentu , ktery je v profilu
            //-- kdyz neni dokument v profileData - vyrobit document novy+add do documentRepo, a do jobRequestu (id, tj. letter_document, cv_document) 
               
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
     * @param ServerRequestInterface $request
     * @param type $visitorLoginName
     * @param type $jobId
     * @return type
     */
    public function sendJobRequest(ServerRequestInterface $request, $visitorLoginName, $jobId) {          
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
                if ( $this->representativeRepo->get($namePrihlasenehoRepresentanta, $job->getCompanyId()) )  {                                          
                    $isRepresentative = true;                                           
                    $mailPrihlasenehoRepresentanta = $loginAggregateFull->getRegistration()->getEmail();            
                }    
            }                         
                        
            if ($isRepresentative) {        
                /** @var VisitorJobRequestInterface  $visitorJobRequest */
                $visitorJobRequest = $this->visitorJobRequestRepo->get($visitorLoginName, $jobId);
              
                if (!isset($visitorJobRequest)) {
                    $this->addFlashMessage("Pracovní údaje návštěvníka " . $visitorLoginName . "  nenalezeny v databázi.");
                } else {                                                                                   
                    //$this->addFlashMessage("Budu mailem odesílat.");  
                    $this->sendMail(
                            $visitorJobRequest, 
                            $mailPrihlasenehoRepresentanta,
                            $namePrihlasenehoRepresentanta                    
                            );

                    $this->addFlashMessage("Pracovní údaje pro pozici " .  $visitorJobRequest->getPositionName() . " odeslány.");
                }                                                                                                                           
                
            } else {
                $this->addFlashMessage("Pracovní údaje smí mailem odesílat pouze vystavovatel.");
            }        
        
        }        
                                        
        return $this->redirectSeeLastGet($request);
    }
        
    
    private function sendMail(
                            VisitorJobRequestInterface $visitorJobRequest,
                            $mailPrihlasenehoRepresentanta,
                            $namePrihlasenehoRepresentanta                           
            ) {
        /** @var Mail $mail */
        $mail = $this->container->get(Mail::class);
        /** @var HtmlMessage $mailMessageFactory */
        $mailMessageFactory = $this->container->get(HtmlMessage::class);

        $data_logo_grafia="data:image/gif;base64,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" ;
        //base64_encode()        
        $subject =  'Veletrh práce a vzdělávání - zájemce o pozici ' . $visitorJobRequest->getPositionName() . ".";        
        $body = $mailMessageFactory->create(__DIR__."/Messages/pracovni-udaje-navstevnika.php",
                                            [ 
                                                'data_logo_grafia' => $data_logo_grafia,
                                                'visitorJobRequest' => $visitorJobRequest
                                            ]);
     
//        $attachments =  [ (new Attachment())
////                                    ->setFileName(ConfigurationCache::mail()['mail.attachments'].'logo_grafia.png')
////                                    ->setAltText('Logo Grafia')
//                            ->setFileName(ConfigurationCache::mail()['mail.attachments'].'nejakeP.pdf') //_DIR__."/Messages/nejakeP.pdf"
//                            ->setAltText('příloha')
//                        ];                                                                 
        $attachments=[];        
        
        

        $cvId = $visitorJobRequest->getCvDocument();
        if  (  isset ( $cvId ) ) {
            $cvDocument = $this->documentRepo->get($visitorJobRequest->getCvDocument());
            $cvContent = $cvDocument->getContent();
            if ( isset( $cvContent ) ) {                                                                
                $attachments[] = (new StringAttachment())
                            ->setStringAttachment($cvDocument->getContent())                                       
                            ->setAltText('Životopis - ' . $cvDocument->getDocumentFilename());
            }
        }
        $letterId = $visitorJobRequest->getLetterDocument();
        if  ( isset ( $letterId ) ) {
            $letterDocument = $this->documentRepo->get($visitorJobRequest->getLetterDocument());
            $documentContent = $letterDocument->getContent();
            if ( isset ( $documentContent ) ) {
                $attachments[] = (new StringAttachment())
                            ->setStringAttachment($letterDocument->getContent())     
                            ->setAltText('Motivační dopis - ' . $letterDocument->getDocumentFilename());
            } 
        }
        
        //--------------------------------        
        $params = (new Params())
                    ->setContent(  (new Content())
                                     ->setSubject($subject)
                                     ->setHtml($body)
                                     ->setAttachments($attachments)
                                )
                    ->setParty  (  (new Party())
                                     ->setFrom('info@najdisi.cz', 'web praci.najdisi.cz')
                                     ->addTo($mailPrihlasenehoRepresentanta, $namePrihlasenehoRepresentanta)
//                                     ->addBcc('webmaster@grafia.cz', 'vs')
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


