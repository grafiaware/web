<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\ViewModel\Status;

use Component\ViewModel\StatusViewModelAbstract;

/**
 * Description of StatusBoardViewModel
 *
 * @author pes2704
 */
class StatusBoardViewModel extends StatusViewModelAbstract implements StatusBoardViewModelInterface {

    public function getLanguageInfo() {
        $language = $this->statusPresentationRepo->get()->getLanguage();
        return [
            'code' => $language->getLangCode(),
            'name' => $language->getName(),
            'state' => $language->getState(),
            'locale' => $language->getLocale(),
            ];
    }

    public function getEditableInfo() {
        $userActions = $this->statusSecurityRepo->get()->getUserActions();
        return [
            'article' => $userActions->isEditableArticle(),
            'layout' => $userActions->isEditableLayout(),
            ];
    }

    public function getSecurityInfo() {
        return [
            'userName' => $this->statusSecurityRepo->get()->getCredentials()->getLoginName(),
            'role' => $this->statusSecurityRepo->get()->getCredentials()->getRole(),
            ];
    }

    public function getIterator(): \Traversable {
        return new \ArrayObject(
                [
                'languageInfo' => $this->getLanguageInfo(),
                'editableInfo' => $this->getEditableInfo(),
                'securityInfo' => $this->getSecurityInfo()
                ]
            );
    }
}
