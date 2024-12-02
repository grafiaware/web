<?php
namespace Component\View;

use Pes\View\ViewInterface;

/**
 *
 * @author pes2704
 */
interface ComponentPrototypeInterface extends ViewInterface {
    public function __clone();
}
