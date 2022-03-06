<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Access;

use Component\ViewModel\StatusViewModelInterface;
use Access\Enum\RoleEnum;

/**
 * Description of Access
 *
 * @author pes2704
 */
class AccessPresentation implements AccessPresentationInterface {

    /**
     * @var StatusViewModelInterface
     */
    protected $statusViewModel;

    public function __construct(StatusViewModelInterface $statusViewModel) {
        $this->statusViewModel = $statusViewModel;
    }

    /**
     *
     * @param object $resource Objekt (například view nebo komponent), pro který zjičťuji oprávnění k akci
     * @param array $permissions Oprávnění
     * @param type $action Akce
     * @return bool
     */
    public function isAllowed($resource, $permissions, $action): bool {
        $isAllowed = false;
        $role = $this->statusViewModel->getUserRole();
        $logged = $this->statusViewModel->getUserLoginName() ? true : false;
        $activeRole = $this->getActiveRole($logged, $role, $permissions);
        if (isset($activeRole)) {
            if (array_key_exists($activeRole, $permissions) AND array_key_exists($action, $permissions[$activeRole])) {
                $permittedResource = $permissions[$activeRole][$action];
                $isAllowed = ($resource instanceof $permittedResource) ? true : false;
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
}
