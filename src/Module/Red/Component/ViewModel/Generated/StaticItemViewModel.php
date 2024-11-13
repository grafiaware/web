<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Component\ViewModel\Generated;

use Component\ViewModel\ViewModelAbstract;

use Component\ViewModel\StatusViewModelInterface;
use Red\Model\Repository\MenuItemApiRepo;

use Red\Model\Entity\MenuItemApiInterface;
use Red\Model\Entity\MenuItemInterface;
/**
 * Description of LanguageSelect
 *
 * @author pes2704
 */
class StaticItemViewModel extends ViewModelAbstract implements StaticItemViewModelInterface {

    /**
     * 
     * @var StatusViewModelInterface
     */
    private $status;

    /**
     * @var MenuItemApiRepo
     */
    private $menuItemTypeRepo;

    public function __construct(
            StatusViewModelInterface $status,
            MenuItemApiRepo $menuItemTypeRepo
            ) {
        $this->status = $status;
        $this->menuItemTypeRepo = $menuItemTypeRepo;
    }

    /**
     * Vrací menuItem odpovídající prezentované položce menu. Řídí se hodnotami vlastností objektu PresentationStatus.
     *
     * @return MenuItemInterface|null
     */
    public function getMenuItem(): ?MenuItemInterface {
        return $this->status->getPresentedMenuItem();
    }

    public function getIterator(): \Traversable {
        $this->appendData(
                [
                    'menuItem' => $this->getMenuItem()
                ]
                );
        return parent::getIterator();
    }
}
