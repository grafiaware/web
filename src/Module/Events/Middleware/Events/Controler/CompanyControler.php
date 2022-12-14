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

//use Events\Model\Repository\VisitorProfileRepo;
//use Events\Model\Repository\VisitorProfileRepoInterface;
//
//use Events\Model\Repository\VisitorJobRequestRepo;
//use Events\Model\Repository\VisitorJobRequestRepoInterface;

use Events\Model\Repository\DocumentRepo;
use Events\Model\Repository\DocumentRepoInterface;

use Events\Model\Repository\RepresentativeRepoInterface;
use Events\Model\Repository\RepresentativeRepo;

use Events\Model\Repository\CompanyRepoInterface;
use Events\Model\Repository\CompanyContactRepoInterface;
use Events\Model\Entity\CompanyContactInterface;
use Events\Model\Entity\CompanyContact;

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
 * Description of NestedFilesUpload
 *
 * @author pes2704
 */
class CompanyControler extends FrontControlerAbstract {

    /**
     * 
     * @var CompanyContactRepoInterface
     */
    private $companyContactRepo;
    /**
     * 
     * @var CompanyRepoInterface
     */
    private $companyRepo;
    
    
    /**
     * 
     * @param StatusSecurityRepo $statusSecurityRepo
     * @param StatusFlashRepo $statusFlashRepo
     * @param StatusPresentationRepo $statusPresentationRepo
     * @param CompanyRepoInterface $companyRepo
     * @param CompanyContactRepoInterface $companyContactRepo     
     */
    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusFlashRepo $statusFlashRepo,
            StatusPresentationRepo $statusPresentationRepo,
            
            CompanyRepoInterface $companyRepo,
            CompanyContactRepoInterface $companyContactRepo
            
            ) {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo);
        $this->companyRepo = $companyRepo;
        $this->companyContactRepo = $companyContactRepo;
    }
    
    
    /**
     * POST company contact
     *
     * @param ServerRequestInterface $request
     * @return type
     */
    public function insertCompanyContact (ServerRequestInterface $request) {        
//        $this->addFlashMessage("Insert neinseruje.");
//        return $this->redirectSeeLastGet($request);      
        
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
                            AND  $this->representativeRepo->get($loginName) )  {
                $isRepresentative = true; 
            }
                        
            if ($isRepresentative) {
                // POST data
                
                $name = (new RequestParams())->getParsedBodyParam($request, 'name');               
                $phones = (new RequestParams())->getParsedBodyParam($request, 'phones');
                $mobiles = (new RequestParams())->getParsedBodyParam($request, "mobiles");
                $emails = (new RequestParams())->getParsedBodyParam($request, "emails");
                /** @var CompanyContact $companyContact */
                $companyContact = $this->container->get(CompanyContact::class); //new $companyContact
                $companyContact->setName( (new RequestParams())->getParsedBodyParam($request, 'name') );
                $companyContact->setPhones((new RequestParams())->getParsedBodyParam($request, 'phones'));
                $companyContact->setMobiles((new RequestParams())->getParsedBodyParam($request, "mobiles"));
                $companyContact->setEmails((new RequestParams())->getParsedBodyParam($request, "emails"));
                
                $this->companyContactRepo->add($companyContact);
                
            } else {
                $this->addFlashMessage("Údaje o kontaktech vyvstavovatele smí editovat pouze representant vystavovatele.");
            }
            
        }
        return $this->redirectSeeLastGet($request);
    }
    
    /**
     * POST company contact
     *
     * @param ServerRequestInterface $request
     * @return type
     */
    public function updateCompanyContact (ServerRequestInterface $request) {                   
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
                            AND  $this->representativeRepo->get($loginName) )  {
                $isRepresentative = true; 
            }
                        
            if ($isRepresentative) {
                // POST data
                $сompanyId = (new RequestParams())->getParsedBodyParam($request, 'company-id');
                /** @var CompanyContact $companyContact */
                $CompanyContact = $this->companyRepo->get( (new RequestParams())->getParsedBodyParam($request, 'company-id') );                              

                $companyContact->setName( (new RequestParams())->getParsedBodyParam($request, 'name') );
                $companyContact->setPhones((new RequestParams())->getParsedBodyParam($request, 'phones'));
                $companyContact->setMobiles((new RequestParams())->getParsedBodyParam($request, "mobiles"));
                $companyContact->setEmails((new RequestParams())->getParsedBodyParam($request, "emails"));
                
                $this->companyContactRepo->add($companyContact);
                
            } else {
                $this->addFlashMessage("Údaje o kontaktech vystavovatele smí editovat pouze representant vystavovatele.");
            }
            
        }
        return $this->redirectSeeLastGet($request);
    }
    
    
     /**
     * POST company contact
     *
     * @param ServerRequestInterface $request
     * @return type
     */
    public function removeCompanyContact (ServerRequestInterface $request) {                   
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
                            AND  $this->representativeRepo->get($loginName) )  {
                $isRepresentative = true; 
            }
                        
            if ($isRepresentative) {
                
                
            } else {
                $this->addFlashMessage("Údaje o kontaktech vystavovatele smí mazat pouze representant vystavovatele.");
            }
            
        }
        return $this->redirectSeeLastGet($request);
    }
            
    
  //------33333333333333333333333333333333333$$$$$$$$$$#####################################
  //#############################################################################
