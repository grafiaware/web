<?php

namespace Middleware\Api\ApiController;

use Site\Configuration;

use Controller\PresentationFrontControllerAbstract;

use Model\Repository\{
    StatusSecurityRepo, StatusFlashRepo, StatusPresentationRepo, VisitorDataRepo
};

use Model\Entity\VisitorData;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;

use Pes\Http\Helper\RequestStatus;
use Pes\Http\Request\RequestParams;


use Pes\Http\Factory\ResponseFactory;
use Pes\Http\Response;

/**
 * Description of NestedFilesUpload
 *
 * @author pes2704
 */
class VisitorDataUploadControler extends PresentationFrontControllerAbstract {

    const UPLOADED_KEY_CV = "visitor-cv";
    const UPLOADED_KEY_LETTER = "visitor-letter";

//    private $multiple = false;
//
//    private $accept;

    private $visitorDataRepo;

    private $menuItemRepo;

    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusFlashRepo $statusFlashRepo,
            StatusPresentationRepo $statusPresentationRepo,
            VisitorDataRepo $visitorDataRepo
            ) {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo);
        $this->visitorDataRepo = $visitorDataRepo;
    }
    /**
     * Nastaví, zda bude možné vybrat k uploadu více souborů současně.
     *
     * @param bool $multiple
     */
//    public function setMultiple($multiple=false) {
//        $this->multiple = (bool) $multiple;
//    }

    /**
     * Nastaví omezení přípon souborů, které bude možné uploadovat. Pokud není zadáno, dialog pro váběr souboru nebo souborů nabízí všechny typy souborů.
     *
     * @param array $acceptedExtensions Pole hodnot
     */
