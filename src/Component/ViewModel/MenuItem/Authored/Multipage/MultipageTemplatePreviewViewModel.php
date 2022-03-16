<?php
namespace Component\ViewModel\MenuItem\Authored\Multipage;

/**
 * Description of MultipageTemplatePreviewViewModel
 *
 * @author pes2704
 */
class MultipageTemplatePreviewViewModel extends MultipageViewModel implements MultipageTemplatePreviewViewModelInterface {

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
