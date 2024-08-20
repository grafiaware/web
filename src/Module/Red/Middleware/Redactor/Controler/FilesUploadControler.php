<?php

namespace Red\Middleware\Redactor\Controler;

use Site\ConfigurationCache;

use FrontControler\FilesUploadControllerAbstract;
use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusFlashRepo;
use Status\Model\Repository\StatusPresentationRepo;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use Pes\Http\Response;
use Red\Service\Asset\AssetServiceInterface;
use Pes\Http\Factory\ResponseFactory;

use Exception;
use Pes\Utils\Exception\CreateDirectoryFailedException;
use FrontControler\Exception\UploadFileException;

/**
 * Description of NestedFilesUpload
 *
 * @author pes2704
 */
class FilesUploadControler extends FilesUploadControllerAbstract {

    private $assetService;
    
    public function __construct(
            StatusSecurityRepo $statusSecurityRepo, 
            StatusFlashRepo $statusFlashRepo, 
            StatusPresentationRepo $statusPresentationRepo,
            AssetServiceInterface $assetService) {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo);
        $this->assetService = $assetService;
    }
    
    const UPLOADED_KEY = "file";
    const IMAGE_MAX_FILE_SIZE = 1E6;
    // MUSÍ se shodovat s možnými file typy vybíranými v filePickerCallback funkci pro tinyMCE
    const IMAGE_ACCEPTED_EXTENSIONS = ['.apng','.avif','.jpeg','.jpg','.jpe','.jfi','.jif','.jfif','.png','.gif','.bmp','.svg','.webp'];
    const ATTACHMENT_MAX_FILE_SIZE = 10E6;
    // MUSÍ se shodovat s možnými file typy vybíranými v filePickerCallback funkci pro tinyMCE
