<?php
namespace Red\Component\View\Menu;

use Red\Component\View\ComponentCollectionAbstract;
use Pes\View\InheritDataViewInterface;
use Pes\View\ViewInterface;

/**
 * Description of ItemButtonsComponent
 *
 * ItemButtonsComponent je kontejner na button komponenty - je typu CollectionViewInterface, komponentní view se přidávají jako kolekce (iterable bez pojmenování prvků)
 *
 * @author pes2704
 */
class ItemButtonsComponent extends ComponentCollectionAbstract implements InheritDataViewInterface {

    public function inheritData(iterable $data): ViewInterface {
        $this->setData($data);
        return $this;
    }
}
