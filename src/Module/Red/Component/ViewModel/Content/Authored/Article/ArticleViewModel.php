<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Component\ViewModel\Content\Authored\Article;

use Red\Component\ViewModel\Content\Authored\AuthoredViewModelAbstract;

use Component\ViewModel\StatusViewModelInterface;
use Red\Model\Repository\MenuItemRepoInterface;
use Red\Model\Repository\ItemActionRepoInterface;

use Red\Model\Entity\ArticleInterface;
use Red\Model\Repository\ArticleRepo;

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

    /**
     *
     * @var ArticleInterface
     */
    private $article;

    public function __construct(
            StatusViewModelInterface $status,
            MenuItemRepoInterface $menuItemRepo,
            ItemActionRepoInterface $itemActionRepo,
            ArticleRepo $articleRepo
            ) {
        parent::__construct($status, $menuItemRepo, $itemActionRepo);
        $this->articleRepo = $articleRepo;
    }

    /**
     * Vrací typ položky. Používá hodnoty z AuthoredTypeEnum.
     *
     * @return string
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
        if (!isset($this->article)) {
            if (isset($this->menuItemId)) {
                $this->article = $this->articleRepo->getByMenuItemId($this->menuItemId);
            }
        }
        return $this->article ?? null;
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