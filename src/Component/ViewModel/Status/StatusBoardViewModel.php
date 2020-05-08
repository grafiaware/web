<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\ViewModel\Status;

use StatusManager\StatusSecurityManagerInterface;
use StatusManager\StatusPresentationManagerInterface;

/**
 * Description of StatusBoardViewModel
 *
 * @author pes2704
 */
class StatusBoardViewModel {

    private $statusSecurityModel;
    private $statusPresentationModel;

    public function __construct(StatusSecurityManagerInterface $statusSecurityModel, StatusPresentationManagerInterface $statusPresentationModel) {
        $this->statusSecurityModel = $statusSecurityModel;
        $this->statusPresentationModel = $statusPresentationModel;
    }

    public function getLanguageInfo() {
        $statusPresentation = $this->statusPresentationModel->getStatusPresentation();
        return [
            'code' => $statusPresentation->getLanguage()->getLangCode(),
            'name' => $statusPresentation->getLanguage()->getName(),
            'state' => $statusPresentation->getLanguage()->getState(),
            'locale' => $statusPresentation->getLanguage()->getLocale(),
            ];
    }

    public function getEditableInfo($param) {
        $statusPresentation = $this->statusPresentationModel->getStatusPresentation();
        [
            'article' => $statusPresentation->getUserActions()->isEditableArticle(),
            'layout' => $statusPresentation->getUserActions()->isEditableLayout(),
        ];

    }

    public function getSucurityInfo($param) {
        return 'No security info.';
    }

}
