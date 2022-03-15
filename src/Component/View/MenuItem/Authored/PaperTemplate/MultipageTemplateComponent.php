<?php
namespace Component\View\MenuItem\Authored\PaperTemplate;

use Component\View\MenuItem\Authored\AuthoredComponentAbstract;

class MultipageTemplateComponent extends AuthoredComponentAbstract implements MultipageTemplateComponentInterface {

    private $selectedPaperTemplateFileName;
    
    public function setSelectedPaperTemplateFileName($name): void {
        $this->selectedPaperTemplateFileName = $name;
    }    
}
