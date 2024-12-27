<?php
namespace Component\View;
use Component\ViewModel\ViewModelItemInterface;
use Pes\View\Template\FileTemplateInterface;

/**
 *
 * @author pes2704
 */
interface ComponentItemInterface {
    public function getItemViewModel(): ?ViewModelItemInterface;
    public function setItemViewModel(ViewModelItemInterface $collectionViewModel);
    public function setItemTemplate(FileTemplateInterface $template);
    public function setItemTemplatePath($templateFilePath, $editableTemplateFilePath=null);
    public function addPluginTemplatePath($name, $templateFilePath, $editableTemplateFilePath=null);
}
