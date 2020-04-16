<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Entity;

/**
 * Description of StatusPresentation
 *
 * @author pes2704
 */
class UserActions implements UserActionsInterface {

    private $editLayout = FALSE;
    private $editPaper = FALSE;

    /**
     * Informuje, zda prezentace je přepnuta do modu editace layoutu.
     * @return bool
     */
    public function isEditableLayout() {
        return $this->editLayout;
    }

    /**
     * Informuje, zda prezentace je přepnuta do modu editace článku.
     * @return bool
     */
    public function isEditableArticle() {
        return $this->editPaper;
    }

    /**
     * Nastaví informaci, že prentace je přepnuta do modu editace layoutu.
     *
     * @param mixed $editLayout Metoda převede zadanou hodnotu na boolen hodnotu.
     * @return UserActionsInterface
     */
    public function setEditableLayout($editLayout): UserActionsInterface {
        $this->editLayout = boolval($editLayout);
        return $this;
    }

    /**
     * Nastaví informaci, že prentace je přepnuta do modu editace článku
     *
     * @param mixed $editPaper Metoda převede zadanou hodnotu na boolen hodnotu.
     * @return UserActionsInterface
     */
    public function setEditableArticle($editPaper): UserActionsInterface {
        $this->editPaper = boolval($editPaper);
        return $this;
    }
}
