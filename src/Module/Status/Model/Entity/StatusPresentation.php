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

    ### resource path

    /**
     *
     * @return string
     */
    public function getLastGetResourcePath() {
        return $this->lastResourcePath;
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

    ## language

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

    ## menu item

    /**
     *
     * @return MenuItemInterface|null
     */
    public function getMenuItem(): ?MenuItemInterface {
        return $this->menuItem;
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

    ## template

    /**
     *
     * @return string
     */
    public function getLastTemplateName() {
        return $this->lastTemplateName;
    }

    public function setLastTemplateName($templateName): StatusPresentationInterface {
        $this->lastTemplateName = $templateName;
        return $this;
    }


    ## user action

    /**
     *
     * @return UserActionsInterface|null
     */
    public function getUserActions(): ?UserActionsInterface {
        return $this->userActions;
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

    ## item action

    /**
     * Vrací item action pro zadaný typ a id obsahu nebo null.
     *
     * @param type $contentType
     * @param type $contentId
     * @return ItemActionInterface|null
     */
    public function getItemAction($contentType, $contentId): ?ItemActionInterface {
        $key = $contentType.$contentId;
        return array_key_exists($key, $this->itemActions) ? $this->itemActions[$key] : null;
    }

    /**
     * Přidá item action do vnitřní kolekce pro typ a id obsahu.
     * @param ItemActionInterface $itemAction
     * @return StatusPresentationInterface
     */
    public function addItemAction(ItemActionInterface $itemAction): StatusPresentationInterface {
        $this->itemActions[$itemAction->getTypeFk().$itemAction->getContentId()] = $itemAction;
        return $this;
    }

    public function removeItemAction(ItemActionInterface $itemAction): StatusPresentationInterface {
        unset($this->itemActions[$itemAction->getCreated().$itemAction->getContentId()]);
        return $this;
    }
}
