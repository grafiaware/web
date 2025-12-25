<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Status\Model\Entity;

use Model\Entity\PersistableEntityAbstract;

use Status\Model\Entity\PresentationInterface;

use Red\Model\Entity\MenuItemInterface;
use Red\Model\Entity\StaticItemInterface;

use Red\Model\Entity\LanguageInterface;
use Red\Model\Entity\ItemActionInterface;

/**
 * Description of StatusHierarchy
 *
 * @author pes2704
 */
class Presentation extends PersistableEntityAbstract implements PresentationInterface {

    private $languageCode;

    private $requestedLangCode;

    /**
     * @var MenuItemInterface
     */
    private $menuItem;
    
    /**
     * 
     * @var StaticItemInterface
     */
    private $staticItem;

    private $lastResourcePath;

    private $lastTemplateName;
    
    private $info = [];
    
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
    public function setLastGetResourcePath($lastResourcePath): PresentationInterface {
        $this->lastResourcePath = $lastResourcePath;
        return $this;
    }

    ## language

    /**
     *
     * @return string|null
     */
    public function getLanguageCode(): ?string {
        return $this->languageCode;
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
     * @param string $languageCode
     * @return PresentationInterface
     */
    public function setLanguageCode($languageCode): PresentationInterface {
        $this->languageCode = $languageCode;
        return $this;
    }

    /**
     *
     * @param string $requestedLangCode
     * @return PresentationInterface
     */
    public function setRequestedLangCode($requestedLangCode): PresentationInterface {
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
     * @return PresentationInterface
     */
    public function setMenuItem(MenuItemInterface $menuItem): PresentationInterface {
        $this->menuItem = $menuItem;
        return $this;
    }

    ## static item
    
    public function getStaticItem(): StaticItemInterface {
        return $this->staticItem;
    }
    
    public function setStaticItem(StaticItemInterface $staticItem=null): PresentationInterface {
        $this->staticItem = $staticItem;
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

    public function setLastTemplateName($templateName): PresentationInterface {
        $this->lastTemplateName = $templateName;
        return $this;
    }

    
    public function setInfo($name, $value) {
        $this->info[$name] = $value;
    }
    public function getInfo($name) {
        return $this->info[$name] ?? null;
    }
    public function getInfos(): array {
        return $this->info;
    }    
}
