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
use Events\Model\Entity\VisitorProfileInterface;
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

use Mail\MessageFactory\HtmlMessage;
use Pes\Mail\Assembly;
use Pes\Mail\Assembly\Content;
use Pes\Mail\Assembly\Attachment; 
use Pes\Mail\Assembly\StringAttachment;
use Pes\Mail\Assembly\Party;

use Access\Enum\RoleEnum;
use Events\Service\JobRequest\DocumentUpload;


/**
 * Description of NestedFilesUpload
 *
 * @author pes2704
 */
class VisitorProfileControler extends FrontControlerAbstract {

    const UPLOADED_KEY_CV = "visitor-cv";
    const UPLOADED_KEY_LETTER = "visitor-letter";
    const UPLOADED_KEY = "visitor-document";
    
    const TYPE_CV = "cv";
    const TYPE_LETTER = "letter";
    
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
            JobRepo $job,
            private DocumentUpload $documentUpload
            ) {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo);
        $this->visitorProfileRepo = $visitorProfileRepo;
        $this->visitorJobRequestRepo = $visitorJobRequesRepo;        
        $this->documentRepo = $documentRepo;
        $this->representativeRepo = $representativeRepo;
        $this->jobRepo =  $job;
    }

    /**
     * POST visitor data
     *
     * @param ServerRequestInterface $request
     * @return type
     */
    public function addVisitorProfile(ServerRequestInterface $request) {
        $loginAggregateCredentials = $this->statusSecurityRepo->get()->getLoginAggregate();
        if (!isset($loginAggregateCredentials)) {
            $response = (new ResponseFactory())->createResponse()->withStatus(401);  // Unathorized
        } else {
            $loginName = $loginAggregateCredentials->getLoginName();
            $visitorProfile = $this->visitorProfileRepo->get($loginName);
            if (!isset($visitorProfile)) {  // ?? odmítnutí, pokud profil existuje
                $visitorProfile = new VisitorProfile();
                $visitorProfile->setLoginLoginName($loginName);
                $this->hydrateVisitorProfile($request, $visitorProfile);
                $this->visitorProfileRepo->add($visitorProfile);
                $this->addFlashMessage("Profil uložen");
            } else {
                $this->addFlashMessage("Profil již existuje.", FlashSeverityEnum::WARNING);
            }
            $response =  $this->redirectSeeLastGet($request);
        }
        return $response;
    }
    
    public function updateVisitorProfile(ServerRequestInterface $request, $loginName) {
        $loginAggregateCredentials = $this->statusSecurityRepo->get()->getLoginAggregate();
        if (!isset($loginAggregateCredentials)) {
            $response = (new ResponseFactory())->createResponse()->withStatus(401);  // Unathorized
        } else {
            $statusLoginName = $loginAggregateCredentials->getLoginName();
            if ($loginName !== $statusLoginName) {
                $response = (new ResponseFactory())->createResponse()->withStatus(401);  // Unathorized                
            } else {
//TODO: přidej kontrolu podle login name hash
//            $postLoginNameHash = (new RequestParams())->getParsedBodyParam($request, 'loginnamehash');
//            $loginameHash = ... vis status
                $visitorProfile = $this->visitorProfileRepo->get($loginName);
                if (!isset($visitorProfile)) {
                    $this->addFlashMessage("Profil neexistuje", FlashSeverityEnum::WARNING);
                } else {
                    $this->hydrateVisitorProfile($request, $visitorProfile);
                    $this->addFlashMessage("Profil uložen");
                }
                $response =  $this->redirectSeeLastGet($request);
            }
        }
        return $response;
    }
    private function hydrateVisitorProfile(ServerRequestInterface $request, VisitorProfileInterface $visitorProfile) {
        $requestParams = new RequestParams();
        $visitorProfile->setPrefix($requestParams->getParsedBodyParam($request, 'prefix'));
        $visitorProfile->setName($requestParams->getParsedBodyParam($request, 'name'));
        $visitorProfile->setSurname($requestParams->getParsedBodyParam($request, 'surname'));
        $visitorProfile->setPostfix($requestParams->getParsedBodyParam($request, 'postfix'));
        $visitorProfile->setPhone($requestParams->getParsedBodyParam($request, 'phone'));
        $visitorProfile->setCvEducationText($requestParams->getParsedBodyParam($request, 'cv-education-text'));
        $visitorProfile->setCvSkillsText($requestParams->getParsedBodyParam($request, 'cv-skills-text'));        
    }
 
    
             
    /**
     * 
     * @param ServerRequestInterface $request
     * @param type $parentId
     * @param type $type
     * @return type
     */
    public function addDocument (ServerRequestInterface $request, $parentId, $type) {                 
//      muze ???
//      -----------------------------------------------------------------------                
        // POST data        
        
                /** @var DocumentInterface $document */
                $document = $this->documentRepo->get($id);
                if (isset($document)) {
                    $this->addFlashMessage("Mám document v tabulce document  id $id! ");
                } else {
                    $this->addFlashMessage("Nemám document v  tabulce document  id $id! Uáááá :-(");
                }
        
        $visitorProfile = $this->visitorProfileRepo->get($parentId);
                
                
        $statusSecurity = $this->statusSecurityRepo->get();
        $loginAggregateCredentials = $statusSecurity->getLoginAggregate();

        if (!isset($loginAggregateCredentials)) {
            $response = (new ResponseFactory())->createResponse()->withStatus(401);  // Unauthorized
        } else {
            $userHash = $loginAggregateCredentials->getLoginNameHash();

         
            $files = $request->getUploadedFiles();

            
            /** @var  UploadedFileInterface $file */
            if(isset($files) AND $files) {
                if (array_key_exists(self::UPLOADED_KEY.$userHash, $files)) {
                    $fileForSave = $files[self::UPLOADED_KEY.$userHash];
                    $typeSelf = self::UPLOADED_KEY;
//                } elseif (array_key_exists(self::UPLOADED_KEY_LETTER.$userHash, $files)) {
//                    $fileForSave = $files[self::UPLOADED_KEY_LETTER.$userHash];
//                    $type = self::UPLOADED_KEY_LETTER;
                } else {
                    //  tady nevim co delat
                }
                $response = $this->createResponeIfError($request, $fileForSave);
            } else {
                $this->addFlashMessage("neodeslán žádný soubor. Soubor neuložen.", FlashSeverityEnum::WARNING);
                $response = $this->redirectSeeLastGet($request);                                 
            }
        }
             
                ###### SAVE
        if (!isset($response) AND isset($typeSelf)) {
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
            if ( isset($loginAggregateCredentials) )  {
               /* df */ // $loginName = $loginAggregateCredentials->getLoginName();
               /* df */ //  $visitorProfileData = $this->visitorProfileRepo->get($loginName);
                
               /**  @var VisitorProfileInterface  $visitorProfileData */
                $visitorProfileData = $this->visitorProfileRepo->get($parentId);
                if (!isset($visitorProfileData)) {   // u UPDATE vzdy existuje
                    $visitorProfileData = new VisitorProfile();
                    $visitorProfileData->setLoginLoginName($parentId);
                    $this->visitorProfileRepo->add($visitorProfileData);
                }
                
                 /** @var DocumentInterface $documentCv */           
                 /** @var DocumentInterface $documentLettter */     
                $documentCvId = $visitorProfileData->getCvDocument();
                $documentLettterId = $visitorProfileData->getLetterDocument();   
                
                switch ($type) {
                    case 'cv' :  //self::UPLOADED_KEY_CV:
                        if (!isset($documentCvId)) {
                            $documentCv = new Document();
                            $this->documentRepo->add($documentCv);
                           
                            $visitorProfileData->setCvDocument($documentCv->getId());
                        }
                        else {        
                             $documentCv = $this->documentRepo->get($documentCvId);  
                        }                 
                        $documentCv->getContent(file_get_contents($uploadedFileTemp));
                        $documentCv->getDocumentMimetype($clientMime);
                        $documentCv->setDocumentFilename($clientFileName);                        
                        
                        $this->addFlashMessage("Uložen váš životopis.", FlashSeverityEnum::SUCCESS);
                        break;
                        
                    case 'letter' ://self::UPLOADED_KEY_LETTER:
                        if (!isset($documentLettterId)) {
                            $documentLetter = new Document();
                            $this->documentRepo->add($documentLetter);
                            
                            $visitorProfileData->setLetterDocument($documentLetter->getId());
                        }
                         else {   
                            $documentLetter = $this->documentRepo->get($documentLettterId);     
                        }     
                        $documentLetter->setContent(file_get_contents($uploadedFileTemp));
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
       
                    
        //return $this->redirectSeeLastGet($request);
    }

    
    
    
    
   /**
    * 
    * @param VisitorProfileInterface $visitorProfile
    * @param type $type
    * @return type
    */
    private function getRightDocument( VisitorProfileInterface $visitorProfile, $type ): ?DocumentInterface {
        switch ($type) {
            case self::TYPE_CV:
                $idDocument = $visitorProfile->getCvDocument();
                break;
            case self::TYPE_LETTER:
                $idDocument = $visitorProfile->getLetterDocument();
                break;
            default:
                $idDocument = null;
                break;
        }
        return isset($idDocument) ? $this->documentRepo->get($idDocument) : null;
    }

    /**
     * #175: dokumenty profilu smí měnit jen návštěvník – vlastník profilu, nebo administrátor.
     */
    private function canEditProfileDocuments($parentId): bool {
        $loginAggregate = $this->statusSecurityRepo->get()->getLoginAggregate();
        if (!isset($loginAggregate)) {
            return false;
        }
        $role = $loginAggregate->getCredentials()?->getRoleFk();
        return ($role === RoleEnum::VISITOR && $loginAggregate->getLoginName() === $parentId)
                || $role === RoleEnum::EVENTS_ADMINISTRATOR;
    }

        
    /**
     * 
     * @param ServerRequestInterface $request
     * @param type $parentId
     * @param type $type
     * @param type $id
     * @return type
     */
    public function addupdateDocument (ServerRequestInterface $request, $parentId, $type ) {
        $loginAggregateCredentials = $this->statusSecurityRepo->get()->getLoginAggregate();
        if (!isset($loginAggregateCredentials)) {
            return (new ResponseFactory())->createResponse()->withStatus(401);  // Unauthorized
        }
        if (!$this->canEditProfileDocuments($parentId) || !in_array($type, [self::TYPE_CV, self::TYPE_LETTER], true)) {
            $this->addFlashMessage("Chyba oprávnění. Soubor neuložen.", FlashSeverityEnum::WARNING);
            return $this->redirectSeeLastGet($request);
        }
        /** @var VisitorProfileInterface $visitorProfile */
        $visitorProfile = $this->visitorProfileRepo->get($parentId);
        if (!isset($visitorProfile)) {
            $this->addFlashMessage("Nejdříve uložte údaje profilu. Soubor neuložen.", FlashSeverityEnum::WARNING);
            return $this->redirectSeeLastGet($request);
        }
        $files = $request->getUploadedFiles() ?? [];
        $fileForSave = $files[self::UPLOADED_KEY.$loginAggregateCredentials->getLoginNameHash()] ?? null;
        if (!$this->documentUpload->isSent($fileForSave)) {
            $this->addFlashMessage("Nebyl vybrán žádný soubor. Soubor neuložen.", FlashSeverityEnum::WARNING);
            return $this->redirectSeeLastGet($request);
        }
        $error = $this->documentUpload->validate($fileForSave);
        if (isset($error)) {
            $this->addFlashMessage($error . " Soubor neuložen.", FlashSeverityEnum::WARNING);
            return $this->redirectSeeLastGet($request);
        }

        $document = $this->getRightDocument($visitorProfile, $type);
        if (isset($document)) {
            $this->documentUpload->fill($document, $fileForSave);     // výměna – uloží se při flush
        } else {
            $document = $this->documentUpload->createDocument($fileForSave);
            $this->documentRepo->add($document);                     // autoincrement – uloží se hned a dostane id
            if ($type === self::TYPE_CV) {
                $visitorProfile->setCvDocument($document->getId());
            } else {
                $visitorProfile->setLetterDocument($document->getId());
            }
        }
        $label = $type === self::TYPE_CV ? "životopis" : "motivační dopis";
        $this->addFlashMessage("Uložen váš $label: " . $document->getDocumentFilename() . ".", FlashSeverityEnum::SUCCESS);
        return $this->redirectSeeLastGet($request);
    }

    public function remove(ServerRequestInterface $request, $parentId, $type) {
        if (!$this->canEditProfileDocuments($parentId)) {
            $this->addFlashMessage("Chyba oprávnění. Soubor nesmazán.", FlashSeverityEnum::WARNING);
            return $this->redirectSeeLastGet($request);
        }
        $visitorProfile = $this->visitorProfileRepo->get($parentId);
        $document = isset($visitorProfile) ? $this->getRightDocument($visitorProfile, $type) : null;
        if (!isset($document)) {
            $this->addFlashMessage("Soubor nenalezen.", FlashSeverityEnum::WARNING);
        } else {
            // odkaz v profilu zrušit i v entitě (v DB to udělá ON DELETE SET NULL) – jinak by se při flush zapsalo staré id
            if ($type === self::TYPE_CV) {
                $visitorProfile->setCvDocument(null);
            } else {
                $visitorProfile->setLetterDocument(null);
            }
            $this->documentRepo->remove($document);
            $this->addFlashMessage("Soubor smazán. Zájmů o pozici, které jste už odeslali, se to netýká.", FlashSeverityEnum::SUCCESS);
        }
        return $this->redirectSeeLastGet($request);
    }

    //----------------------------------------------------------------------------------------------------------------

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
     *
     * @param ServerRequestInterface $request
     */
    public function uploadVisitorFile(ServerRequestInterface $request) {

        //TODO: self::UPLOADED_KEY -rozlišit uploady z jednotlivých metod

//".doc", ".docx", ".dot", ".odt", "pages", ".xls", ."xlsx", ".ods", ".txt", ".pdf"

        $statusSecurity = $this->statusSecurityRepo->get();
        $loginAggregateCredentials = $statusSecurity->getLoginAggregate();

        if (!isset($loginAggregateCredentials)) {
            $response = (new ResponseFactory())->createResponse()->withStatus(401);  // Unauthorized
        } else {
            $userHash = $loginAggregateCredentials->getLoginNameHash();

            // z konfigurace
            $files = $request->getUploadedFiles();

            // POST - jeden soubor
            /** @var  UploadedFileInterface $file */
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
                
                 /** @var DocumentInterface $documentCv */           
                 /** @var DocumentInterface $documentLettter */     
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
                        $documentCv->getContent(file_get_contents($uploadedFileTemp));
                        $documentCv->getDocumentMimetype($clientMime);
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
                        $documentLetter->setContent(file_get_contents($uploadedFileTemp));
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

        if (!($clientFileName==="")) {
            $this->addFlashMessage($clientFileName);}

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
        } elseif (array_search(pathinfo($clientFileName,  PATHINFO_EXTENSION ), ConfigurationCache::eventsUploads()['upload.events.acceptedextensions'])) {
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

