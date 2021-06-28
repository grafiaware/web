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
     *
     * @return bool
     */
    public function presentOnlyPublished() {
        return ! $this->isArticleEditable();  //negace
    }

    /**
     * {@inheritdoc}
     *
     * Editovat smí uživatel s rolí 'sup'
     *
     * @return bool
     */
    public function isEditableByUser(): bool {
        $loginAggregate = $this->statusSecurityRepo->get()->getLoginAggregate();
        if ($loginAggregate) {
            $isEditableArticle = $this->statusSecurityRepo->get()->getUserActions()->isEditableArticle();
            $isSupervisor = $loginAggregate->getCredentials()->getRole() == 'sup';
            return ($isEditableArticle AND $isSupervisor);
        } else {
            return false;
        }
    }
}
