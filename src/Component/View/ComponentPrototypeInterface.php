<?php
namespace Component\View;
use Component\View\ComponentItemInterface;
/**
 *
 * @author pes2704
 */
interface ComponentPrototypeInterface extends ComponentItemInterface {
    
    /**
     * Klonuje view pro komponent. Volá se při přidávání dalších view do composite view vždy pro každý item v kolekci.
     */
    public function __clone();
}
