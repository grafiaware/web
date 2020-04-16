<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\ViewModel\Authored;

use StatusModel\StatusPresentationModel;

/**
 * Description of AuthoredViewModelAbstract
 *
 * @author pes2704
 */
class AuthoredViewModelAbstract extends StatusPresentationModel {

    /**
     *
     * @return bool
     */
    public function presentOnlyPublished() {
        return ! $this->userEdit();  //negace
    }

    /**
     *
     * @return bool
     */
    public function userEdit() {
        $userAction = $this->getStatusPresentation()->getUserActions();
        return $userAction ? $userAction->isEditableArticle() : false;
    }
}
