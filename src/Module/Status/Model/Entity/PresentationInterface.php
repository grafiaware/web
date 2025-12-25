<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Status\Model\Entity;

use Model\Entity\PersistableEntityInterface;

use Red\Model\Entity\MenuItemInterface;
use Red\Model\Entity\StaticItemInterface;

use Red\Model\Entity\LanguageInterface;
use Red\Model\Entity\ItemActionInterface;

use Status\Model\Entity\PresentationInterface;

/**
 *
 * @author pes2704
 */
interface PresentationInterface extends PersistableEntityInterface {
    
    /**
     *
     * @param type $lastResourcePath
     * @return $this
     */
    public function setLastGetResourcePath($lastResourcePath): PresentationInterface;

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
     * @return PresentationInterface
     */
    public function setLanguageCode($languageCode): PresentationInterface;

    /**
     *
     * @param string $requestedLangCode
     * @return PresentationInterface
     */
    public function setRequestedLangCode($requestedLangCode): PresentationInterface;

    /**
     *
     * @return MenuItemInterface|null
     */
    public function getMenuItem(): ?MenuItemInterface;

    /**
     *
     * @param MenuItemInterface $menuItem
     * @return PresentationInterface
     */
    public function setMenuItem(MenuItemInterface $menuItem): PresentationInterface;
    
    /**
     * 
     * @return StaticItemInterface
     */
    public function getStaticItem(): StaticItemInterface;
    
    /**
     * 
     * @param StaticItemInterface $staticItem
     */
    public function setStaticItem(StaticItemInterface $staticItem=null): PresentationInterface;
    
    /**
     * Jméno poslední template zobrazené v náhledu template
     */
    public function getLastTemplateName();

    /**
     * Jméno poslední template zobrazené v náhledu template
     *
     * @param type $templateName
     * @return PresentationInterface
     */
    public function setLastTemplateName($templateName): PresentationInterface;

    
    public function setInfo($name, $value);
    
    public function getInfo($name);

    public function getInfos(): array;      

}
