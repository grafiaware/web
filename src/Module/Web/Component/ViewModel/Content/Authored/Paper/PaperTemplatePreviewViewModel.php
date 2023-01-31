<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Web\Component\ViewModel\Content\Authored\Paper;

/**
 * Description of PaperTemplatePreviewViewModel
 *
 * @author pes2704
 */
class PaperTemplatePreviewViewModel extends PaperViewModel implements PaperTemplatePreviewViewModelInterface {

    private $previewTemplateName;

    public function setPreviewTemplateName($name) {
        $this->previewTemplateName = $name;
    }

    /**
     * Přetěžuje metodu MultipageViewModel, vrací namísto skutečného jména multipage template jméno nastavené metodou setPreviewTemplateName().
     *
     * @return string|null
     */
    public function getAuthoredTemplateName(): ?string {
        return $this->previewTemplateName;
    }

}
