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
use Red\Model\Repository\ItemActionRepo;
use Red\Model\Repository\MenuItemRepoInterface;

use Component\ViewModel\StatusViewModel;
use Red\Model\Entity\ItemActionInterface;
use Red\Model\Entity\MenuItemInterface;

/**
 * Description of AuthoredViewModelAbstract
 *
 * @author pes2704
 */
abstract class AuthoredViewModelAbstract extends StatusViewModel implements AuthoredViewModelInterface {

    protected $menuItemId;
    protected $menuItemType;

    /**
     *
     * @var MenuItemRepoInterface
     */
    protected $menuItemRepo;


    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusPresentationRepo $statusPresentationRepo,
            StatusFlashRepo $statusFlashRepo,
            ItemActionRepo $itemActionRepo,
            MenuItemRepoInterface $menuItemRepo
            ) {
        parent::__construct($statusSecurityRepo, $statusPresentationRepo, $statusFlashRepo, $itemActionRepo);
        $this->menuItemRepo = $menuItemRepo;
    }

    abstract public function getItemType();

    abstract public function getAuthoredTemplateName();

    abstract public function getAuthoredContentId();

    public function getItemId() {
        if (!isset($this->menuItemId)) {
            throw new LogicException("Nebyla nastavena hodnota menu item id. Hodnotu je nutné nastavit voláním metody setItemId().");
        }
        return $this->menuItemId;
    }
    /**
     * Nastaví id položky MenuItem, podle kterého bude načítáná příslušná entita s obsahem (např. Paper, Article, Multipage) a ItemAction
     * Obvykle je metoda volána z metody Front kontroleru.
     *
     * @param type $menuItemId
     * @throws LogicException
     */
    public function setItemId($menuItemId) {
        $this->menuItemId = $menuItemId;
    }

    public function getMenuItem(): MenuItemInterface {
        return $this->menuItemRepo->getById($this->getItemId());
    }

    /**
     * Info pro zobrazení stavu menuItem v paper nebo article rendereru
     *
     * @return bool
     */
    public function isMenuItemActive(): bool {
        return ($this->getMenuItem() AND $this->getMenuItem()->getActive()) ? true : false;
    }

    /**
     * {@inheritdoc}
     *
     * Informuje zda prezentace je v editovatelném režimu a současně prezentovaný obsah je editovatelný přihlášeným uživatelem.
     * Editovat smí uživatel s rolí 'sup'
     *
     * @return bool
     */
    public function presentEditableContent(): bool {
        return $this->statusPresentationRepo->get()->getUserActions()->presentEditableContent();
    }

    public function getItemAction(): ?ItemActionInterface {
        return $this->itemActionRepo->get($this->getItemType(), $this->getItemId());
    }

    public function userPerformActionWithItem(): bool {
        $itemAction = $this->getItemAction();
        $loginAgg = $this->statusSecurityRepo->get()->getLoginAggregate();
        return isset($itemAction) AND isset($loginAgg) AND $itemAction->getEditorLoginName()==$loginAgg->getLoginName();
    }
}
