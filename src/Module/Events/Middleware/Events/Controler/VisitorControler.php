<?php

namespace Events\Middleware\Events\Controler;

use Site\ConfigurationCache;

use FrontControler\FrontControlerAbstract;

use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusFlashRepo;
use Status\Model\Repository\StatusPresentationRepo;

use Events\Model\Repository\VisitorProfileRepo;
use Events\Model\Repository\VisitorJobRequestRepo;
use Events\Model\Repository\DocumentRepo;

//TODO: chybný namespace Red
use Red\Model\Entity\LoginAggregateFullInterface;

use Events\Model\Entity\VisitorProfile;
use Events\Model\Entity\Document;
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
 * Description of NestedFilesUpload
 *
 * @author pes2704
 */
class VisitorControler extends FrontControlerAbstract {

    const UPLOADED_KEY_CV = "visitor-cv";
    const UPLOADED_KEY_LETTER = "visitor-letter";

//    private $multiple = false;
//
//    private $accept;

    private $visitorProfileRepo;

    private $visitorJobRequestRepo;
    
    private $documentRepo;

    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusFlashRepo $statusFlashRepo,
            StatusPresentationRepo $statusPresentationRepo,
            VisitorProfileRepo $visitorProfileRepo,
            VisitorJobRequestRepo $visitorJobRequesRepo,     //?
            
