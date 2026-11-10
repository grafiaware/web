<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Access\Enum;

use Pes\Core\Type\Enum;

/**
 * Description of RoleEnum
 *
 * @author pes2704
 */
class RoleEnum  extends Enum {
    const ANONYMOUS = 'anonymous';
    const AUTHENTICATED = 'authenticated';
    const EDITOR = 'editor';
    const SUPERVISOR = 'supervisor';
    const VISITOR = 'visitor';
    const REPRESENTATIVE = 'representative';
    const EVENTS_ADMINISTRATOR = 'events_administrator';
    const RED_ADMINISTRATOR = 'red_administrator';
    // A9 (docs/vp/zadani/A9.md): přidělování rolí
    /** Účet z konfigurace na serveru (ConfigurationRoot.local.php) – pseudorole, není v tabulce role, nejde přidělit. */
    const GLOBAL_ROLE_GRANTOR = 'global_role_grantor';
    const AUTH_ROLE_GRANTOR = 'auth_role_grantor';
    const RED_ROLE_GRANTOR = 'red_role_grantor';
    const EVENTS_ROLE_GRANTOR = 'events_role_grantor';
}
