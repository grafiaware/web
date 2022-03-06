<?php
namespace Component\View\Element;

use Component\View\ComponentAbstract;
use Pes\View\InheritDataViewInterface;
use Pes\View\ViewInterface;

/**
 * Description of ElementInheritDataComponent
 *
 * @author pes2704
 */
class ElementInheritDataComponent extends ComponentAbstract implements InheritDataViewInterface {

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
