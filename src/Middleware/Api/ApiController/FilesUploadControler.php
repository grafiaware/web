<?php

namespace Middleware\Api\ApiController;

use Site\Configuration;

use Controller\PresentationFrontControllerAbstract;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use Pes\Http\Helper\RequestStatus;
use Pes\Http\Response;
use Pes\Text\Html;

/**
 * Description of NestedFilesUpload
 *
 * @author pes2704
 */
class FilesUploadControler extends PresentationFrontControllerAbstract {

    const UPLOADED_KEY = "file";

    const UPLOADED_FOLDER = "uploaded/";

    private $multiple;

    private $accept;

    /**
     * Nastaví, zda bude možné vybrat k uploadu více souborů současně.
     *
     * @param bool $multiple
     */
    public function setMultiple($multiple=false) {
        $this->multiple = (bool) $multiple;
    }

    /**
     * Nastaví omezení přípon souborů, které bude možné uploadovat. Pokud není zadáno, dialog pro váběr souboru nebo souborů nabízí všechny typy souborů.
     *
     * @param array $acceptedExtensions Pole hodnot
     */
    public function setAcceptedExtensions($acceptedExtensions=[]) {
        $accepted = [];
        foreach ($acceptedExtensions as $extension) {
            $accepted[] =".".trim(trim($extension), ".");
        }
        $this->accept = implode(", ", $accepted);
    }

    /**
     * Zpracuje request typu GET nebo POST a vrací response.
     *
     * @return Response
     */
    public function upload(ServerRequestInterface $request) {
        $uploadedFolderPath = "uploaded/";
        $this->setAcceptedExtensions([".png", ".jpg", ".gif"]);
        $time = str_replace(",", "-", $request->getServerParams()["REQUEST_TIME_FLOAT"]); // stovky mikrosekund
//        $timestamp = (new \DateTime("now"))->getTimestamp();  // sekundy
        // POST - jeden soubor
        /* @var $file UploadedFileInterface */
        $file = $request->getUploadedFiles()[self::UPLOADED_KEY];
        $size = 0;
        $targetFilename = Configuration::filesUploadControler()['filesDirectory'].self::UPLOADED_FOLDER.$time."-".$file->getClientFilename();
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