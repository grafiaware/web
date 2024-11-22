<?php
namespace Component\View;

use Component\View\ComponentCompositeAbstract;
use Pes\View\InheritViewModelInterface;
use Pes\View\ViewInterface;

/**
 * Description of ElementInheritDataComponent
 *
 * @author pes2704
 */
class ElementInheritViewModelComponent extends ElementComponent implements InheritViewModelInterface {

    /**
     * Předá data beze změny.
     *
     * @param iterable $data
     * @return ViewInterface
     */
    public function inheritViewModel(iterable $data): ViewInterface {
        return $this->setData($data);
    }

}
