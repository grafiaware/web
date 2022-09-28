<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Service\ItemCreator\Article;

use Red\Service\ItemCreator\ItemCreatorAbstract;

use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusPresentationRepo;
use Status\Model\Repository\StatusFlashRepo;
use Red\Model\Repository\ArticleRepo;

use Red\Model\Entity\Article;

/**
 * Description of PaperService
 *
 * @author pes2704
 */
class ArticleCreator extends ItemCreatorAbstract {

    /**
     * @var ArticleRepo
     */
    protected $articleRepo;

    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusPresentationRepo $statusPresentationRepo,
            StatusFlashRepo $statusFlashRepo,
            ArticleRepo $articleRepo
            ) {
        parent::__construct($statusSecurityRepo, $statusPresentationRepo, $statusFlashRepo);
        $this->articleRepo = $articleRepo;
    }

    /**
     * Vytvoří nový Article pro zadané menu item id. Nastaví jako editora právě přihlášeného uživatele. Ostatní vlastnosti entity Article ponechá prázdné. Entitu přidá do Repository.
     *
     * @param type $menuItemIdFk
     * @return void
     */
    public function initialize($menuItemIdFk): void {
        $article = new Article();
        $article->setEditor($this->statusSecurityRepo->get()->getLoginAggregate()->getLoginName());
        $article->setMenuItemIdFk($menuItemIdFk);
        $this->articleRepo->add($article);
    }


}
