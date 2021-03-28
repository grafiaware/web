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
class UserActions extends EntityAbstract implements UserActionsInterface {

    private $editLayout = false;
    private $editPaper = false;
    private $editMenu = false;

    /**
     * Informuje, zda prezentace je přepnuta do modu editace layoutu.
     *
     * @return bool
     */
    public function isEditableLayout(): bool {
        return $this->editLayout;
    }

    /**
     * Informuje, zda prezentace je přepnuta do modu editace článku.
     *
     * @return bool
     */
    public function isEditableArticle(): bool {
        return $this->editPaper;
    }

    /**
     * Informuje, zda prezentace je přepnuta do modu editace menu.
     * 
     * @return bool
     */
    public function isEditableMenu(): bool {
        return $this->editMenu;
    }

    /**
     * Nastaví informaci, že prezentace je přepnuta do modu editace layoutu.
     *
     * @param mixed $editLayout Metoda převede zadanou hodnotu na boolen hodnotu.
     * @return UserActionsInterface
     */
    public function setEditableLayout($editLayout): UserActionsInterface {
        $this->editLayout = boolval($editLayout);
        return $this;
    }

    /**
     * Nastaví informaci, že prezentace je přepnuta do modu editace článku
     *
     * @param mixed $editPaper Metoda převede zadanou hodnotu na boolen hodnotu.
     * @return UserActionsInterface
     */
    public function setEditableArticle($editPaper): UserActionsInterface {
        $this->editPaper = boolval($editPaper);
        return $this;
    }

    /**
     * Nastaví informaci, že pretentace je přepnuta do modu editace menu
     *
     * @param mixed $editableMenu Metoda převede zadanou hodnotu na boolen hodnotu.
     * @return UserActionsInterface
     */
    public function setEditableMenu($editableMenu): UserActionsInterface {
        $this->editMenu = boolval($editableMenu);
        return $this;
    }


}
