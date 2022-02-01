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
use Red\Model\Repository\ItemActionRepo;
use Red\Model\Repository\MenuItemRepoInterface;

use TemplateService\TemplateSeekerInterface;

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
            StatusSecurityRepo $statusSecurityRepo,
            StatusPresentationRepo $statusPresentationRepo,
            StatusFlashRepo $statusFlashRepo,
            ItemActionRepo $itemActionRepo,
            MenuItemRepoInterface $menuItemRepo,
            TemplateSeekerInterface $templateSeeker,
            ArticleRepo $articleRepo
            ) {
        parent::__construct($statusSecurityRepo, $statusPresentationRepo, $statusFlashRepo, $itemActionRepo, $menuItemRepo, $templateSeeker);
        $this->articleRepo = $articleRepo;
    }

    /**
     * Vrací typ položky. Používá AuthoredEnum.
     * Obvykle je metoda volána z metody Front kontroleru.
     *
     * @param type $menuItemType
     */
    public function getItemType() {
        return AuthoredTypeEnum::ARTICLE;
    }

    public function getAuthoredTemplateName() {
        return $this->getArticle()->getTemplate();
    }

    public function getAuthoredContentId() {
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

    public function getIterator() {
        $this->appendData(['article'=> $this->getArticle(), 'isEditable'=> $this->presentEditableContent()]);
        return parent::getIterator();
    }


}