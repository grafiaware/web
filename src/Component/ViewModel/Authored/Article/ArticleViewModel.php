<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\ViewModel\Authored\Article;

use Red\Model\Entity\Article;
use Red\Model\Entity\ArticleInterface;

use Component\ViewModel\Authored\AuthoredViewModelAbstract;

use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusPresentationRepo;
use Status\Model\Repository\StatusFlashRepo;
use Red\Model\Repository\ArticleRepo;

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
            ArticleRepo $articleRepo
            ) {
        parent::__construct($statusSecurityRepo, $statusPresentationRepo, $statusFlashRepo);
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
        if (isset($this->menuItemId)) {
            $paper = $this->articleRepo->getByReference($this->menuItemId);
        }
        return $paper ?? null;
    }

    public function getContentTemplateName() {
        $article = $this->getArticle();
        return isset($article) ? $article->getTemplate() : null;
    }

    public function getContentId() {
        $article = $this->getArticle();
        return isset($article) ? $article->getId() : null;
    }

    public function getContentType() {
        return 'article';
    }

    public function getIterator() {
        return new \ArrayObject(
                        ['article'=> $this->getPaper(), 'isEditable'=> $this->isEditableByUser()]
                );  // nebo offsetSet po jedné hodnotě
    }
}