<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Entity;

/**
 * Description of StatusHierarchy
 *
 * @author pes2704
 */
class StatusPresentation implements StatusPresentationInterface {

    private $language;

    private $requestedLangCode;

    private $itemUid = '';

    private $userActions;

    /**
     *
     * @return \Model\Entity\Language
     */
    public function getLanguage(): ?Language {
        return $this->language;
    }

    public function setLanguage(Language $language): StatusPresentationInterface {
        $this->language = $language;
        return $this;
    }

    public function getRequestedLangCode() {
        return $this->requestedLangCode;
    }

    public function setRequestedLangCode($requestedLangCode) {
        $this->requestedLangCode = $requestedLangCode;
        return $this;
    }

    public function getItemUid() {
        return $this->itemUid;
    }

    public function setItemUid($uid): StatusPresentationInterface {
        $this->itemUid = $uid;
        return $this;
    }

    public function getUserActions(): ?UserActions {
        return $this->userActions;
    }

    public function setUserActions(UserActions $userActions) {
        $this->userActions = $userActions;
        return $this;
    }
}
