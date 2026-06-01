<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPInterface.php to edit this template
 */

namespace Model\Entity;

/**
 *
 * @author pes2704
 */
interface SecurityPersistableEntityInterface extends PersistableEntityInterface {
    /**
     * Metoda provede ptřebná nastavení v situaci ztráty security kontextu (typicky při odhlášení uživatele). 
     * Metoda přijímá login jméno uživatele, který se právě odhlásil. Nastavení této informace lze pak použít v následném requestu, 
     * který bude přistupovat k databázi se zápisem informací závislých na přihlášeném uživateli.
     * 
     * @param string|null $loggedOffUserName
     */
    public function processActionsForLossOfSecurityContext(?string $loggedOffUserName=null);
}
