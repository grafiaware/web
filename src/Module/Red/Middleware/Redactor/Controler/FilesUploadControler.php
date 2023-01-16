<?php

namespace Red\Middleware\Redactor\Controler;

use Site\ConfigurationCache;

use FrontControler\FilesUploadControllerAbstract;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use Pes\Http\Response;
use Pes\Utils\Directory;

use Utils\UrlConvertor;

use Red\Model\Entity\MenuItemAsset;
use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusFlashRepo;
use Status\Model\Repository\StatusPresentationRepo;
use Red\Model\Repository\MenuItemAssetRepo;

use Pes\Http\Factory\ResponseFactory;

use Exception;
use Pes\Utils\Exception\CreateDirectoryFailedException;

/**
 * Description of NestedFilesUpload
 *
 * @author pes2704
 */
class FilesUploadControler extends FilesUploadControllerAbstract {

    const UPLOADED_KEY = "file";
    const MAX_FILE_SIZE = "1000000";

    /**
     * @var MenuItemAssetRepo
     */
    private $menuItemAssetRepo;

    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusFlashRepo $statusFlashRepo,
            StatusPresentationRepo $statusPresentationRepo,
            MenuItemAssetRepo $menuItemAssetRepoRepo) {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo);
        $this->menuItemAssetRepo = $menuItemAssetRepoRepo;
    }

    /**
     * Metoda přijímá soubory typu image odesílané image handlerem editoru TinyMCE.
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
    public function uploadEditorImages(ServerRequestInterface $request) {
        $this->setAcceptedExtensions([".png", ".jpg", ".gif"]);
        $time = str_replace(",", "-", $request->getServerParams()["REQUEST_TIME_FLOAT"]); // stovky mikrosekund
        // POST - jeden soubor
        /* @var $uploadedFile UploadedFileInterface */
        $uploadedFile = $request->getUploadedFiles()[self::UPLOADED_KEY];
        if ($uploadedFile->getError() != UPLOAD_ERR_OK) {
            $httpStatus = 400; // 400 Bad Request
            $httpError = $this->uploadErrorMessage($uploadedFile->getError());
        }

        $clientFileName = urldecode($uploadedFile->getClientFilename());  // někdy - např po ImageTools editaci je název souboru z Tiny url kódován
        $clientMime = $uploadedFile->getClientMediaType();
        $clientFileSize = $uploadedFile->getSize();  // v bytech
        $clientFileExt = pathinfo($clientFileName,  PATHINFO_EXTENSION );

        if ($clientFileSize > self::MAX_FILE_SIZE) {
            $httpStatus = 413; // 413 Payload Too Large
            $httpError = "Maximum file size exceeded.";
        }
        if (!in_array($clientFileExt, array("gif", "jpg", "png"))) {
            $httpStatus = 406; // 406 Not Acceptable
            $httpError = "Invalid file extension.";
        }

        $presentedMenuitem = $this->statusPresentationRepo->get()->getMenuItem();
        // relativní cesta vzhledem k root (_files/...)
        $baseFilepath = ConfigurationCache::filesUploadController()['upload.red'];

        try {
            // vytvoř složku pokud neexistuje
            Directory::createDirectory($baseFilepath);
            $targetFilepath = $baseFilepath.$clientFileName;
            $uploadedFile->moveTo($targetFilepath);
        } catch (CreateDirectoryFailedException $e) {
            $httpStatus = 500; // 500 Internal Server Error
            $httpError =  $e->getMessage();
        } catch (Exception $e) {
            $httpStatus = 500; // 500 Internal Server Error
            $httpError =  $e->getMessage();
        }

        if (is_null($this->menuItemAssetRepo->getByFilename($clientFileName))) {
            try {
                $newAsset = new MenuItemAsset();
                $newAsset->setMenuItemIdFk($presentedMenuitem->getId());
                $newAsset->setFilepath($clientFileName);
                $newAsset->setMimeType($clientMime);
                $this->menuItemAssetRepo->add($newAsset);
            } catch (Exception $e) {
                $httpStatus = 500; // 500 Internal Server Error
                $httpError =  $e->getMessage();
                unlink($targetFilepath);
            }
        }
//                $httpStatus = 500; // 500 Internal Server Error
//                $httpError =  'Ajajajajajajajajajaj!';
        if ($httpError) {
            $httpStatus = $httpStatus ?? 404;
            $response = (new ResponseFactory())->createResponse()->withStatus($httpStatus, $httpError);
            $response = $this->addHeaders($request, $response);
        } else {
            // response pro TinyMCE - musí obsahovat json s informací o cestě a jménu uloženého souboru
            // hodnotu v json položce 'location' použije timyMCE pro změnu url obrázku ve výsledném html
            $json = json_encode(array('location' => $targetFilepath));  //
            $response = $this->createResponseFromString($request, $json);
        }
        return $response;
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

        $this->setAcceptedExtensions([".doc", ".docx", ".dot", ".odt", "pages", ".xls", ".xlsx", ".ods", ".txt", ".pdf"]);
        $time = str_replace(",", "-", $request->getServerParams()["REQUEST_TIME_FLOAT"]); // přesnost: stovky mikrosekund
//        $timestamp = (new \DateTime("now"))->getTimestamp();  // přesnost: sekundy
        // POST - jeden soubor
        /* @var $file UploadedFileInterface */
        $file = $request->getUploadedFiles()[self::UPLOADED_KEY];
        $size = 0;
        $item = $this->statusPresentationRepo->get()->getMenuItem();

        $targetFilename = ConfigurationCache::filesUploadControler()['upload.events.visitor'].$item->getLangCodeFk()."_".$item->getId()."-".$file->getClientFilename();
        $file->moveTo($targetFilename);
        $json = json_encode(array('location' => $targetFilename));  // toto jméno použije timyMCE pro změnu url obrázku ve výsledném html
        return $this->createResponseFromString($request, $json);

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

    // KOPIE metody z VisitorControler
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