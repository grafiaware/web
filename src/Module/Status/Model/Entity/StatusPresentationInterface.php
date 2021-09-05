<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Status\Model\Entity;

use Model\Entity\EntitySingletonInterface;

use Red\Model\Entity\MenuItemInterface;
use Red\Model\Entity\LanguageInterface;
use Red\Model\Entity\ItemActionInterface;
use Red\Model\Entity\UserActionsInterface;

use Status\Model\Entity\StatusPresentationInterface;

/**
 *
 * @author pes2704
 */
interface StatusPresentationInterface extends EntitySingletonInterface {

    /**
     *
     * @return LanguageInterface|null
     */
    public function getLanguage(): ?LanguageInterface;

    /**
     * @return string
     */
    public function getRequestedLangCode();

    /**
     *
     * @return MenuItemInterface|null
     */
    public function getMenuItem(): ?MenuItemInterface;

    /**
     *
     * @return string
     */
    public function getLastGetResourcePath();

    public function getLastTemplateName();

    /**
     *
     * @return array ItemActionInterface array of
     */
    public function getItemActions();

    /**
     *
     * @return UserActionsInterface|null
     */
    public function getUserActions(): ?UserActionsInterface;

    /**
     *
     * @param LanguageInterface $language
     * @return StatusPresentationInterface
     */
    public function setLanguage(LanguageInterface $language): StatusPresentationInterface;

    /**
     *
     * @param string $requestedLangCode
     * @return StatusPresentationInterface
     */
    public function setRequestedLangCode($requestedLangCode): StatusPresentationInterface;

    /**
     *
     * @param MenuItemInterface $menuItem
     * @return StatusPresentationInterface
     */
    public function setMenuItem(MenuItemInterface $menuItem): StatusPresentationInterface;

    /**
     *
     * @param UserActionsInterface $userActions
     * @return StatusPresentationInterface
     */
    public function setUserActions(UserActionsInterface $userActions): StatusPresentationInterface;

    /**
     *
     * @param type $lastResourcePath
     * @return $this
     */
    public function setLastGetResourcePath($lastResourcePath): StatusPresentationInterface;

    public function setLastTemplateName($templateName): StatusPresentationInterface;

    public function addItemAction(ItemActionInterface $itemAction): StatusPresentationInterface;

    public function removeItemAction(ItemActionInterface $itemAction): StatusPresentationInterface;

}