//    const ATTACHMENT_ACCEPTED_EXTENSIONS = ['pdf'];    
    const ATTACHMENT_ACCEPTED_EXTENSIONS = [".doc", ".docx", ".dot", ".odt", ".pages", ".xls", ".xlsx", ".ods", ".txt", ".pdf"];

    public function imageUpload(ServerRequestInterface $request) {
        return $this->storeUpload(
                    $request, 
                    static::UPLOADED_KEY, 
                    static::IMAGE_MAX_FILE_SIZE, 
                    static::IMAGE_ACCEPTED_EXTENSIONS);
    }
    
    public function attachmentUpload(ServerRequestInterface $request) {
        return $this->storeUpload(
                    $request, 
                    static::UPLOADED_KEY, 
                    static::ATTACHMENT_MAX_FILE_SIZE, 
                    static::ATTACHMENT_ACCEPTED_EXTENSIONS);
    }
    
    /**
     * Metoda přijímá soubory typu image a video odesílané image handlerem editoru TinyMCE.
     *
     * Image handler je skript definovaný v v inicializaci TinyMCE (image_upload_handler() ).
     * Image handler odesílá POST request s s daty FormData, ve kterých je položka odpovídající <input> typu 'file' s uploadovaným souborem.
     *
     * Metoda zpracuje request s uploadovaným souborem. Uploadovaný soubor se pokusí uložit do složky určené pro soubory prezentovaného menu item a
     * v případě úspěchu vrací response, který obsahuje jako obsah (body) json informující odesilající skript (image_upload_handler())
     * o skutečném plném jménu uloženého souboru. Tento json obsahuje položku "Location" s plným jménem uloženého souboru. Toto jméno souboru image_upload_handler()
     * vloží zpět do html kódu jako url obrázku (hodnotu src atributu tagu <image>).
     *
     * @return Response
     */
    private function storeUpload(
            ServerRequestInterface $request,
            $uploadedKey,
            $maxFileSize,
            $acceptedExtensions = []) {
        try {
            $uploadedFile = $this->getAndValidateUploadedFile($request, $uploadedKey, $maxFileSize, $acceptedExtensions);
        } catch (UploadFileException $e) {
            $httpStatus = $e->getCode(); // http status kód byl předán do Exception->code v getAndValidateUploadedFile()
            $httpError =  $e->getMessage();
        } catch (CreateDirectoryFailedException $e) {
            $httpStatus = 500; // 500 Internal Server Error
            $httpError =  $e->getMessage();
        } catch (Exception $e) {
            $httpStatus = 500; // 500 Internal Server Error
            if($e->getMessage()) {
                $httpError =  $e->getMessage();
            } else {
                $httpError = get_class($e);   // vypíše typ exception - typicky PDO exception nemá message
            }
        }

        if (isset($httpError)) {
            $response = $this->errorResponse($request, $httpError, $httpStatus);
        } else {
            $editedItemId = $this->getEditedItemId($request);
            $editor = $this->statusSecurityRepo->get()->getLoginAggregate()->getLoginName();
            $targetFilepath = $this->assetService->storeAsset($uploadedFile, $editedItemId, $editor);
            $response = $this->okTinyJsonResponse($targetFilepath);
        }

        return $response;
    }
    
    private function getEditedItemId(ServerRequestInterface $request) {
        $editedItemId = $this->paramValue($request, 'edited_item_id');
        if ($editedItemId) {    
            return $editedItemId;
        }else {
            throw new UploadFileException("Not Acceptable. Redactor: Request has no 'edited_item_id' variable.", 406);
        }    
    }
    
    private function errorResponse($httpError, $httpStatus=null) {
        return $this->addCacheHeaders((new ResponseFactory())->createResponse()->withStatus($httpStatus ?? 404, $httpError));
    }
    
    private function okTinyJsonResponse($targetFilepath) {
        // response pro TinyMCE - musí obsahovat json s informací o cestě a jménu uloženého souboru
        // hodnotu v json položce 'location' použije timyMCE pro změnu url obrázku ve výsledném html
//        $json = json_encode(['location' => $targetFilepath]);  //
//        
//        $response = $this->createStringOKResponse($json);
//        return $response->withHeader('Content-Type', 'application/json');
        
        return $this->createJsonOKResponse(['location' => $targetFilepath]);
    }

    /**
     *
     * @param ServerRequestInterface $request
     * @return type
     */
    public function uploadTxtDocuments(ServerRequestInterface $request) {

        //TODO: self::UPLOADED_KEY -rozlišit uploady z jednotlivých metod

//".doc", ".docx", ".dot", ".odt", ".pages", ".xls", ."xlsx", ".ods", ".txt", ".pdf"

        $statusSecurity = $this->statusSecurityRepo->get();

        $this->setAcceptedExtensions([".doc", ".docx", ".dot", ".odt", ".pages", ".xls", ".xlsx", ".ods", ".txt", ".pdf"]);
        $time = str_replace(",", "-", $request->getServerParams()["REQUEST_TIME_FLOAT"]); // přesnost: stovky mikrosekund
//        $timestamp = (new \DateTime("now"))->getTimestamp();  // přesnost: sekundy
        // POST - jeden soubor
        /* @var $file UploadedFileInterface */
        $file = $request->getUploadedFiles()[self::UPLOADED_KEY];
        $size = 0;
        
        // item je vždy presented item, t.j. item, který byl zobrazen po kliknutí na lolořky menu - nelze uploadovat soubor pro obsah, který nebyl zobrazen po kliknutí na lolořky menu
        $item = $this->statusPresentationRepo->get()->getMenuItem();

        $targetFilename = ConfigurationCache::eventsUploads()['upload.events.visitor'].$item->getLangCodeFk()."_".$item->getId()."-".$file->getClientFilename();
        $file->moveTo($targetFilename);
        $json = json_encode(array('location' => $targetFilename));  // toto jméno použije timyMCE pro změnu url obrázku ve výsledném html
        return $this->createStringOKResponse($json);

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
          $imageFolder = "@images/";

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

#########################################################

// When an image is inserted into the editor after image upload...
//editor.on('NodeChange', function (e) {
//    if (e.element.tagName === "IMG") {
//        // Set an alt attribute. We will insert the value when the image was successfully uploaded and the imageId was returned from the server.
//        // We saved the alt tag that we want into the imageId hidden input field (imageId) when the server returned data.
//        // Here, we are taking that value and inserting the new alt tag.
//        e.element.setAttribute("alt", $("#imageId").val());
//        return;
//    }
//    return;
//});