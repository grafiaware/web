<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Service\ItemCreator\Multipage;

use Red\Service\ItemCreator\ItemCreatorAbstract;
use Red\Service\ItemCreator\ItemCreatorInterface;

use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusPresentationRepo;
use Status\Model\Repository\StatusFlashRepo;
use Red\Model\Repository\MultipageRepo;

use Red\Model\Entity\MenuItemInterface;
use Red\Model\Entity\Multipage;

/**
 * Description of PaperService
 *
 * @author pes2704
 */
class MultipageCreator extends ItemCreatorAbstract implements ItemCreatorInterface {

    /**
     * @var PaperRepo
     */
    protected $multipageRepo;

    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusPresentationRepo $statusPresentationRepo,
            StatusFlashRepo $statusFlashRepo,
            MultipageRepo $multipageRepo
            ) {
        parent::__construct($statusSecurityRepo, $statusPresentationRepo, $statusFlashRepo);
        $this->multipageRepo = $multipageRepo;
    }

    /**
     * Vytvoří nový Article pro zadané menu item id. Nastaví jako editora právě přihlášeného uživatele. Ostatní vlastnosti entity Article ponechá prázdné. Entitu přidá do Repository.
     *
     * @param MenuItemInterface $menuItem
     * @return void
     */
    public function initialize(MenuItemInterface $menuItem, ServerRequestInterface $request=null): void {
        $multipage = new Multipage();
        $multipage->setEditor($this->statusSecurityRepo->get()->getLoginAggregate()->getLoginName());
        $multipage->setMenuItemIdFk($menuItem->getId());
        $this->multipageRepo->add($multipage);
    }
}
