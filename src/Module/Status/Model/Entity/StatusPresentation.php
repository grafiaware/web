<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Status\Model\Entity;

use Model\Entity\PersistableEntityAbstract;

use Status\Model\Entity\StatusPresentationInterface;

use Red\Model\Entity\MenuItemInterface;
use Red\Model\Entity\LanguageInterface;
use Red\Model\Entity\ItemActionInterface;

/**
 * Description of StatusHierarchy
 *
 * @author pes2704
 */
class StatusPresentation extends PersistableEntityAbstract implements StatusPresentationInterface {

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
