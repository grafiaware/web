<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\ViewModel\Status;

use Model\Repository\StatusSecurityRepo;
use Model\Repository\StatusPresentationRepo;

/**
 * Description of StatusBoardViewModel
 *
 * @author pes2704
 */
class StatusBoardViewModel implements StatusBoardViewModelInterface {

    private $statusSecurityRepo;
    private $statusPresentationRepo;

    public function __construct(StatusSecurityRepo $statusSecurityRepo, StatusPresentationRepo $statusPresentationRepo) {
        $this->statusSecurityRepo = $statusSecurityRepo;
        $this->statusPresentationRepo = $statusPresentationRepo;
    }

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
        [
            'article' => $userActions->isEditableArticle(),
            'layout' => $userActions->isEditableLayout(),
        ];

    }

    public function getSecurityInfo() {
        return [
            'userName' => $this->statusSecurityRepo->get()->getUser()->getUserName(),
            'role' => $this->statusSecurityRepo->get()->getUser()->getRole(),
            ];
    }

}
