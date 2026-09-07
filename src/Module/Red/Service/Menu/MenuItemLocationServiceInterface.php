<?php
namespace Red\Service\Menu;

/**
 * Umístění položky v hierarchii menu (koš vs. ostatní stromy).
 */
interface MenuItemLocationServiceInterface {

    /**
     * Položka leží v podstromu kořene menu trash (včetně kořene).
     */
    public function isInTrash(string $uid): bool;
}
