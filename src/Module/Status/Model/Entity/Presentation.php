<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Status\Model\Entity;

use Model\Entity\PersistableEntityAbstract;

use Red\Model\Entity\MenuItemInterface;
use Red\Model\Entity\StaticItemInterface;
use Status\Model\Entity\PresentationInterface;

/**
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
    #[\Override]
    public function getLastGetResourcePath(): ?string {
        return $this->lastResourcePath;
    }

    /**
     *
     * @param type $lastResourcePath
     * @return $this
     */
    #[\Override]
    public function setLastGetResourcePath(string $lastResourcePath): PresentationInterface {
        $this->lastResourcePath = $lastResourcePath;
        return $this;
    }

    ## language

    /**
     *
     * @return string|null
     */
    #[\Override]
    public function getLanguageCode(): ?string {
        return $this->languageCode;
    }

    /**
     *
     * @return string
     */
    #[\Override]
    public function getRequestedLangCode(): ?string {
        return $this->requestedLangCode;
    }

    /**
     *
     * @param string $languageCode
     * @return PresentationInterface
     */
    #[\Override]
    public function setLanguageCode(string $languageCode): PresentationInterface {
        $this->languageCode = $languageCode;
        return $this;
    }

    /**
     *
     * @param string $requestedLangCode
     * @return PresentationInterface
     */
    #[\Override]
    public function setRequestedLangCode(string $requestedLangCode): PresentationInterface {
        $this->requestedLangCode = $requestedLangCode;
        return $this;
    }

    ## menu item

    /**
     *
     * @return MenuItemInterface|null
     */
    #[\Override]
    public function getMenuItem(): ?MenuItemInterface {
        return $this->menuItem;
    }

    /**
     *
     * @param MenuItemInterface $menuItem
     * @return PresentationInterface
     */
    #[\Override]
    public function setMenuItem(MenuItemInterface $menuItem): PresentationInterface {
        $this->menuItem = $menuItem;
        return $this;
    }

    ## static item
    
    #[\Override]
    public function getStaticItem(): ?StaticItemInterface {
        return $this->staticItem;
    }
    
    #[\Override]
    public function setStaticItem(?StaticItemInterface $staticItem=null): PresentationInterface {
        $this->staticItem = $staticItem;
        return $this;
    }
    
    ## template

    /**
     *
     * @return string
     */
    #[\Override]
    public function getLastTemplateName(): ?string {
        return $this->lastTemplateName;
    }

    /**
     * 
     * @param string $templateName
     * @return PresentationInterface
     */
    #[\Override]
    public function setLastTemplateName(string $templateName): PresentationInterface {
        $this->lastTemplateName = $templateName;
        return $this;
    }

    #[\Override]
    public function setInfo($name, $value) {
        $this->info[$name] = $value;
    }
    
    #[\Override]
    public function getInfo($name) {
        return $this->info[$name] ?? null;
    }
    
    #[\Override]
    public function getInfos(): array {
        return $this->info;
    }    
}
