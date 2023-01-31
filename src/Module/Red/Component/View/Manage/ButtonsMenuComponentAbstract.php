<?php
namespace Red\Component\View\Manage;

use Red\Component\View\ComponentCollectionAbstract;

use Pes\View\InheritDataViewInterface;
use Pes\View\ViewInterface;

/**
 * Description of ButtonsMenuComponent
 *
 * @author pes2704
 */
abstract class ButtonsMenuComponentAbstract extends ComponentCollectionAbstract implements InheritDataViewInterface {

    public function inheritData(iterable $data): ViewInterface {
        $this->setData($data);
        return $this;
    }

}
