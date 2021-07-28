<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace FrontControler\Permission;

/**
 *
 * @author pes2704
 */
interface PermissionInterface {

    /**
     * Přijímá plně kvalifikovaný název třídy, ze které bude volána metoda isPermittedMethod a pole dvojic jméno akce => role.
     *
     * Role je role uživatele - hodnota User::getRole() nebo zástupný symbol pro roli (wildcard)
     * Zástupné symboly pro role jsou: <ul>
     * <li>'*' znamená neexistující nebo libovolnou roli</li>
     * <li>'authenticated' znamená libovolnou roli (všechny role)</li>
     * </ul>
     * Příklad volání:
     * <pre>
     * $permissions->setPermittedActions(__CLASS__, [
     *      'home' => '*',
     *      'item' => 'editor',
     *      'searchResult' => 'authenticated'])
     * </pre>
     *
     * @param string $classFqdn Plně kvalifikovaný název třídy, ze které bude volána metoda isPermittedMethod
     * @param array $permissions
     * @return void
     */
    public function setPermittedActions($classFqdn, array $permissions): void;

    /**
     * Vrací informaci zda je akce pro uživatele povolena. Určí to na základě role přihlášeního uživatele
     * a pole párů akce => role zadaného metodou ->setPermittedActions().
     *
     * Role je role uživatele - hodnota User::getRole() nebo zástupný symbol pro roli (wildcard)
     * Zástupné symboly pro role jsou: <ul>
     * <li>'*' znamená neexistující nebo libovolnou roli</li>
     * <li>'authenticated' znamená libovolnou roli (všechny role)</li>
     * </ul>
     *
     * @param string $action Jméno akce uvedené jako klíč v poli, které bylo zadáno metodou ->setPermittedActions().
     * @return bool
     */
    public function isPermittedAction($action): bool;

    /**
     * <p>Vrací informaci zda je volání metody kontroleru pro uživatele povoleno. Zjišťuje, zda je pro roli uživatele povolena akce
     * se jménem stejným jako je jméno metody konroleru. Jméno metody kontroleru odvodí z FQDN jména metody kontroleru. To umožňuje,
     * aby metoda isPermittedMethod() byla volána s použítím magické konstanty __METHOD__ takto:</p>
     * <code>
     * $this->isPermittedMethod(__METHOD__);
     * </code>
     *
     * @param string $methodFqdn Plně kvalifikované jméno metody, ze které je volána metoda isPermittedMethod(). Tuto hodnotu má pseudokonstanta __METHOD__ ve volající metodě.
     * @return bool
     */
    public function isPermittedMethod($methodFqdn): bool;
}
