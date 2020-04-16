<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Database\Hierarchy;

use Pes\Database\Handler\HandlerInterface;

/**
 * Objekt HookedContentActionsInterface obsahuje metody, které mohu být volány při operacích vkládání nebo mazání položek v hierarchii.
 * Tento objekt tak umožňuje "pověsit" na operace vkládání a mazání položek v hierarchii další operace s jinými databázovými tabulkami. Tyto další
 * operace probíhají uvnitř spuštěné transakce, ve které se vkládají nebo mažou položky hierarchie a typicky tak umožňují přidání nebo mazání identifikátorů
 * položek hierarchie (uid) použitých jako cizí klíče v dalších tabulkách.
 *
 * Metody objektu přijímají databázový handler spuštěné transakce. Metody třídy nemusí transakci použít, ale nesmí ji ukončit, předpokládá se, že transakce
 * pokračuje v objektu editujícím hierarchii, na který jsou tyto akce navěšeny.
 * @author pes2704
 */
interface HookedMenuItemActorInterface {
    /**
     * Metoda add
     *
     * Vloží nové řádky do tabulky položek menu. Při vkládání de řídí vlastnostmí předchůdce, tedy rodiče nebo sourozence,  ke kterému se nová položka přidává
     * (jako dítě nebo sourozenec). Vloží položky ve všech jazykových verzích, které má předchůdce.
     *
     * @param HandlerInterface $transactionHandler Databázový handler se spuštěnou transakcí
     * @param string $parentUid uid předchůdce
     * @param type $uid uid položky hierarchie vložené v probíhající transakci
     */
    public function add(HandlerInterface $transactionHandler, $parentUid, $uid);

    /**
     * Metoda trash
     *
     * Provede příkazy potřebné před provedením přesunutí položek hierarchie do koše.
     *
     * @param HandlerInterface $transactionHandler Databázový handler se spuštěnou transakcí
     * @param array $uidsArray Pole primárních klíčů položek (pole uid) připravených v transakci pro smazání.
     */
    public function trash(HandlerInterface $transactionHandler, $uidsArray);

    /**
     * Metoda delete
     *
     * Provede příkazy potřebné před tím, než jsou v transakci smazány položky hierarchie. Typicky smaže položky tabulky, ve které je primární klíč hierarchie
     * použit jako cizí klíč a pokud by nebyly smazány, došlo by k chybě.
     *
     * @param HandlerInterface $transactionHandler Databázový handler se spuštěnou transakcí
     * @param array $uidsArray Pole primárních klíčů položek (pole uid) připravených v transakci pro smazání.
     */
    public function delete(HandlerInterface $transactionHandler, $uidsArray);
}
