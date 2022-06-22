<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\ViewModel\Content\Authored\Article;

use Component\ViewModel\Content\Authored\AuthoredViewModelAbstract;
use Component\ViewModel\StatusViewModelInterface;

use Red\Model\Entity\ArticleInterface;
use Red\Model\Repository\ArticleRepo;
use Red\Model\Repository\MenuItemRepoInterface;

use Red\Model\Enum\AuthoredTypeEnum;

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
            StatusViewModelInterface $status,
            MenuItemRepoInterface $menuItemRepo,
            ArticleRepo $articleRepo
            ) {
        parent::__construct($status, $menuItemRepo);
        $this->articleRepo = $articleRepo;
    }

    /**
     * Vrací typ položky. Používá AuthoredEnum.
     * Obvykle je metoda volána z metody Front kontroleru.
     *
     * @param type $menuItemType
     */
    public function getAuthoredContentType(): string {
        return AuthoredTypeEnum::ARTICLE;
    }

    public function getAuthoredTemplateName(): ?string {
        $article = $this->getArticle();
        return isset($article) ? $article->getTemplate() : null;
    }

    public function getAuthoredContentId(): string {
        return $this->getArticle()->getId();
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
            $article = $this->articleRepo->getByReference($this->menuItemId);
        }
        return $article ?? null;
    }

    /**
     * Informuje, jestli article má zobrazitelný obsah. Zobrazitelný obsah má article. jehož metoda getContent() vrací neprázdný řetězec.
     *
     * @return bool
     */
    public function hasContent(): bool {
        $article = $this->getArticle();
        return (isset($article) AND $article->getContent()) ? true : false; //content může být null
    }


}