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
use Red\Model\Repository\ItemActionRepo;

use Red\Model\Entity\MenuItemInterface;

/**
 * Description of AuthoredViewModelAbstract
 *
 * @author pes2704
 */
abstract class AuthoredViewModelAbstract extends StatusViewModel implements AuthoredViewModelInterface {

    protected $menuItemIdCached;

    protected $menuItemCached;

    /**
     * @var PaperAggregateRepo
     */
    protected $menuItemRepo;

    private $itemActionRepo;

    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusPresentationRepo $statusPresentationRepo,
            StatusFlashRepo $statusFlashRepo,
            MenuItemRepoInterface $menuItemRepo,
            ItemActionRepo $itemActionRepo
            ) {
        parent::__construct($statusSecurityRepo, $statusPresentationRepo, $statusFlashRepo);
        $this->menuItemRepo = $menuItemRepo;
        $this->itemActionRepo = $itemActionRepo;
    }

    public function setItemId($menuItemId) {
        if (isset($this->menuItemIdCached)) {
            throw new LogicException("Menu item id je již nastaveno na hodnotu {$this->menuItemIdCached}. Nelze nastavovat menu item id opakovaně.");
        }
        $this->menuItemIdCached = $menuItemId;
    }

    /**
     * Info pro zobrazení stavu menuItem v paper nebo article rendereru
     *
     * @return bool
     */
    public function isMenuItemActive(): bool {
        return ($this->getMenuItemCached() AND $this->getMenuItemCached()->getActive()) ? true : false;
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
        return $this->statusPresentationRepo->get()->getUserActions()->presentEditableArticle();
    }

    private function getMenuItemCached(): ?MenuItemInterface {
       if ( !isset($this->menuItemCached) AND isset($this->menuItemIdCached)) {
           $this->menuItemCached = $this->menuItemRepo->getById($this->menuItemIdCached);
       }
       return $this->menuItemCached;
    }
}
