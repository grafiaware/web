<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\ViewModel\Authored;

use Model\Repository\StatusSecurityRepo;
use Model\Repository\StatusPresentationRepo;
use Model\Repository\MenuRepo;

/**
 * Description of AuthoredViewModelAbstract
 *
 * @author pes2704
 */
class AuthoredViewModelAbstract implements AuthoredViewModelInterface {

    /**
     * @var StatusSecurityRepo
     */
    protected $statusSecurityRepo;

    /**
     * @var StatusPresentationRepo
     */
    protected $statusPresentationRepo;

    /**
     * @var MenuRepo
     */
    protected $menuRepo;

    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusPresentationRepo $statusPresentationRepo,
            MenuRepo $menuRepo
            ) {
        $this->statusSecurityRepo = $statusSecurityRepo;
        $this->statusPresentationRepo = $statusPresentationRepo;
        $this->menuRepo = $menuRepo;
    }

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
        $userActions = $this->statusSecurityRepo->get()->getUserActions();
        return $userActions ? $userActions->isEditableArticle() : false;
    }
}
