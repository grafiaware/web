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

    private $actions = ['display', 'edit'];

    public function __construct(ComponentConfigurationInterface $configuration) {
        parent::__construct($configuration);
    }

    public function isAllowed(AccessComponentInterface $component, $action): bool {
        $isAllowed = false;
        $role = $this->contextData->getUserRole();
        $permissions = $component->getComponentPermissions();
        $activeRole = $this->getActiveRole($role, $permissions);
        if (isset($activeRole)) {
            if (array_key_exists($action, $permissions[$activeRole])) {
                $resource = $permissions[$activeRole][$action];
                $isAllowed = ($component instanceof $resource) ? true : false;
            } else {
                $isAllowed =false;
            }
        } else {
            throw new UnexpectedValueException("Neznámá oprávnění pro komponentu $component a roli uživatele $role.");
        }
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
    private function getActiveRole($role, $permissions) {
        return (isset($role) AND array_key_exists($role, $permissions)) ? $role : (array_key_exists('everyone', $permissions) ? 'everyone' : null);
    }

    public function getComponentPermissions(): array {
        return [
            'sup' => ['display' => \Component\View\StatusComponentAbstract::class, 'edit' => \Component\View\StatusComponentAbstract::class],
            'editor' => ['display' => \Component\View\StatusComponentAbstract::class, 'edit' => \Component\View\StatusComponentAbstract::class],
            'everyone' => ['display' => \Component\View\StatusComponentAbstract::class]
        ];
    }

}
