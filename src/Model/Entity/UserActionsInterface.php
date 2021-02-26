<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Entity;

/**
 *
 * @author pes2704
 */
interface UserActionsInterface extends EntitySingletorInterface {

    /**
     * @return bool
     */
    public function isEditableLayout();

    /**
     * @return bool
     */
    public function isEditableArticle();

    public function setEditableLayout($editableLayout): UserActionsInterface;
    public function setEditableArticle($editablePaper): UserActionsInterface;
}
