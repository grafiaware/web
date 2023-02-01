<?php
namespace Red\Component\View\Element;

use Red\Component\View\ComponentCompositeAbstract;
use Pes\View\InheritDataViewInterface;
use Pes\View\ViewInterface;

/**
 * Description of ElementInheritDataComponent
 *
 * @author pes2704
 */
class ElementInheritDataComponent extends ComponentCompositeAbstract implements InheritDataViewInterface {

    /**
     * Předá data beze změny.
     *
     * @param iterable $data
     * @return ViewInterface
     */
    public function inheritData(iterable $data): ViewInterface {
        return $this->setData($data);
    }

}
