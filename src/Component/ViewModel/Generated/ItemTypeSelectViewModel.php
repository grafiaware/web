<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\ViewModel\Generated;

use Component\ViewModel\StatusViewModelAbstract;

use Module\Status\Model\Repository\StatusSecurityRepo;
use Module\Status\Model\Repository\StatusPresentationRepo;
use Module\Status\Model\Repository\StatusFlashRepo;

use Model\Repository\MenuItemTypeRepo;

use Model\Entity\MenuItemTypeInterface;
use Model\Entity\MenuItemInterface;
/**
 * Description of LanguageSelect
 *
 * @author pes2704
 */
class ItemTypeSelectViewModel extends StatusViewModelAbstract implements ItemTypeSelectViewModelInterface {

    /**
     * @var MenuItemTypeRepo
     */
    private $menuItemTypeRepo;

    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusPresentationRepo $statusPresentationRepo,
            StatusFlashRepo $statusFlashRepo,
            MenuItemTypeRepo $menuItemTypeRepo
            ) {
        parent::__construct($statusSecurityRepo, $statusPresentationRepo, $statusFlashRepo);
        $this->menuItemTypeRepo = $menuItemTypeRepo;
    }

    /**
     *
     * @return MenuItemTypeInterface array of
     */
    public function getTypeTransitions() {
        $typeTransitions = [
            'root' => '',
            'empty' => ['static', 'paper'],
            'redirect' => '',
            'static' => '',
            'paper' => '',
            'trash' => '',
            'generated' => ''
        ];
        return $typeTransitions;
//        return $this->menuItemTypeRepo->findAll();
    }


    /**
     * Vrací menuItem odpovídající prezentované položce menu. Řídí se hodnotami vlastností objektu PresentationStatus.
     *
     * @return MenuItemInterface|null
     */
    public function getMenuItem(): ?MenuItemInterface {
        return $this->statusPresentationRepo->get()->getMenuItem();
    }

    public function getIterator(): \Traversable {
        return new \ArrayObject(
                [
                    'menuItem' => $this->getMenuItem()
                ]
                );
    }
}
