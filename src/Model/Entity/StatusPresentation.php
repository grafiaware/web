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
 * Description of StatusHierarchy
 *
 * @author pes2704
 */
class StatusPresentation implements StatusPresentationInterface {

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
     * @return MenuItemInterface|null
     */
    public function getMenuItem(): ?MenuItemInterface {
        return $this->menuItem;
    }

    /**
     *
     * @return string
     */
    public function getLastResourcePath() {
        return $this->lastResourcePath;
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

    /**
     *
     * @param MenuItemInterface $menuItem
     * @return StatusPresentationInterface
     */
    public function setMenuItem(MenuItemInterface $menuItem): StatusPresentationInterface {
        $this->menuItem = $menuItem;
        return $this;
    }

    /**
     *
     * @param type $lastResourcePath
     * @return $this
     */
    public function setLastResourcePath($lastResourcePath): StatusPresentationInterface {
        $this->lastResourcePath = $lastResourcePath;
        return $this;
    }

}
