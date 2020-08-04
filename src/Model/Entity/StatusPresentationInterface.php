<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Entity;

use Model\Entity\StatusPresentationInterface;

use Model\Entity\{
    MenuItemInterface, LanguageInterface
};

/**
 *
 * @author pes2704
 */
interface StatusPresentationInterface {

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
}
