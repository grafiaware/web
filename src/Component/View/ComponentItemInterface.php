<?php
namespace Component\View;
use Component\ViewModel\ViewModelItemInterface;

/**
 *
 * @author pes2704
 */
interface ComponentItemInterface {
    public function setItemViewModel(ViewModelItemInterface $collectionViewModel);
    public function addPluginTemplateName($name, $templateFilePath);
}
