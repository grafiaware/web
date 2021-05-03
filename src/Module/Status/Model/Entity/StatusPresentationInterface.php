<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Module\Status\Model\Entity;

use Model\Entity\EntitySingletonInterface;
use Model\Entity\{
    MenuItemInterface, LanguageInterface
};

use Module\Status\Model\Entity\StatusPresentationInterface;



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
     * @param type $lastResourcePath
     * @return $this
     */
    public function setLastGetResourcePath($lastResourcePath): StatusPresentationInterface;
}
