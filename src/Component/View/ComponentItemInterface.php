<?php
namespace Component\View;
use Component\ViewModel\ViewModelItemInterface;
use Pes\View\Template\FileTemplateInterface;

/**
 *
 * @author pes2704
 */
interface ComponentItemInterface {
    
    /**
     * Vrací view model pro generování view jednoho potomka.
     * 
     * @return ViewModelItemInterface|null
     */
    public function getItemViewModel(): ?ViewModelItemInterface;
    
    /**
     * Nastaví view model pro generování view jednoho potomka.
     * 
     * @param ViewModelItemInterface $collectionViewModel
     */
    public function setItemViewModel(ViewModelItemInterface $collectionViewModel);
    
    /**
     * Nastaví objekt template pro generování html jednoho potomka.
     * 
     * @param FileTemplateInterface $template
     */
    public function setItemTemplate(FileTemplateInterface $template);
    
    /**
     * Nstaví cesty k šablonám pro needitační mód (povinně) a editační mód (volitelně). 
     * Tyto cesty jsou pak automaticky předávány objektu template bezprostředně před renderováním v metodě beforeRenderingHook().
     * 
     * @param type $templateFilePath
     * @param type $editableTemplateFilePath
     */
    public function setItemTemplatePath($templateFilePath, $editableTemplateFilePath=null);
    
    /**
     * Přidá cesty k šablonám pluginů pro needitační mód (povinně) a editační mód (volitelně). Plugin slouží pro renderování jednotlivých itemů - položek v kolekci.
     * Tyto cesty jsou pak automaticky přidávány do dat view modelu bezprostředně před renderováním v metodě beforeRenderingHook().
     * 
     * @param type $name
     * @param type $templateFilePath
     * @param type $editableTemplateFilePath
     */
    public function addPluginTemplatePath($name, $templateFilePath, $editableTemplateFilePath=null);
}
