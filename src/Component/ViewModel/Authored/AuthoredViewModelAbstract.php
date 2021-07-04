<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\ViewModel\Authored;

use Component\ViewModel\StatusViewModelAbstract;

/**
 * Description of AuthoredViewModelAbstract
 *
 * @author pes2704
 */
abstract class AuthoredViewModelAbstract extends StatusViewModelAbstract implements AuthoredViewModelInterface {

    protected $menuItemId;

    public function setItemId($menuItemId) {
        $this->menuItemId = $menuItemId;
    }

    /**
     * {@inheritdoc}
     *
     * Editovat smí uživatel s rolí 'sup'
     *
     * @return bool
     */
    public function userCanEdit(): bool {
        $loginAggregate = $this->statusSecurityRepo->get()->getLoginAggregate();
        if ($loginAggregate) {
            $presentEditableArticle = $this->statusSecurityRepo->get()->getUserActions()->presentEditableArticle();
            $userIsSupervisor = $loginAggregate->getCredentials()->getRole() == 'sup';
            return ($presentEditableArticle AND $userIsSupervisor);
        } else {
            return false;
        }
    }
}
