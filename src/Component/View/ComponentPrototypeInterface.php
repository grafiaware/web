<?php
namespace Component\View;

use Component\View\ComponentItemInterface;

/**
 *
 * @author pes2704
 */
interface ComponentPrototypeInterface extends ComponentItemInterface {
    public function __clone();
}
