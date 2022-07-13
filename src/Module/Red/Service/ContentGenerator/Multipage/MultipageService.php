<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Service\ContentGenerator\Multipage;

use Red\Service\ContentGenerator\ContentServiceAbstract;

use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusPresentationRepo;
use Status\Model\Repository\StatusFlashRepo;
use Red\Model\Repository\MultipageRepo;

use Red\Model\Entity\Multipage;

/**
 * Description of PaperService
 *
 * @author pes2704
 */
class MultipageService extends ContentServiceAbstract {

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
     * @param type $menuItemIdFk
     * @return void
     */
    public function initialize($menuItemIdFk): void {
        $multipage = new Multipage();
        $multipage->setEditor($this->statusSecurityRepo->get()->getLoginAggregate()->getLoginName());
        $multipage->setMenuItemIdFk($menuItemIdFk);
        $this->multipageRepo->add($multipage);
    }
}
