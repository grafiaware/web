<?php

namespace FrontControler;

use Site\ConfigurationCache;

use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusFlashRepo;
use Status\Model\Repository\StatusPresentationRepo;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use Pes\Http\Response;

use FrontControler\Exception\UploadFileException;

/**
 * Description of NestedFilesUpload
 *
 * @author pes2704
 */
class FilesUploadControllerAbstract extends PresentationFrontControlerAbstract {

    const UPLOADED_KEY = "file";
    const MAX_FILE_SIZE = 0;
    const ACCEPTED_EXTENSIONS = [];
    
    private $multiple;

    private $accept;
    
    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusFlashRepo $statusFlashRepo,
            StatusPresentationRepo $statusPresentationRepo) {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo);
    }
    
    /**
     * 
     * @param ServerRequestInterface $request
     * @param type $uploadedKey
     * @param type $maxFileSize
     * @param type $acceptedExtensions
     * @return UploadedFileInterface
     * @throws UploadFileException
     */
    protected function getAndValidateUploadedFile(
                ServerRequestInterface $request,
                $uploadedKey,
                $maxFileSize,
                $acceptedExtensions = []
            ): UploadedFileInterface {  
        // POST - jeden soubor
        /* @var $uploadedFile UploadedFileInterface */
        $uploadedFile = $request->getUploadedFiles()[$uploadedKey];
        if ($uploadedFile->getError() != UPLOAD_ERR_OK) {
            // messege z uploadErrorMessage()
            throw new UploadFileException('Bad Request. Redactor: '.$this->uploadErrorMessage($uploadedFile->getError()), 400); // 400 Bad Request
        }

        $clientFileName = urldecode($uploadedFile->getClientFilename());  // někdy - např po ImageTools editaci je název souboru z Tiny url kódován
        $clientFileSize = $uploadedFile->getSize();  // v bytech
        $clientFileExt = pathinfo($clientFileName,  PATHINFO_EXTENSION );

        if ($clientFileSize > $maxFileSize) {
            $message = "Maximum controler file size ".$maxFileSize." bytes exceeded.";
            throw new UploadFileException('Payload Too Large. Redactor: '.$message, 413); // 413 Payload Too Large
        }
        if (!in_array('.'.$clientFileExt, $acceptedExtensions)) {   // extension s tečkou - pro sjednocení s javascript
            $accepted = "'".implode("', '", $acceptedExtensions)."'";
            $message = "Invalid file extension '$clientFileExt'. Accepted only: $accepted.";
            throw new UploadFileException('Not Acceptable. Redactor: '.$message, 406);  // 406 Not Acceptable
        }
        return $uploadedFile;
    }
    
        // KOPIE metody z VisitorControler
    protected function uploadErrorMessage($error) {
        switch ($error) {
            case UPLOAD_ERR_OK:
                $message = 'There is no error, the file uploaded with success.';
                break;
            case UPLOAD_ERR_INI_SIZE:
                $message = 'The uploaded file exceeds the upload_max_filesize directive in php.ini.';
                break;
            case UPLOAD_ERR_FORM_SIZE:
                $message = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.';
                break;
            case UPLOAD_ERR_PARTIAL:
                $message = 'The uploaded file was only partially uploaded.';
                break;
            case UPLOAD_ERR_NO_FILE:
                $message = 'No file was uploaded.';
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $message = 'Missing a temporary folder.';
                break;
            case UPLOAD_ERR_CANT_WRITE:
                $message = 'Failed to write file to disk.';
                break;
            case UPLOAD_ERR_EXTENSION:
                $message = 'File upload stopped by extension.';
                break;
            default:
                $message = 'Unknown upload error.';
                break;
        }
        return $message;
    }
    
    /**
     * Nastaví, zda bude možné vybrat k uploadu více souborů současně.
     *
     * @param bool $multiple
     */
    protected function setMultiple($multiple=false) {
        $this->multiple = (bool) $multiple;
    }

    /**
     * Nastaví omezení přípon souborů, které bude možné uploadovat. Pokud není zadáno, dialog pro váběr souboru nebo souborů nabízí všechny typy souborů.
     *
     * @param array $acceptedExtensions Pole hodnot
     */
    protected function setAcceptedExtensions($acceptedExtensions=[]) {
        $accepted = [];
        foreach ($acceptedExtensions as $extension) {
            $accepted[] =".".trim(trim($extension), ".");
        }
        $this->accept = implode(", ", $accepted);
    }
    
    protected function getAcceptedExtensions(): string {
        return $this->accept ?? '';
    }
    
    /**
     * Vrací čas uploadu souboru odvozený z času requestu. Nahradí desetinou čárku v čísle pomlčkou. Řetězec je možné použít jako součást názvu souboru.
     *
     * @param ServerRequestInterface $request
     * @return string
     */
    protected function getUploadTimeString(ServerRequestInterface $request) {
        return str_replace(",", "-", $request->getServerParams()["REQUEST_TIME_FLOAT"]); // stovky mikrosekund
//        $timestamp = (new \DateTime("now"))->getTimestamp();  // sekundy
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
