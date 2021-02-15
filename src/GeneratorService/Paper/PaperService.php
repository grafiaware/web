<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace GeneratorService\Paper;

use GeneratorService\ContentServiceAbstract;

use Model\Repository\StatusSecurityRepo;
use Model\Repository\StatusPresentationRepo;
use Model\Repository\StatusFlashRepo;
use Model\Repository\PaperRepo;

use Model\Entity\PaperInterface;
use Model\Entity\Paper;

/**
 * Description of PaperService
 *
 * @author pes2704
 */
class PaperService extends ContentServiceAbstract {

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
     * Vytvoří nový Paper pro zadané menu item id. Nastaví jako editora právě přihlášeného uživatele. Ostatní vlastnosti entity Paper ponechá prázdné.
     * 
     * @param type $menuItemIdFk
     * @return void
     */
    public function initialize($menuItemIdFk): void {
        $paper = new Paper();
        $paper->setEditor($this->statusSecurityRepo->get()->getCredential()->getLoginName());
        $paper->setMenuItemIdFk($menuItemIdFk);
        $this->paperRepo->add($paper);
    }
}
