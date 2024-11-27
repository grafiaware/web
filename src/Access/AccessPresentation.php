<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Access;

use Component\View\ComponentInterface;
use Component\ViewModel\StatusViewModelInterface;

use Access\Enum\AccessPresentationEnum;
use Access\Enum\RoleEnum;

use Access\Exception\ClassNotExistsException;

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

//    public function getStatus(): StatusViewModelInterface {
//        return $this->statusViewModel;
//    }
    public function hasAnyPermission($resourceClassname): bool {
        $has = false;
        $role = $this->statusViewModel->getUserRole();
        $logged = $this->statusViewModel->isUserLoggedIn();
        $permissions = $resourceClassname::getComponentPermissions();
        $activeRoles = $this->getActiveRoles($logged, $role, $permissions);
        foreach ($activeRoles as $activeRole) {
            if (array_key_exists($activeRole, $permissions)) {
                $permission = $permissions[$activeRole];
                $has = (bool) $permission;
            }
        }
        return $has;
    }

    /**
     * Vrací informaci, jestli přihlášený uživatel má oprávnění zobrazit komponent v zadaném prezentační režimu.
     * 
     * @param string $resourceClassname FQDN třídy (například view nebo komponent), pro kterou zjišťuji oprávnění k akci
     * @param type $presentationMode Prezentační režim. Hodnota z AccessPresentationEnum.
     * @return bool
     */
    public function isAllowed($resourceClassname, $presentationMode): bool {
        $mode = (new AccessPresentationEnum())($presentationMode);
        return $this->allowedByRole($resourceClassname, $mode);
    }
    
    private function allowedByRole($resourceClassname, $mode) {
        $isAllowed = false;
        $role = $this->statusViewModel->getUserRole();
        $logged = $this->statusViewModel->isUserLoggedIn();
        $permissions = $resourceClassname::getComponentPermissions();
        $activeRoles = $this->getActiveRoles($logged, $role, $permissions);
        foreach ($activeRoles as $activeRole) {
            if (array_key_exists($activeRole, $permissions) AND array_key_exists($mode, $permissions[$activeRole])) {
                $permission = $permissions[$activeRole][$mode];
                if($permission instanceof \Closure) {
                    $isAllowed = (bool) $permission();
                } else {
                    $isAllowed = (bool) $permission;
                } 
                if ($isAllowed) {
                    break;
                }
            }
        }
        return $isAllowed;
    }
    
    private function allowedByStatus($resourceClassname) {
        $resourceClassname::getStatusPermissions();
    }

    /**
     * Vrací pole rolí uživatele.
     * Pokud má uživatel přidělenu roli a pro tuto roli jsou existuje položka v permission je mu přidělena tato role jako aktivní.
     * Pokud je uživatel přihlášen je mu přidána aktivní role AUTHENTICATED,
     * pokud uživatel není přihlášen je mu přidána aktivní role ANONYMOUS.
     *
     * Příklad:
     * 1) Uživatel s rolí SUP je i přihlášen - získá aktivní role SUP a AUTHENTICATED
     * 2) Uživatel je přihlášen a nemá nastavenou roli - získá jen AUTHENTICATED
     * 3) Uživatel není přihlášen - získá jen ANONYMOUS
     *
     * @param type $role
     * @param type $permissions
     * @return type
     */
    private function getActiveRoles($logged, $role, $permissions) {
        if (isset($role) AND array_key_exists($role, $permissions)){
            $ret[] = $role;
        }
        if($logged){
            $ret[] = RoleEnum::AUTHENTICATED;
        } else {
            $ret[] = RoleEnum::ANONYMOUS;
        }
        return $ret;
    }
}
