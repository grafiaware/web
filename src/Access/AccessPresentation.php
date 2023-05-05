<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Access;

use Component\View\ComponentInterface;
use Component\ViewModel\StatusViewModelInterface;
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

    public function getStatus(): StatusViewModelInterface {
        return $this->statusViewModel;
    }

    /**
     *
     * @param string $resourceClassname FQDN třídy (například view nebo komponent), pro kterou zjišťuji oprávnění k akci
     * @param type $action Akce
     * @return bool
     */
    public function isAllowed(string $resourceClassname, $action): bool {
        // autoload proběhne již při použití XXX::class
        if (!class_exists($resourceClassname)) {
            throw new ClassNotExistsException("Třída '$resourceClassname' neexistuje.");
        }
        $isAllowed = false;
        $role = $this->statusViewModel->getUserRole();
        $logged = $this->statusViewModel->getUserLoginName() ? true : false;
        $permissions = $resourceClassname::getComponentPermissions();
        $activeRoles = $this->getActiveRoles($logged, $role, $permissions);
        $isAllowed =false;
        foreach ($activeRoles as $activeRole) {
            if (array_key_exists($activeRole, $permissions) AND array_key_exists($action, $permissions[$activeRole])) {
                $permittedResource = $permissions[$activeRole][$action];
                $isAllowed = ($resourceClassname === $permittedResource) ? true : false;
                if ($isAllowed) {
                    break;
                }
            }
        }
        return $isAllowed;
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
