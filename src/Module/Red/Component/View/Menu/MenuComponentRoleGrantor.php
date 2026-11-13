<?php

namespace Red\Component\View\Menu;

use Red\Component\View\Menu\MenuComponentAbstract;

use Access\Enum\RoleEnum;
use Access\Enum\AccessPresentationEnum;

/**
 * A9 (docs/vp/zadani/A9.md): menu „menu role grantor“ (správa účtů a rolí – ADMIN CREDENTIALS, ADMIN ROLE)
 * pro role, které přidělují role. Kopie MenuComponentEventsAdmin; menu events admin zůstává pro events_administrator.
 */
class MenuComponentRoleGrantor extends MenuComponentAbstract {
    public static function getComponentPermissions(): array {
        return [
            RoleEnum::GLOBAL_ROLE_GRANTOR => [AccessPresentationEnum::DISPLAY => true],
            RoleEnum::AUTH_ROLE_GRANTOR => [AccessPresentationEnum::DISPLAY => true],
            RoleEnum::RED_ROLE_GRANTOR => [AccessPresentationEnum::DISPLAY => true],
            RoleEnum::EVENTS_ROLE_GRANTOR => [AccessPresentationEnum::DISPLAY => true],
        ];
    }
}
