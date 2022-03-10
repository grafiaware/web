<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\View;

use Component\View\AccessComponentInterface;
use Configuration\ComponentConfigurationInterface;
use Component\ViewModel\StatusViewModelInterface;

use Access\AccessPresentationInterface;
use Access\Enum\RoleEnum;
use Access\Enum\AccessPresentationEnum;

/**
 * Description of AclComponentAbstract
 *
 * @author pes2704
 */
abstract class StatusComponentAbstract extends ComponentAbstract implements StatusComponentInterface, AccessComponentInterface {

    /**
     * @var StatusViewModelInterface
     */
    protected $statusViewModel;

    /**
     * @var AccessPresentationInterface
     */
    protected $accessView;

    public function __construct(ComponentConfigurationInterface $configuration, StatusViewModelInterface $statusViewModel, AccessPresentationInterface $accessView) {
        $this->statusViewModel = $statusViewModel;
        $this->accessView = $accessView;
        parent::__construct($configuration);
    }

    public function getStatus(): StatusViewModelInterface {
        return $this->statusViewModel;
    }

    public function isAllowedToPresent($action): bool {
        return $this->accessView->isAllowed($this, $this->getComponentPermissions(), $action);
    }

    public function getComponentPermissions(): array {
        return [
            RoleEnum::SUP => [AccessPresentationEnum::DISPLAY => \Component\View\StatusComponentAbstract::class, AccessPresentationEnum::EDIT => \Component\View\StatusComponentAbstract::class],
            RoleEnum::EDITOR => [AccessPresentationEnum::DISPLAY => \Component\View\StatusComponentAbstract::class, AccessPresentationEnum::EDIT => \Component\View\StatusComponentAbstract::class],
            RoleEnum::AUTHENTICATED => [AccessPresentationEnum::DISPLAY => \Component\View\StatusComponentAbstract::class],
            RoleEnum::ANONYMOUS => [AccessPresentationEnum::DISPLAY => \Component\View\StatusComponentAbstract::class]
        ];
    }

}
