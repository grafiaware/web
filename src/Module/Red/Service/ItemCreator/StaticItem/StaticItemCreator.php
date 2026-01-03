<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Red\Service\ItemCreator\StaticItem;   // Static je keyword a použití Red\Service\ItemCreator\Static je syntaktická chyba

use Red\Service\ItemCreator\ItemCreatorAbstract;

use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusPresentationRepo;
use Status\Model\Repository\StatusFlashRepo;
use Red\Model\Repository\StaticItemRepo;

use Red\Model\Entity\MenuItemInterface;
use Red\Model\Entity\StaticItemClass;

/**
 * Description of StaticService
 *
 * @author pes2704
 */
class StaticItemCreator extends ItemCreatorAbstract {

    /**
     * @var StaticItemRepo
     */
    protected $staticRepo;

    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusPresentationRepo $statusPresentationRepo,
            StatusFlashRepo $statusFlashRepo,
            StaticItemRepo $staticRepo
            ) {
        parent::__construct($statusSecurityRepo, $statusPresentationRepo, $statusFlashRepo);
        $this->staticRepo = $staticRepo;
    }

    /**
     * Vytvoří novou entitu StaticClass pro zadané MenuItem. Nastaví jako creatora právě přihlášeného uživatele. 
     * Ostatní vlastnosti entity StaticClass ponechá prázdné. Entitu přidá do Repository.
     * 
     * @param MenuItemInterface $menuItem
     * @return void
     */
    public function initialize(MenuItemInterface $menuItem): void {
        $static = new StaticItemClass();
        //TODO: SV musíš doplnit komponent pro POST zadání cesty a jména template (jméno složky) - a ten zobrazovat při kliknutí na static položku (v editačním režimu)
        
        $static->setCreator($this->statusSecurityRepo->get()->getLoginAggregate()->getLoginName());
        $static->setMenuItemIdFk($menuItem->getId());
        $this->staticRepo->add($static);
    }

}
