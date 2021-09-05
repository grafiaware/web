<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Status\Model\Entity;

use Model\Entity\EntityAbstract;

use Status\Model\Entity\StatusPresentationInterface;

use Red\Model\Entity\MenuItemInterface;
use Red\Model\Entity\LanguageInterface;
use Red\Model\Entity\ItemActionInterface;
use Red\Model\Entity\UserActionsInterface;

/**
 * Description of StatusHierarchy
 *
 * @author pes2704
 */
class StatusPresentation extends EntityAbstract implements StatusPresentationInterface {

    /**
     * @var LanguageInterface
     */
    private $language;

    private $requestedLangCode;

    /**
     * @var MenuItemInterface
     */
    private $menuItem;

    private $lastResourcePath;

    private $lastTemplateName;

    /**
     * @var ItemActionInterface array of
     */
    private $itemActions = [];

    /**
     * @var UserActionsInterface
     */
    private $userActions;

    /**
     *
     * @return LanguageInterface|null
     */
    public function getLanguage(): ?LanguageInterface {
        return $this->language;
    }

    /**
     *
     * @return string
     */
    public function getRequestedLangCode() {
        return $this->requestedLangCode;
    }

    /**
     *
     * @return MenuItemInterface|null
     */
    public function getMenuItem(): ?MenuItemInterface {
        return $this->menuItem;
    }

    /**
     *
     * @return string
     */
    public function getLastGetResourcePath() {
        return $this->lastResourcePath;
    }

    /**
     *
     * @return string
     */
    public function getLastTemplateName() {
        return $this->lastTemplateName;
    }

    /**
     *
     * @return array ItemActionInterface array of
     */
    public function getItemActions() {
        return $this->itemActions;
    }

    /**
     *
     * @return UserActionsInterface|null
     */
    public function getUserActions(): ?UserActionsInterface {
        return $this->userActions;
    }

    /**
     *
     * @param LanguageInterface $language
     * @return StatusPresentationInterface
     */
    public function setLanguage(LanguageInterface $language): StatusPresentationInterface {
        $this->language = $language;
        return $this;
    }

    /**
     *
     * @param string $requestedLangCode
     * @return StatusPresentationInterface
     */
    public function setRequestedLangCode($requestedLangCode): StatusPresentationInterface {
        $this->requestedLangCode = $requestedLangCode;
        return $this;
    }

    /**
     *
     * @param MenuItemInterface $menuItem
     * @return StatusPresentationInterface
     */
    public function setMenuItem(MenuItemInterface $menuItem): StatusPresentationInterface {
        $this->menuItem = $menuItem;
        return $this;
    }

    /**
     *
     * @param type $lastResourcePath
     * @return $this
     */
    public function setLastGetResourcePath($lastResourcePath): StatusPresentationInterface {
        $this->lastResourcePath = $lastResourcePath;
        return $this;
    }

    public function setLastTemplateName($templateName): StatusPresentationInterface {
        $this->lastTemplateName = $templateName;
    }

    /**
     *
     * @param UserActionsInterface $userActions
     * @return StatusPresentationInterface
     */
    public function setUserActions(UserActionsInterface $userActions): StatusPresentationInterface {
        $this->userActions = $userActions;
        return $this;
    }

    public function addItemAction(ItemActionInterface $itemAction): StatusPresentationInterface {
        $this->itemActions[$itemAction->getCreated().$itemAction->getItemId()] = $itemAction;
        return $this;
    }

    public function removeItemAction(ItemActionInterface $itemAction): \Status\Model\Entity\StatusPresentationInterface {
        unset($this->itemActions[$itemAction->getCreated().$itemAction->getItemId()]);
        return $this;
    }
}
