<?php

namespace FrontController;

use Site\Configuration;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use Pes\Http\Response;

/**
 * Description of NestedFilesUpload
 *
 * @author pes2704
 */
class FilesUploadControllerAbstract extends PresentationFrontControllerAbstract {

    const UPLOADED_KEY = "file";

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
     * Vrací čas uploadu souboru odvozený z času requestu. Nahradí desetinou čárku v čísle pomlčkou. Řetězec je možné použít jako součást názvu souboru.
     *
     * @param ServerRequestInterface $request
     * @return string
     */
    public function getUploadTimeString(ServerRequestInterface $request) {
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
