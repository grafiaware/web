<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\ViewModel\Authored\Article;

use ArrayObject;
use Component\ViewModel\Authored\AuthoredViewModelAbstract;
use Red\Model\Entity\ArticleInterface;
use Red\Model\Repository\ArticleRepo;
use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusPresentationRepo;
use Status\Model\Repository\StatusFlashRepo;
use Red\Model\Repository\MenuItemRepoInterface;
use Red\Model\Repository\ItemActionRepo;

/**
 * Description of PaperViewModelAnstract
 *
 * @author pes2704
 */
class ArticleViewModel extends AuthoredViewModelAbstract implements ArticleViewModelInterface {

    /**
     * @var ArticleRepo
     */
    protected $articleRepo;

    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusPresentationRepo $statusPresentationRepo,
            StatusFlashRepo $statusFlashRepo,
            MenuItemRepoInterface $menuItemRepo,
            ItemActionRepo $itemActionRepo,
            ArticleRepo $articleRepo
            ) {
        parent::__construct($statusSecurityRepo, $statusPresentationRepo, $statusFlashRepo, $menuItemRepo, $itemActionRepo);
        $this->articleRepo = $articleRepo;
    }

    /**
     * {@inheritdoc}
     *
     * MenuItem musí být aktivní nebo prezentace musí být v režimu article editable - jinak repository nevrací menuItem a nevznikne Article, metoda vrací null.
     *
     * @return ArticleInterface|null
     */
    public function getArticle(): ?ArticleInterface {
        if (isset($this->menuItemIdCached)) {
            $article = $this->articleRepo->getByReference($this->menuItemIdCached);
        }
        return $article ?? null;
    }

    public function getIterator() {
        $this->appendData(['article'=> $this->getArticle(), 'isEditable'=> $this->presentEditableContent()]);
        return parent::getIterator();
    }


}