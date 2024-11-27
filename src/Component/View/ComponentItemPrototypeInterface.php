<?php
namespace Component\View;

use Pes\View\ViewInterface;

/**
 *
 * @author pes2704
 */
interface ComponentItemPrototypeInterface extends ViewInterface {
    public function __clone();
}
