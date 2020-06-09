<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\ViewModel\Authored\Paper;

use Model\Repository\StatusPresentationRepo;
use Model\Repository\StatusSecurityRepo;
use Model\Repository\HierarchyNodeRepo;
use Model\Repository\MenuItemPaperAggregateRepo;

use Component\ViewModel\Authored\AuthoredViewModelAbstract;

/**
 * Description of PaperViewModelAnstract
 *
 * @author pes2704
 */
abstract class PaperViewModelAbstract extends AuthoredViewModelAbstract implements PaperViewModelInterface {

    /**
     * @var MenuItemPaperAggregateRepo
     */
    protected $paperAggregateRepo;

    /**
     *
     * @var StatusPresentationRepo
     */
    protected $statusPresentationRepo;


    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusPresentationRepo $statusPresentationRepo,
            HierarchyNodeRepo $menuRepo,
            MenuItemPaperAggregateRepo $paperAggregateRepo
            ) {
        parent::__construct($statusSecurityRepo, $statusPresentationRepo, $menuRepo);
        $this->paperAggregateRepo = $paperAggregateRepo;
    }

}