// dale na vzmayani
    
    
    /**
     * POST visitor data
     *
     * @param ServerRequestInterface $request
     * @return type
     */
    public function visitor(ServerRequestInterface $request) {
        $statusSecurity = $this->statusSecurityRepo->get();
        $loginAggregateCredentials = $statusSecurity->getLoginAggregate();

        if (!isset($loginAggregateCredentials)) {
            $response = (new ResponseFactory())->createResponse();
            $response = $response->withStatus(401);  // Unathorized
        } else {
            $loginName = $loginAggregateCredentials->getLoginName();

            $visitorProfile = $this->visitorProfileRepo->get($loginName);
            if (!isset($visitorProfile)) {
                $visitorProfile = new VisitorProfile();
                $visitorProfile->setLoginLoginName($loginName);
                $this->visitorProfileRepo->add($visitorProfile);
            }
    
            // POST data
            $visitorProfile->setPrefix((new RequestParams())->getParsedBodyParam($request, 'prefix'));
            $visitorProfile->setName((new RequestParams())->getParsedBodyParam($request, 'name'));
            $visitorProfile->setSurname((new RequestParams())->getParsedBodyParam($request, 'surname'));
            $visitorProfile->setPostfix((new RequestParams())->getParsedBodyParam($request, 'postfix'));
            $visitorProfile->setEmail((new RequestParams())->getParsedBodyParam($request, 'email'));
            $visitorProfile->setPhone((new RequestParams())->getParsedBodyParam($request, 'phone'));
            $visitorProfile->setCvEducationText((new RequestParams())->getParsedBodyParam($request, 'cv-education-text'));
            $visitorProfile->setCvSkillsText((new RequestParams())->getParsedBodyParam($request, 'cv-skills-text'));

//            $this->addFlashMessage(" Data uložena");
            return $this->redirectSeeLastGet($request);
        }
    }

    
    /**
     * Representative odesílá mailem sobě.
     * 
     * @param ServerRequestInterface $request
     * @return type
     */
    public function sendJobRequest(ServerRequestInterface $request) {
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
            if(isset($role) AND ($role==ConfigurationCache::loginLogoutController()['roleRepresentative']) AND  $this->representativeRepo->get($loginName) )  {
                $isRepresentative = true; 
            }                
            if ($isRepresentative) {
                // POST data
                $shortName = (new RequestParams())->getParsedBodyParam($request, 'short-name');
                $positionName = (new RequestParams())->getParsedBodyParam($request, 'position-name');
                $visitorLoginName = (new RequestParams())->getParsedBodyParam($request, "visitor-login-name");
                $jobId = (new RequestParams())->getParsedBodyParam($request, "job-id");

                $visitorDataJRqPost = $this->visitorJobRequestRepo->get($visitorLoginName, $jobId);
                if (!isset($visitorDataJRqPost)) {
                    $this->addFlashMessage("Pracovní údaje pro pozici $positionName nenalezeny v databázi.");
                } else {
              /**/  $this->sendMail($positionName, $visitorDataJRqPost, $loginAggregateCredentials);
                    $this->addFlashMessage("Pracovní údaje pro pozici $positionName neodeslány.**Momentálně se maily neposílají.**");
                }
            } else {
                $this->addFlashMessage("Pracovní údaje smí mailem odesílat pouze vystavovatel.");
            }
        }
        return $this->redirectSeeLastGet($request);
    }

    private function sendMail($positionName, 
                              VisitorJobRequestInterface $visitorDataPost,
                              LoginAggregateFullInterface /*LoginAggregateFullInterface*/ $loginAggregateCredentials) {
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

    /**
     * POST visitor data posted for presenter
     *
     * @param ServerRequestInterface $request
     * @return type
     */
    public function jobRequest_stare(ServerRequestInterface $request) {
        $statusSecurity = $this->statusSecurityRepo->get();
        $loginAggregateCredentials = $statusSecurity->getLoginAggregate();

        if (!isset($loginAggregateCredentials)) {
            $response = (new ResponseFactory())->createResponse();
            $response = $response->withStatus(401);  // Unaathorized
        } else {
            $loginName = $loginAggregateCredentials->getLoginName();

            // POST data
            $shortName = (new RequestParams())->getParsedBodyParam($request, 'short-name');
            $positionName = (new RequestParams())->getParsedBodyParam($request, 'position-name');
            $jobId = (new RequestParams())->getParsedBodyParam($request, 'job-id');

            $visitorProfileData = $this->visitorProfileRepo->get($loginName);
            $visitorJobRequestData = $this->visitorJobRequestRepo->get($loginName, $jobId /*$shortName, $positionName*/ );

            if (!isset($visitorJobRequestData)) {
                $visitorJobRequestData = new VisitorJobRequest();
                $visitorJobRequestData->setLoginLoginName($loginName);
                $visitorJobRequestData->setJobId($jobId);
                $visitorJobRequestData->setPositionName($positionName);
                $this->visitorJobRequestRepo->add($visitorJobRequestData);
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

            $this->uploadDocs_stare($request, $visitorJobRequestData);

            if (!$visitorJobRequestData->getCvDocument()) {                                                 
                $visitorJobRequestData->setCvDocument($visitorProfileData->getCvDocument());               
            }
            if (!$visitorJobRequestData->getLetterDocument()) {
                $visitorJobRequestData->setLetterDocument($visitorProfileData->getLetterDocument());
            }

            $this->addFlashMessage("Pracovní údaje odeslány pro pozici $positionName.");
            return $this->redirectSeeLastGet($request);
        }
    }

    
    
    
    /**
     * Visitor 'odesílá' data pro zaměstnavatele - vytváří se visitorJobRequest
     * 
     * @param ServerRequestInterface $request
     * @return type
     */
    public function jobRequest(ServerRequestInterface $request) {
        $statusSecurity = $this->statusSecurityRepo->get();
        $loginAggregateCredentials = $statusSecurity->getLoginAggregate();
        if (!isset($loginAggregateCredentials)) {
            $response = (new ResponseFactory())->createResponse();
            $response = $response->withStatus(401);  // Unaathorized
        } else {
            $loginName = $loginAggregateCredentials->getLoginName();

            // POST data prisly
            $shortName = (new RequestParams())->getParsedBodyParam($request, 'short-name');
            $positionName = (new RequestParams())->getParsedBodyParam($request, 'position-name');
            $jobId = (new RequestParams())->getParsedBodyParam($request, 'job-id');

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
                $visitorJobRequestData->setPositionName($positionName);
                $this->visitorJobRequestRepo->add($visitorJobRequestData);
                
                // z post dat
                $files = $request->getUploadedFiles();  
                /* @var $file UploadedFileInterface */
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

                        $cvDocument->setDocument($cvDocumentFromProfile->getDocument());
                        $cvDocument->setDocumentFilename($cvDocumentFromProfile->getDocumentFilename());
                        $cvDocument->setDocumentMimetype($cvDocumentFromProfile->getDocumentMimetype());
                        $this->documentRepo->add($cvDocument);

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

                        $letterDocument->setDocument($letterDocumentFromProfile->getDocument());
                        $letterDocument->setDocumentFilename($letterDocumentFromProfile->getDocumentFilename());
                        $letterDocument->setDocumentMimetype($letterDocumentFromProfile->getDocumentMimetype());                    
                        $this->documentRepo->add($letterDocument);

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

                $this->addFlashMessage("Pracovní údaje odeslány pro pozici $positionName.");
            }
            
            return $this->redirectSeeLastGet($request);
        }
    }

    
    
     /**
     * Nic nehlídá - chyby při uploadu NEHLÁSÍ - pokud nevyvolají výjimku, výjimky nejsou ošetřeny!
     *
     *
     * @param ServerRequestInterface $request
     * @return type
     */
    private function uploadDocs_stare(ServerRequestInterface $request, VisitorJobRequestInterface $visitorJobRequest) {

        //TODO: self::UPLOADED_KEY -rozlišit uploady z jednotlivých metod
        //".doc", ".docx", ".dot", ".odt", "pages", ".xls", ."xlsx", ".ods", ".txt", ".pdf"

        $statusSecurity = $this->statusSecurityRepo->get();
        $loginAggregateCredentials = $statusSecurity->getLoginAggregate();
        if (isset($loginAggregateCredentials)) {
            $userHash = $loginAggregateCredentials->getLoginNameHash();
            $uploadError = '';

            //files z requestu
            $files = $request->getUploadedFiles();  
            /* @var $file UploadedFileInterface */
            if(isset($files) AND $files) {
                // !po jednom souboru!
                foreach ($files as $uploadedFileName => $file) {
                                       
                    // naplnit novy document daty z uploadu a naplnit VisitorJobRequest                                                                                
                    if ($file->getError()=== UPLOAD_ERR_OK) {                                               
                        switch ($uploadedFileName) {
                            case self::UPLOADED_KEY_CV.$userHash:
                                 /* @var DocumentInterface $documentCv */
                                $documentCv = $this->container->get(Document::class); //   new Document();
                                $this->documentRepo->add($documentCv);
                                //$documentCvId = $documentCv->getId();  
                                                                
                                $this->hydrateVisitorJobRequestDataByFile($file, self::UPLOADED_KEY_CV, $documentCv, $visitorJobRequest);
                                break;
                            
                            case self::UPLOADED_KEY_LETTER.$userHash:
                                /* @var  DocumentInterface  $documentLetter */
                                $documentLetter = $this->container->get(Document::class); //   new Document();
                                $this->documentRepo->add($documentLetter);
                                $documentLetterId = $documentLetter->getId;  
                                
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
    
    
    
    /**
     * Naplnit novy document daty z uploadu souborů($files) a naplnit VisitorJobRequest                                                                                
     * 
     * Nic nehlídá - chyby při uploadu NEHLÁSÍ - pokud nevyvolají výjimku, výjimky nejsou ošetřeny!
     *
     * @param ServerRequestInterface $request
     * @return type
     */
    private function processingDocs( /*ServerRequestInterface $request,*/ $files, VisitorJobRequestInterface $visitorJobRequest) {

        //TODO: self::UPLOADED_KEY -rozlišit uploady z jednotlivých metod
        //".doc", ".docx", ".dot", ".odt", "pages", ".xls", ."xlsx", ".ods", ".txt", ".pdf"

        $statusSecurity = $this->statusSecurityRepo->get();
        $loginAggregateCredentials = $statusSecurity->getLoginAggregate();
        if (isset($loginAggregateCredentials)) {
            $userHash = $loginAggregateCredentials->getLoginNameHash();
            $uploadError = '';

            /* @var $file UploadedFileInterface */
            if(isset($files) AND $files) {
                                  
                foreach ($files as $uploadedFileName => $file) {                                       
                    // naplnit novy document daty z uploadu a naplnit VisitorJobRequest                                                                                
                    if ($file->getError()===UPLOAD_ERR_OK) {                                               
                        switch ($uploadedFileName) {
                            case self::UPLOADED_KEY_CV.$userHash:
                                 /* @var $documentCv  Document */
                                $documentCv = $this->container->get(Document::class);    //   new Document();
                                $this->documentRepo->add($documentCv);
                                                               
                                $this->hydrateVisitorJobRequestDataByFile($file, self::UPLOADED_KEY_CV, $documentCv, $visitorJobRequest);
                                break;
                            
                            case self::UPLOADED_KEY_LETTER.$userHash:
                                /* @var $documentLetter  Document */
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
     *
     * @param ServerRequestInterface $request
     */
    public function uploadVisitorFile(ServerRequestInterface $request) {

        //TODO: self::UPLOADED_KEY -rozlišit uploady z jednotlivých metod

//".doc", ".docx", ".dot", ".odt", "pages", ".xls", ."xlsx", ".ods", ".txt", ".pdf"

        $statusSecurity = $this->statusSecurityRepo->get();
        $loginAggregateCredentials = $statusSecurity->getLoginAggregate();

        if (!isset($loginAggregateCredentials)) {
            $response = (new ResponseFactory())->createResponse();
            $response = $response->withStatus(401);  // Unauthorized
        } else {
            $userHash = $loginAggregateCredentials->getLoginNameHash();

            // z konfigurace
            $files = $request->getUploadedFiles();

            // POST - jeden soubor
            /* @var $file UploadedFileInterface */
            if(isset($files) AND $files) {
                if (array_key_exists(self::UPLOADED_KEY_CV.$userHash, $files)) {
                    $fileForSave = $files[self::UPLOADED_KEY_CV.$userHash];
                    $type = self::UPLOADED_KEY_CV;
                } elseif (array_key_exists(self::UPLOADED_KEY_LETTER.$userHash, $files)) {
                    $fileForSave = $files[self::UPLOADED_KEY_LETTER.$userHash];
                    $type = self::UPLOADED_KEY_LETTER;
                }
                $response = $this->createResponeIfError($request, $fileForSave);
            } else {
                $this->addFlashMessage("neodeslán žádný soubor. Soubor neuložen.", FlashSeverityEnum::WARNING);
                $response = $this->redirectSeeLastGet($request);
            }
        }

        ###### SAVE
        if (!isset($response) AND isset($type)) {
    //        $time = str_replace(",", "-", $request->getServerParams()["REQUEST_TIME_FLOAT"]); // stovky mikrosekund
    //        $timestamp = (new \DateTime("now"))->getTimestamp();  // sekundy

            // file move to temp
            $clientFileName = $fileForSave->getClientFilename();
            $clientMime = $fileForSave->getClientMediaType();
            $size = $fileForSave->getSize();  // v bytech
            $ext = pathinfo($clientFileName,  PATHINFO_EXTENSION );
            $uploadedFileTemp = tempnam(sys_get_temp_dir(), hash('sha256', $clientFileName)).'.'.$ext;
            $fileForSave->moveTo($uploadedFileTemp);

            // if login - save data
            if (isset($loginAggregateCredentials)) {
                $loginName = $loginAggregateCredentials->getLoginName();

                $visitorProfileData = $this->visitorProfileRepo->get($loginName);
                if (!isset($visitorProfileData)) {
                    $visitorProfileData = new VisitorData();
                    $visitorProfileData->setLoginName($loginName);
                    $this->visitorProfileRepo->add($visitorProfileData);
                }
                                      
                $documentCvId = $visitorProfileData->getCvDocument();
                $documentLettterId = $visitorProfileData->getLetterDocument();            
                switch ($type) {
                    case self::UPLOADED_KEY_CV:
                        if (!isset($documentCvId)) {
                            $documentCv = new Document();
                            $this->documentRepo->add($documentCv);
                           
                            $visitorProfileData->setCvDocument($documentCv->getId());
                        }
                        else {        
                             $documentCv = $this->documentRepo->get($documentCvId);  
                        }                 
                        $documentCv->setDocument(file_get_contents($uploadedFileTemp));
                        $documentCv->setDocumentMimetype($clientMime);
                        $documentCv->setDocumentFilename($clientFileName);                        
                        
                        $this->addFlashMessage("Uložen váš životopis.", FlashSeverityEnum::SUCCESS);
                        break;
                        
                    case self::UPLOADED_KEY_LETTER:
                        if (!isset($documentLettterId)) {
                            $documentLetter = new Document();
                            $this->documentRepo->add($documentLetter);
                            
                            $visitorProfileData->setLetterDocument($documentLetter->getId());
                        }
                         else {   
                            $documentLetter = $this->documentRepo->get($documentLettterId);     
                        }     
                        $documentLetter->setDocument(file_get_contents($uploadedFileTemp));
                        $documentLetter->setDocumentMimetype($clientMime);
                        $documentLetter->setDocumentFilename($clientFileName);
                        
                        $this->addFlashMessage("Uložen váš motivační dopis.", FlashSeverityEnum::SUCCESS);
                        break;
                    default:
                        break;
                }
                $flashMessage = "Uloženo $size bytů.";
                $this->addFlashMessage($flashMessage);

                $response = $this->redirectSeeLastGet($request);
            } else {
                $this->addFlashMessage("Chyba oprávnění.", FlashSeverityEnum::WARNING);
                $this->addFlashMessage("Soubor neuložen!", FlashSeverityEnum::WARNING);
                $this->redirectSeeLastGet($request);
            }
        } else {
            if (isset($clientFileName)) {
                $this->addFlashMessage($clientFileName);
            }
            $this->addFlashMessage("Soubor neuložen!", FlashSeverityEnum::WARNING);
            return $response;
        }

        return $response;

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

    
    
    // nevim kde se pouziva ????? VS
    private function saveUploadedFile($param) {
        $cvFilepathName = __DIR__."/".$cvFilename;
        $finfo = finfo_open(FILEINFO_MIME_TYPE); // return mime type ala mimetype extension
        $mime = finfo_file($finfo, $cvFilepathName);
        $content = file_get_contents($cvFilepathName);
        $visitorData->setCvDocument($content);
        $visitorData->setCvDocumentMimetype($mime);
        $visitorData->setCvDocumentFilename($cvFilepathName);

        $letterFilepathName = __DIR__."/".$letterFilename;
        $finfo = finfo_open(FILEINFO_MIME_TYPE); // return mime type ala mimetype extension
        $mime = finfo_file($finfo, $letterFilepathName);
        $content = file_get_contents($letterFilepathName);
        $visitorData->setLetterDocument($content);
        $visitorData->setLetterDocumentMimetype($mime);
        $visitorData->setLetterDocumentFilename($letterFilepathName);

        $this->visitorProfileRepo->add($visitorData);
    }

    private function createResponeIfError($request, UploadedFileInterface $uploadfile) {
        $clientFileName = $uploadfile->getClientFilename();
        $error = $uploadfile->getError();
        $size = $uploadfile->getSize();  // v bytech

        $this->addFlashMessage($clientFileName);

        // Sanitize input // Remove anything which isn't a word, whitespace, number or any of the following caracters -_~,;[]().
//        $fileNameError = preg_match("/([^\w\s\d\-_~,;:\[\]\(\).])|([\.]{2,})/", $clientFileName);
//        if ($fileNameError) {
//            $response = (new ResponseFactory())->createResponse();
//            $response = $response->withStatus(400, "Bad Request. Invalid file name.");
//            $this->addFlashMessage("Chybné jméno souboru.", FlashSeverityEnum::WARNING);
////                header("HTTP/1.1 400 Invalid file name.");
//        } else
            
            if ($clientFileName==="") {
            $response = (new ResponseFactory())->createResponse();
            $response = $response->withStatus(400, "Bad Request. No file name.");
            $this->addFlashMessage("Prázdné jméno souboru.", FlashSeverityEnum::WARNING);
        } elseif (array_search(pathinfo($clientFileName,  PATHINFO_EXTENSION ), ConfigurationCache::filesUploadController()['upload.events.acceptedextensions'])) {
            $response = (new ResponseFactory())->createResponse();
            $response = $response->withStatus(400, "Bad Request. Invalid file extesion.");
            $this->addFlashMessage("Chybná přípona souboru.", FlashSeverityEnum::WARNING);
        } elseif ($error !== UPLOAD_ERR_OK) {
            $errMessage = $this->uploadErrorMessage($error);
            $response = (new ResponseFactory())->createResponse();
            $response = $response->withStatus(400, "Server or transfer error. $errMessage");
            $this->addFlashMessage("Nepodařilo se nahrát soubor. Chyba: $errMessage", FlashSeverityEnum::WARNING);
        } elseif ($size>10000000) {
            $response = (new ResponseFactory())->createResponse();
            $response = $response->withStatus(400, "Bad Request. File size is over limit.");
            $this->addFlashMessage("Soubor je příliš velký. Maximum je 10MB.", FlashSeverityEnum::WARNING);
        } elseif ($size<10) {
            $response = (new ResponseFactory())->createResponse();
            $response = $response->withStatus(400, "Bad Request. File size is under limit.", FlashSeverityEnum::WARNING);
            $this->addFlashMessage("Soubor je prázdný.", FlashSeverityEnum::WARNING);
        }
        return isset($response) ? $this->redirectSeeLastGet($request) : null;  // SV vžd ypřeklopím chybový esponse na přesměrování
//        return $response ?? null;

    }
}

