<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Service\ItemCreator\Paper;

use Red\Service\ItemCreator\ItemCreatorAbstract;

use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusPresentationRepo;
use Status\Model\Repository\StatusFlashRepo;
use Red\Model\Repository\PaperRepo;

use Red\Model\Entity\Paper;

/**
 * Description of PaperService
 *
 * @author pes2704
 */
class PaperCreator extends ItemCreatorAbstract {

    /**
     * @var PaperRepo
     */
    protected $paperRepo;

    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusPresentationRepo $statusPresentationRepo,
            StatusFlashRepo $statusFlashRepo,
            PaperRepo $paperRepo
            ) {
        parent::__construct($statusSecurityRepo, $statusPresentationRepo, $statusFlashRepo);
        $this->paperRepo = $paperRepo;
    }

    /**
     * Vytvoří nový Paper pro zadané menu item id. Nastaví jako editora právě přihlášeného uživatele. Ostatní vlastnosti entity Paper ponechá prázdné. Entitu přidá do Repository.
     *
     * @param type $menuItemIdFk
     * @return void
     */
    public function initialize($menuItemIdFk): void {
        $paper = new Paper();
        $paper->setEditor($this->statusSecurityRepo->get()->getLoginAggregate()->getLoginName());
        $paper->setMenuItemIdFk($menuItemIdFk);
        $this->paperRepo->add($paper);
    }
}
