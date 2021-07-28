<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace FrontControler\Permission;

/**
 * Description of Permissions
 *
 * @author pes2704
 */
class Permission implements PermissionInterface{

    private $callerClassFqdn;

    private $permissions = [];

    /**
     * {@inheritdoc}
     *
     * @param type $classFqdn
     * @param type $permissions
     * @return void
     */
    public function setPermittedActions($classFqdn, $permissions): void {
        $this->callerClassFqdn = $classFqdn;
        $this->permissions = $permissions;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \LogicException
     * @throws \UnexpectedValueException
     */
    public function isPermittedAction($action): bool {
        $permitted = FALSE;
        //TODO: povoleno pro všechny přihlášené uživatele !!!
        $securityStatus = $this->statusModel->getStatusSecurity();
        $role = isset($securityStatus) ? $securityStatus->getRole() : NULL;
        $grants = $this->getGrants();
        if (!isset($grants) OR ! is_array($grants)) {
            throw new \LogicException("Nejsou nastavena oprávnění. Metoda getGrants() objektu ".get_called_class()." nevrací potřebné pole.");
        } elseif (!array_key_exists($action, $grants)) {
            throw new \UnexpectedValueException("Nejsou nastavena oprávnění pro akci $action v objektu ".get_called_class()."." );
        } elseif($grants[$action]=='*') {
            $permitted = TRUE;
        } elseif (isset($role) AND $grants[$action]=='authenticated') {
            $permitted = TRUE;
        } elseif (isset($role) AND $grants[$action]==$role) {
            $permitted = TRUE;
        }
        return $permitted;
    }


    /**
     * {@inheritdoc}
     *
     * @param type $methodFqdn
     * @return type
     */
    public function isPermittedMethod($methodFqdn): bool {
        if (strpos($methodFqdn, $this->callerClassFqdn)==0) {
            $methodFqdn = substr($methodFqdn, strlen($this->callerClassFqdn.'::'));
            return $this->isPermittedAction($methodFqdn);
        }
    }
}
