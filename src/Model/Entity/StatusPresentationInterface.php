<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Entity;

use Model\Entity\StatusPresentationInterface;

use Model\Entity\{
    MenuItem, Language, UserActions
};

/**
 *
 * @author pes2704
 */
interface StatusPresentationInterface {

    /**
     *
     * @return Language|null
     */
    public function getLanguage(): ?Language;


    /**
     * @return string
     */
    public function getRequestedLangCode();

    /**
     *
     * @return MenuItem|null
     */
    public function getMenuItem(): ?MenuItem;

    /**
     *
     * @return UserActions|null
     */
    public function getUserActions(): ?UserActions;

    /**
     *
     * @param Language $language
     * @return StatusPresentationInterface
     */
    public function setLanguage(Language $language): StatusPresentationInterface;

    /**
     *
     * @param string $requestedLangCode
     * @return StatusPresentationInterface
     */
    public function setRequestedLangCode($requestedLangCode): StatusPresentationInterface;

    /**
     *
     * @param MenuItem $menuItem
     * @return StatusPresentationInterface
     */
    public function setMenuItem(MenuItem $menuItem): StatusPresentationInterface;

    /**
     *
     * @param UserActions $userStatus
     * @return StatusPresentationInterface
     */
    public function setUserActions(UserActions $userStatus): StatusPresentationInterface;
}