//    public function setAcceptedExtensions($acceptedExtensions=[]) {
//        $accepted = [];
//        foreach ($acceptedExtensions as $extension) {
//            $accepted[] =".".trim(trim($extension), ".");
//        }
//        $this->accept = implode(", ", $accepted);
//    }

    public function saveData(ServerRequestInterface $request) {
        $statusSecurity = $this->statusSecurityRepo->get();
        $loginAggregateCredentials = $statusSecurity->getLoginAggregate();

        if (!isset($loginAggregateCredentials)) {
            $response = (new ResponseFactory())->createResponse();
            $response = $response->withStatus(401);  // Unaathorized
        } else {
            $loginName = $loginAggregateCredentials->getLoginName();

            $visitorData = $this->visitorDataRepo->get($loginName);
            if (!isset($visitorData)) {
                $visitorData = new VisitorData();
                $visitorData->setLoginName($loginName);
                $this->visitorDataRepo->add($visitorData);
            }
            // POST data
            $visitorData->setPrefix((new RequestParams())->getParsedBodyParam($request, 'prefix'));
            $visitorData->setName((new RequestParams())->getParsedBodyParam($request, 'name'));
            $visitorData->setSurname((new RequestParams())->getParsedBodyParam($request, 'surname'));
            $visitorData->setPostfix((new RequestParams())->getParsedBodyParam($request, 'postfix'));
            $visitorData->setEmail((new RequestParams())->getParsedBodyParam($request, 'email'));
            $visitorData->setPhone((new RequestParams())->getParsedBodyParam($request, 'phone'));
            $visitorData->setCvEducationText((new RequestParams())->getParsedBodyParam($request, 'cv-education-text'));
            $visitorData->setCvSkillsText((new RequestParams())->getParsedBodyParam($request, 'cv-skills-text'));

//            $this->addFlashMessage(" Data uložena");
            return $this->redirectSeeLastGet($request);
        }
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
            $response = $response->withStatus(401);  // Unaathorized
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
                $response = $this->createResponeIfError($fileForSave);
            } else {
                $this->addFlashMessage("neodeslán žádný soubor. Soubor neuložen.");
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

                $visitorData = $this->visitorDataRepo->get($loginName);
                if (!isset($visitorData)) {
                    $visitorData = new VisitorData();
                    $visitorData->setLoginName($loginName);
                    $this->visitorDataRepo->add($visitorData);
                }

                switch ($type) {
                    case self::UPLOADED_KEY_CV:
                        $visitorData->setCvDocument(file_get_contents($uploadedFileTemp));
                        $visitorData->setCvDocumentMimetype($clientMime);
                        $visitorData->setCvDocumentFilename($clientFileName);
                        $this->addFlashMessage("Uložen váš životopis.");
                        break;
                    case self::UPLOADED_KEY_LETTER:
                        $visitorData->setLetterDocument(file_get_contents($uploadedFileTemp));
                        $visitorData->setLetterDocumentMimetype($clientMime);
                        $visitorData->setLetterDocumentFilename($clientFileName);
                        $this->addFlashMessage("Uložen váš motivační dopis.");
                        break;
                    default:
                        break;
                }
                $flashMessage = "Uloženo $size bytů.";
                $this->addFlashMessage($flashMessage);

//            $targetFilename = Configuration::filesUploadControler()['uploads.events.visitor'].self::UPLOADED_FOLDER.$item->getLangCodeFk()."_".$item->getId()."-".$file->getClientFilename();
//            $file->moveTo($targetFilename);
                $response = $this->redirectSeeLastGet($request);
            } else {
                $this->addFlashMessage("Chyba oprávnění.");
                $this->addFlashMessage("Soubor neuložen!");
                $this->redirectSeeLastGet($request);
            }
        } else {
            if (isset($clientFileName)) {
                $this->addFlashMessage($clientFileName);
            }
            $this->addFlashMessage("Soubor neuložen!");
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
                $response = 'Unknown upload error';
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

        $this->visitorDataRepo->add($visitorData);
    }

    private function createResponeIfError(UploadedFileInterface $uploadfile) {
        $clientFileName = $uploadfile->getClientFilename();
        $error = $uploadfile->getError();
        $size = $uploadfile->getSize();  // v bytech

        $this->addFlashMessage($clientFileName);

        // Sanitize input // Remove anything which isn't a word, whitespace, number or any of the following caracters -_~,;[]().
//        $fileNameError = preg_match("/([^\w\s\d\-_~,;:\[\]\(\).])|([\.]{2,})/", $clientFileName);
//        if ($fileNameError) {
//            $response = (new ResponseFactory())->createResponse();
//            $response = $response->withStatus(400, "Bad Request. Invalid file name.");
//            $this->addFlashMessage("Chybné kméno souboru.");
////                header("HTTP/1.1 400 Invalid file name.");
//        } else
            if (array_search(pathinfo($clientFileName,  PATHINFO_EXTENSION ), Configuration::filesUploadControler()['uploads.acceptedextensions'])) {
            $response = (new ResponseFactory())->createResponse();
            $response = $response->withStatus(400, "Bad Request. Invalid file extesion.");
            $this->addFlashMessage("Chybná přípona souboru.");
        } elseif ($error != UPLOAD_ERR_OK) {
            $errMessage = $this->uploadErrorMessage($error);
            $response = (new ResponseFactory())->createResponse();
            $response = $response->withStatus(500, "Server or transfer error. $errMessage");
            $this->addFlashMessage("Nepodařilo se nahrát soubor.");
        } elseif ($size>10000000) {
            $response = (new ResponseFactory())->createResponse();
            $response = $response->withStatus(400, "Bad Request. File size is over limit.");
            $this->addFlashMessage("Soubor je příliš velký. Maximum je 10MB.");
        } elseif ($size<10) {
            $response = (new ResponseFactory())->createResponse();
            $response = $response->withStatus(400, "Bad Request. File size is under limit.");
            $this->addFlashMessage("Soubor je prázdný.");
        }
        return $response ?? null;

    }

    public function tinyMcePostAcceptor($param) {
        //  https://www.tiny.cloud/docs/advanced/php-upload-handler/

        /***************************************************
           * Only these origins are allowed to upload images *
           ***************************************************/
          $accepted_origins = array("http://localhost", "http://192.168.1.1", "http://example.com");

          /*********************************************
           * Change this line to set the upload folder *
           *********************************************/
          $imageFolder = "images/";

          if (isset($_SERVER['HTTP_ORIGIN'])) {
            // same-origin requests won't set an origin. If the origin is set, it must be valid.
            if (in_array($_SERVER['HTTP_ORIGIN'], $accepted_origins)) {
              header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
            } else {
              header("HTTP/1.1 403 Origin Denied");
              return;
            }
          }

          // Don't attempt to process the upload on an OPTIONS request
          if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            header("Access-Control-Allow-Methods: POST, OPTIONS");
            return;
          }

          reset ($_FILES);
          $temp = current($_FILES);
          if (is_uploaded_file($temp['tmp_name'])){
            /*
              If your script needs to receive cookies, set images_upload_credentials : true in
              the configuration and enable the following two headers.
            */
            // header('Access-Control-Allow-Credentials: true');
            // header('P3P: CP="There is no P3P policy."');

            // Sanitize input
            if (preg_match("/([^\w\s\d\-_~,;:\[\]\(\).])|([\.]{2,})/", $temp['name'])) {
                header("HTTP/1.1 400 Invalid file name.");
                return;
            }

            // Verify extension
            if (!in_array(strtolower(pathinfo($temp['name'], PATHINFO_EXTENSION)), array("gif", "jpg", "png"))) {
                header("HTTP/1.1 400 Invalid extension.");
                return;
            }

            // Accept upload if there was no origin, or if it is an accepted origin
            $filetowrite = $imageFolder . $temp['name'];
            move_uploaded_file($temp['tmp_name'], $filetowrite);

            // Determine the base URL
            $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? "https://" : "http://";
            $baseurl = $protocol . $_SERVER["HTTP_HOST"] . rtrim(dirname($_SERVER['REQUEST_URI']), "/") . "/";

            // Respond to the successful upload with JSON.
            // Use a location key to specify the path to the saved image resource.
            // { location : '/your/uploaded/image/file'}
            echo json_encode(array('location' => $baseurl . $filetowrite));
          } else {
            // Notify editor that the upload failed
            header("HTTP/1.1 500 Server Error");
          }

    }

}

//$html = '
//<!-- Learn about this code on MDN: https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input/file -->
//
//<form method="post" enctype="multipart/form-data">
//  <div>
//    <label for="image_uploads">Choose images to upload (PNG, JPG)</label>
//    <input type="file" id="image_uploads" name="'.self::UPLOADED_KEY.'[]" accept=".jpg, .jpeg, .png" multiple>
//  </div>
//  <div class="preview">
//    <p>No files currently selected for upload</p>
//  </div>
//  <div>
//    <button>Submit</button>
//  </div>
//</form>';


//                <input type="file" name="filesupl[]" multiple webkitdirectory id="files" />
