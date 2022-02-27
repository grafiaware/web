<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\View;

use Access\AccessInterface;
use Component\View\AccessComponentInterface;
use Configuration\ComponentConfigurationInterface;
use Component\ViewModel\StatusViewModelInterface;

use Access\Enum\RoleEnum;
use Access\Enum\AllowedViewEnum;

/**
 * Description of AclComponentAbstract
 *
 * @author pes2704
 */
abstract class StatusComponentAbstract extends ComponentAbstract implements AccessComponentInterface {

    /**
     * @var StatusViewModelInterface
     */
    protected $contextData;

    public function __construct(ComponentConfigurationInterface $configuration, StatusViewModelInterface $contextData) {
        $this->contextData = $contextData;
        parent::__construct($configuration);
    }

    public function isAllowed($action): bool {
        $isAllowed = false;
        $role = $this->contextData->getUserRole();
        $logged = $this->contextData->isUserLoggedIn();
        $permissions = $this->getComponentPermissions();
        $activeRole = $this->getActiveRole($logged, $role, $permissions);
        if (isset($activeRole)) {
            if (array_key_exists($activeRole, $permissions) AND array_key_exists($action, $permissions[$activeRole])) {
                $resource = $permissions[$activeRole][$action];
                $isAllowed = ($this instanceof $resource) ? true : false;
            } else {
                $isAllowed =false;
            }
        }
//            $componentClass = get_class($component);
//            $m = $logged ? "Uživatel je přihlášen s rolí '$role'." : "Uživatel není přihlášen.";
//            $message = "Neznámá oprávnění pro komponentu '$componentClass' a přidělenou aktivní roli uživatele '$activeRole'. $m";

        return $isAllowed;
    }

    /**
     * Pokud uživatel má nastavenu roli a tato role je definována v permissions - vrací roli uživatele.
     * Pokud uživatek nemá nastavenu roli nebo jeho role není uvedena v permissions, zjistí jestli je v permission nastavena oprávnění pro roli 'everyone!, pokud ano, vrací roli 'everyone'.
     *
     * @param type $role
     * @param type $permissions
     * @return type
     */
    private function getActiveRole($logged, $role, $permissions) {
        if (isset($role) AND array_key_exists($role, $permissions)){
            $ret = $role;
        } elseif($logged){
            $ret = RoleEnum::EVERYONE;
        } else {
            $ret = RoleEnum::ANONYMOUS;
        }
        return $ret;
    }

    public function getComponentPermissions(): array {
        return [
            RoleEnum::SUP => [AllowedViewEnum::DISPLAY => \Component\View\StatusComponentAbstract::class, AllowedViewEnum::EDIT => \Component\View\StatusComponentAbstract::class],
            RoleEnum::EDITOR => [AllowedViewEnum::DISPLAY => \Component\View\StatusComponentAbstract::class, AllowedViewEnum::EDIT => \Component\View\StatusComponentAbstract::class],
            RoleEnum::EVERYONE => [AllowedViewEnum::DISPLAY => \Component\View\StatusComponentAbstract::class],
            RoleEnum::ANONYMOUS => [AllowedViewEnum::DISPLAY => \Component\View\StatusComponentAbstract::class]
        ];
    }

}
