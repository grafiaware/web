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
 * Description of StatusHierarchy
 *
 * @author pes2704
 */
class StatusPresentation implements StatusPresentationInterface {

    /**
     * @var Language
     */
    private $language;

    private $requestedLangCode;

    /**
     * @var MenuItem
     */
    private $menuItem;

    /**
     *
     * @return Language|null
     */
    public function getLanguage(): ?Language {
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
     * @return MenuItem|null
     */
    public function getMenuItem(): ?MenuItem {
        return $this->menuItem;
    }

    /**
     *
     * @param Language $language
     * @return StatusPresentationInterface
     */
    public function setLanguage(Language $language): StatusPresentationInterface {
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
     * @param MenuItem $menuItem
     * @return StatusPresentationInterface
     */
    public function setMenuItem(MenuItem $menuItem): StatusPresentationInterface {
        $this->menuItem = $menuItem;
        return $this;
    }
}