            DocumentRepo $documentRepo
            ) {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo);
        $this->visitorProfileRepo = $visitorProfileRepo;
        $this->visitorJobRequestRepo = $visitorJobRequesRepo;
        
        $this->documentRepo = $documentRepo;

    }

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

    
    public function sendJobRequest(ServerRequestInterface $request) {
        $statusSecurity = $this->statusSecurityRepo->get();
        $loginAggregateCredentials = $statusSecurity->getLoginAggregate();

        if (!isset($loginAggregateCredentials)) {
            $response = (new ResponseFactory())->createResponse();
            return $response->withStatus(401);  // Unaathorized
        } else {
            $isPresenter = $loginAggregateCredentials->getCredentials()->getRole()=== ConfigurationCache::loginLogoutController()['rolePresenter'];
            if ($isPresenter) {
                // POST data
                $shortName = (new RequestParams())->getParsedBodyParam($request, 'short-name');
                $positionName = (new RequestParams())->getParsedBodyParam($request, 'position-name');
                $visitorLoginName = (new RequestParams())->getParsedBodyParam($request, "visitor-login-name");

                $visitorDataPost = $this->visitorJobRequestRepo->get($visitorLoginName, $shortName, $positionName);
                if (!isset($visitorDataPost)) {
                    $this->addFlashMessage("Pracovní údaje pro pozici $positionName nenalezeny v databázi.");
                } else {
                    $this->sendMail($positionName, $visitorDataPost, $loginAggregateCredentials);
                    $this->addFlashMessage("Pracovní údaje pro pozici $positionName odeslány.");
                }
            } else {
                $this->addFlashMessage("Pracovní údaje smí mailem odesílat pouze vystavovatel.");
            }
        }
        return $this->redirectSeeLastGet($request);
    }

    private function sendMail($positionName, VisitorJobRequestInterface $visitorDataPost, LoginAggregateFullInterface $loginAggregateCredentials) {

        /** @var Mail $mail */
        $mail = $this->container->get(Mail::class);
        /** @var HtmlMessage $mailMessageFactory */
        $mailMessageFactory = $this->container->get(HtmlMessage::class);
        $subject =  'Veletrh práce - pracovní údaje návštěvníka ';
        $body = $mailMessageFactory->create(__DIR__."/Messages/pracovni-udaje-navstevnika.php",
                                            [
                                                'positionName' => $positionName,
                                                'visitorDataPost' => $visitorDataPost]);
        if ($visitorDataPost->getCvDocument()) {
             $attachments[] = (new StringAttachment())
                        ->setStringAttachment($visitorDataPost->getCvDocument())
                        ->setAltText($visitorDataPost->getCvDocumentFilename());
        }
        if ($visitorDataPost->getLetterDocument()) {
             $attachments[] = (new StringAttachment())
                        ->setStringAttachment($visitorDataPost->getLetterDocument())
                        ->setAltText($visitorDataPost->getLetterDocumentFilename());
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
        $mail->mail($params); // posle mail
    }

    /**
     * POST visitor data posted for presenter
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

            // POST data
            $shortName = (new RequestParams())->getParsedBodyParam($request, 'short-name');
            $positionName = (new RequestParams())->getParsedBodyParam($request, 'position-name');

            $visitorData = $this->visitorProfileRepo->get($loginName);
            $visitorDataPost = $this->visitorJobRequestRepo->get($loginName, $shortName, $positionName);

            if (!isset($visitorDataPost)) {
                $visitorDataPost = new VisitorDataPost();
                $visitorDataPost->setLoginLoginName($loginName);
                $visitorDataPost->setJobId($shortName);
                $visitorDataPost->setPositionName($positionName);
                $this->visitorJobRequestRepo->add($visitorDataPost);
            }

//            $visitorDataPost->setPrefix($visitorData->getPrefix());
//            $visitorDataPost->setName($visitorData->getName());
//            $visitorDataPost->setSurname($visitorData->getSurname());
//            $visitorDataPost->setPostfix($visitorData->getPostfix());
//            $visitorDataPost->setEmail($visitorData->getEmail());
//            $visitorDataPost->setPhone($visitorData->getPhone());
//            $visitorDataPost->setCvEducationText($visitorData->getCvEducationText());
//            $visitorDataPost->setCvSkillsText($visitorData->getCvSkillsText());

            // POST data
            $visitorDataPost->setPrefix((new RequestParams())->getParsedBodyParam($request, 'prefix'));
            $visitorDataPost->setName((new RequestParams())->getParsedBodyParam($request, 'name'));
            $visitorDataPost->setSurname((new RequestParams())->getParsedBodyParam($request, 'surname'));
            $visitorDataPost->setPostfix((new RequestParams())->getParsedBodyParam($request, 'postfix'));
            $visitorDataPost->setEmail((new RequestParams())->getParsedBodyParam($request, 'email'));
            $visitorDataPost->setPhone((new RequestParams())->getParsedBodyParam($request, 'phone'));
            $visitorDataPost->setCvEducationText((new RequestParams())->getParsedBodyParam($request, 'cv-education-text'));
            $visitorDataPost->setCvSkillsText((new RequestParams())->getParsedBodyParam($request, 'cv-skills-text'));

            $this->uploadDocs($request, $visitorDataPost);

            if (!$visitorDataPost->getCvDocument()) {
                $visitorDataPost->setCvDocument($visitorData->getCvDocument());
                $visitorDataPost->setCvDocumentFilename($visitorData->getCvDocumentFilename());
                $visitorDataPost->setCvDocumentMimetype($visitorDataPost->getCvDocumentMimetype());
            }
            if (!$visitorDataPost->getLetterDocument()) {
                $visitorDataPost->setLetterDocument($visitorData->getLetterDocument());
                $visitorDataPost->setLetterDocumentFilename($visitorData->getLetterDocumentFilename());
                $visitorDataPost->setLetterDocumentMimetype($visitorDataPost->getLetterDocumentMimetype());
            }

            $this->addFlashMessage("Pracovní údaje odeslány pro pozici $positionName.");
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
    private function uploadDocs(ServerRequestInterface $request, VisitorDataPost $visitorDataPost) {

        //TODO: self::UPLOADED_KEY -rozlišit uploady z jednotlivých metod
        //".doc", ".docx", ".dot", ".odt", "pages", ".xls", ."xlsx", ".ods", ".txt", ".pdf"

        $statusSecurity = $this->statusSecurityRepo->get();
        $loginAggregateCredentials = $statusSecurity->getLoginAggregate();
        if (isset($loginAggregateCredentials)) {

            $userHash = $loginAggregateCredentials->getLoginNameHash();
            $uploadError = '';

            $files = $request->getUploadedFiles();

            // POST - jeden soubor
            /* @var $file UploadedFileInterface */
            if(isset($files) AND $files) {
                foreach ($files as $uploadedFileName => $file) {
                    if ($file->getError()==UPLOAD_ERR_OK) {
                        switch ($uploadedFileName) {
                            case self::UPLOADED_KEY_CV.$userHash:
                                $this->hydrateVisitorDataByFile($file, self::UPLOADED_KEY_CV, $visitorDataPost);
                                break;
                            case self::UPLOADED_KEY_LETTER.$userHash:
                                $this->hydrateVisitorDataByFile($file, self::UPLOADED_KEY_LETTER, $visitorDataPost);
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

    private function hydrateVisitorDataByFile($fileForSave, $type, VisitorDataPost $visitorDataPost) {

    //        $time = str_replace(",", "-", $request->getServerParams()["REQUEST_TIME_FLOAT"]); // stovky mikrosekund
    //        $timestamp = (new \DateTime("now"))->getTimestamp();  // sekundy

        // file move to temp
        $clientFileName = $fileForSave->getClientFilename();
        $clientMime = $fileForSave->getClientMediaType();
        $size = $fileForSave->getSize();  // v bytech
        $ext = pathinfo($clientFileName,  PATHINFO_EXTENSION );
        $uploadedFileTemp = tempnam(sys_get_temp_dir(), hash('sha256', $clientFileName)).'.'.$ext;
        $fileForSave->moveTo($uploadedFileTemp);

        switch ($type) {
            case self::UPLOADED_KEY_CV:
                $visitorDataPost->setCvDocument(file_get_contents($uploadedFileTemp));
                $visitorDataPost->setCvDocumentMimetype($clientMime);
                $visitorDataPost->setCvDocumentFilename($clientFileName);
                break;
            case self::UPLOADED_KEY_LETTER:
                $visitorDataPost->setLetterDocument(file_get_contents($uploadedFileTemp));
                $visitorDataPost->setLetterDocumentMimetype($clientMime);
                $visitorDataPost->setLetterDocumentFilename($clientFileName);
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
     * @return type
     */
    public function uploadTxtDocuments(ServerRequestInterface $request) {

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

                $visitorData = $this->visitorProfileRepo->get($loginName);
                if (!isset($visitorData)) {
                    $visitorData = new VisitorData();
                    $visitorData->setLoginName($loginName);
                    $this->visitorProfileRepo->add($visitorData);
                }
               
                //----------             
                $documentCvId = $visitorData->getCvDocument();
                $documentLettterId = $visitorData->getLetterDocument();            
                switch ($type) {
                    case self::UPLOADED_KEY_CV:
                        if (!isset($documentCvId)) {
                            $documentCv = new Document();
                            $this->documentRepo->add($documentCv);
                        }                        
                        $documentCv->setDocument(file_get_contents($uploadedFileTemp));
                        $documentCv->setDocumentMimetype($clientMime);
                        $documentCv->setDocumentFilename($clientFileName);
                        $visitorData->setCvDocument($documentCv->getId());
                        
                        $this->addFlashMessage("Uložen váš životopis.", FlashSeverityEnum::SUCCESS);
                        break;
                        
                    case self::UPLOADED_KEY_LETTER:
                        if (!isset($documentLettterId)) {
                            $documentLetter = new Document();
                            $this->documentRepo->add($documentLetter);
                        }
                        $documentLetter->setDocument(file_get_contents($uploadedFileTemp));
                        $documentLetter->setDocumentMimetype($clientMime);
                        $documentLetter->setDocumentFilename($clientFileName);
                        $visitorData->setLetterDocument($documentLetter->getId());
                        
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
        } elseif ($error != UPLOAD_ERR_OK) {
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
