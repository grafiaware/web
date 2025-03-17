<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Status\Model\Entity;

use Model\Entity\PersistableEntityInterface;

use Red\Model\Entity\MenuItemInterface;
use Red\Model\Entity\LanguageInterface;
use Red\Model\Entity\ItemActionInterface;

use Status\Model\Entity\StatusPresentationInterface;

/**
 *
 * @author pes2704
 */
interface StatusPresentationInterface extends PersistableEntityInterface {
    
    /**
     *
     * @param type $lastResourcePath
     * @return $this
     */
    public function setLastGetResourcePath($lastResourcePath): StatusPresentationInterface;

    /**
     *
     * @return string
     */
    public function getLastGetResourcePath();

    /**
     *
     * @return string|null
     */
    public function getLanguageCode(): ?string;

    /**
     * @return string
     */
    public function getRequestedLangCode();
    
    /**
     *
     * @param string $languageCode
     * @return StatusPresentationInterface
     */
    public function setLanguageCode($languageCode): StatusPresentationInterface;

    /**
     *
     * @param string $requestedLangCode
     * @return StatusPresentationInterface
     */
    public function setRequestedLangCode($requestedLangCode): StatusPresentationInterface;

    /**
     *
     * @return MenuItemInterface|null
     */
    public function getMenuItem(): ?MenuItemInterface;

    /**
     *
     * @param MenuItemInterface $menuItem
     * @return StatusPresentationInterface
     */
    public function setMenuItem(MenuItemInterface $menuItem): StatusPresentationInterface;

    /**
     * Jméno poslední template zobrazené v náhledu template
     */
    public function getLastTemplateName();

    /**
     * Jméno poslední template zobrazené v náhledu template
     *
     * @param type $templateName
     * @return StatusPresentationInterface
     */
    public function setLastTemplateName($templateName): StatusPresentationInterface;

    
    public function setInfo($name, $value);
    
    public function getInfo($name);

    public function getInfos(): array;      

}
