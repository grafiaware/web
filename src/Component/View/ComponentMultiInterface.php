<?php
namespace Component\View;
use Component\ViewModel\ViewModelMultiInterface;
use Pes\View\Template\FileTemplateInterface;

/**
 *
 * @author pes2704
 */
interface ComponentMultiInterface {
    public function setMultiViewModel(ViewModelMultiInterface $multiViewModel);
    public function getMultiViewModel(): ViewModelMultiInterface;
    public function setMultiTemplate(FileTemplateInterface $template);
    public function setMultiTemplatePath($templateFilePath, $editableTemplateFilePath=null);
    public function addPluginTemplatePath($name, $templateFilePath, $editableTemplateFilePath=null);
    
}
