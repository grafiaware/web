<?php
namespace Red\Service\MenuItemxManipulator;


/**
 *
 * @author pes2704
 */
interface MenuItemManipulatorInterface {

    const DEACTOVATE_ONE = "deactivated one item";
    const DEACTOVATE_WITH_DESCENDANTS = "deactivated with descendants";
    const ACTIVATE_ONE = "activated one item";
    const UNABLE_ACTIVATE = "unable activate item";

    public function toggleItems($langCode, $uid);
}
