<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Red\Service\ItemCreator\MenuItem;   // Static je keyword a použití Red\Service\ItemCreator\Static je syntaktická chyba

use Red\Service\ItemCreator\ItemCreatorAbstract;

use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusPresentationRepo;
use Status\Model\Repository\StatusFlashRepo;
use Red\Model\Repository\MenuRootRepo;

use Red\Model\Entity\MenuItemInterface;
use Red\Model\Entity\MenuRoot;
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
class MenuItemCreator extends ItemCreatorAbstract implements ItemCreatorInterface {


    /**
     * @var MenuRootRepo
     */
    protected $menuRootRepo;

    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusPresentationRepo $statusPresentationRepo,
            StatusFlashRepo $statusFlashRepo,
            MenuRootRepo $menuRootRepo
            ) {
        parent::__construct($statusSecurityRepo, $statusPresentationRepo, $statusFlashRepo);
        $this->menuRootRepo = $menuRootRepo;
    }

    /**
     * Vytvoří novou entitu MenuRoot pro zadané MenuItem. Jako cizí klíč MenuRoot uidFk nastaví MenuItem id, jako jméno MenuRoot nastaví titulek položky menu - MenuItem title. 
     * Jméno MenuRoot je použito v konfiguraci menu (ConfigurationWeb->public static function menu()) jako hodnota menu_root. Entitu přidá do Repository.
     * 
     * @param MenuItemInterface $menuItem
     * @return void
     */
    public function initialize(MenuItemInterface $menuItem): void {
        $menuRoot = new MenuRoot();
        $menuRoot->setUidFk($menuItem->getId());
        $menuRoot->setName($menuItem->getTitle());
        $this->menuRootRepo->add($menuRoot);
    }


}
