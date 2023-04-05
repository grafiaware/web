<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Component\ViewModel\Generated;

use Component\ViewModel\ViewModelInterface;

/**
 *
 * @author pes2704
 */
interface SearchResultViewModelInterface extends ViewModelInterface {
/**
     * Text pro hledání metodou getSearchedMenuItems(). Info v dokumntaci getSearchedMenuItems().
     * @param string $text
     * @return $this
     */
    public function setQuery($text);

    /**
     * Vrací text nastavený pro hledání metodou getSearchedMenuItems().
     * @return string
     */
    public function getQuery();


    /**
     * Vyhledá menu items, které obsahují šlánky, ve kterých se vyskytují v polích nazev nebo obsah v zadaném jazyce slova textu nastaveného metodou setKey().
     * Hledají se jednotlivá slova v MySQL módu IN NATURAL LANGUAGE MODE.
     * Slova v zadaném textu musí být oddělená mezerou, nejkratší vyhledávané slovo má 3 znaky. Vyhledávání podporuje i operátor ""
     * (nepř. "word"), podrobnosti nutnu nastudovat v dokumentaci MySQL. Pro vahledávání se použije jen prvních 200 znaků parametru text.
     *
     * @return MenuItemInterface array of
     */
    public function getSearchedMenuItems();

    public function getString();

}
