<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\ViewModel\Authored;

use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusPresentationRepo;
use Status\Model\Repository\StatusFlashRepo;
use Component\ViewModel\StatusViewModel;
use Red\Model\Repository\MenuItemRepoInterface;

/**
 * Description of AuthoredViewModelAbstract
 *
 * @author pes2704
 */
abstract class AuthoredViewModelAbstract extends StatusViewModel implements AuthoredViewModelInterface {

    protected $menuItemId;
    /**
     * @var PaperAggregateRepo
     */
    protected $menuItemRepo;

    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusPresentationRepo $statusPresentationRepo,
            StatusFlashRepo $statusFlashRepo,
            MenuItemRepoInterface $menuItemRepo
            ) {
        parent::__construct($statusSecurityRepo, $statusPresentationRepo, $statusFlashRepo);
        $this->menuItemRepo = $menuItemRepo;
    }

    public function setItemId($menuItemId) {
        $this->menuItemId = $menuItemId;
    }

    public function isMenuItemActive(): bool {
        $langCodeFk = $this->statusPresentationRepo->get()->getLanguage()->getLangCode();
        return (isset($this->menuItemId) AND $this->menuItemRepo->get($langCodeFk, $this->menuItemId)) ? true : false;
    }

    /**
     * {@inheritdoc}
     *
     * Informuje zda prezentace je v editovatelném režimu a současně prezentovaný obsah je editovatelný přihlášeným uživatelem.
     * Editovat smí uživatel s rolí 'sup'
     *
     * @return bool
     */
    public function presentEditableArticle(): bool {
        return $this->statusSecurityRepo->get()->getUserActions()->presentEditableArticle();
    }
}
