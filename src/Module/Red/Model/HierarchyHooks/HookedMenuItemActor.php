<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\HierarchyHooks;

use Pes\Database\Handler\HandlerInterface;
use Red\Model\Dao\Hierarchy\HookedActorAbstract;

/**
 * Description of HookedArticleActions
 *
 * @author pes2704
 */
class HookedMenuItemActor extends HookedActorAbstract {

    const NEW_TITLE = 'Title';

    #### tyto konstanty musí odpovídat existujícím hodnotám v databázi ####
    const NEW_ITEM_TYPE_FK = 'empty';
    const TRASH_ITEM_TYPE_FK = 'trash';

    private $menuItemTableName;
    private $newTitle;
    private $newItemTypeFk;
    private $trashItemTypeFk;

    /**
     * @param string $menuItemTableName Jméno tabulky pro položky menu
     * @param string $newTitle Titulek nově vytvořené položky. Default hodnota zadána konstantou třídy.
     * @param type $newItemTypeFk Typ nově vytvořené položky. Default hodnota zadána konstantou třídy.
     * @param type $trashItemTypeFk Typ položky, která je v koši. Default hodnota zadána konstantou třídy.
     */
    public function __construct($menuItemTableName, $newTitle = self::NEW_TITLE, $newItemTypeFk=self::NEW_ITEM_TYPE_FK, $trashItemTypeFk= self::TRASH_ITEM_TYPE_FK) {
        $this->menuItemTableName = $menuItemTableName;
        //TODO: Svoboda Message
        $this->newTitle = $newTitle;
        $this->newItemTypeFk = $newItemTypeFk;
        $this->trashItemTypeFk = $trashItemTypeFk;
    }

    /**

     * Metoda add
     *
     * {@inheritdoc}
     *
     * Typ nové položky menu je dán konstantou třídy (hodnota pro prázdnou položku) a titulek nové položky menu je dán instanční proměnnou
     * (default hodnota zadána konstantou třídy).
     *
     * Vkládá:
     * - lang_code_fk - zkopíruje z předchůdce (rodiče nebo sourozence), uid předchůdce je zadáno jako parameetr $predecessorUuid,
     *                  Hodnoty lang_code_fk čte vnořeným selectem, select vrací a insert vloží tolik jazykových mutací položky,
     *                  kolik mutací má předchůdce.
     * - type_fk - nový type_fk zadaný konstantou třídy, musí odpovídat hodnotě vyhrazené v databázi pro prázdnou položku menu
     * - nové uid - zadáno jako parametr metody
     * - title zadané jako instanční proměnná třídy nebo konstantou třídy
     * Ostatní sloupce mají default hodnoty databázové tabulky.
     *
     * @param HandlerInterface $transactionHandler
     * @param string$predecessorUid
     * @param string $uid
     */
    public function add(HandlerInterface $transactionHandler, $predecessorUid, $uid) {
        $this->checkTransaction($transactionHandler);

        ;
        // lang_code_fk zkopírované z předchůdce (rodiče nebo sourozence), nové uid, type_fk zkopírované z předchůdce (rodiče nebo sourozence)
        // default title zadané jako instanční proměnná, prettyuri zřetězení z lang_code_fk a nového uid
        // ostatní sloupce mají default hodnoty dané definicí tabulky.
        // select vrací a insert vloží tolik položek, kolik je verzí předchůdce se stejným uid_fk - standartně verze pro všechny jazyky
        $stmt = $transactionHandler->prepare(
                " INSERT INTO $this->menuItemTableName (lang_code_fk, uid_fk, type_fk, title, prettyuri)
                    SELECT lang_code_fk, '$uid', '$this->newItemTypeFk', '$this->newTitle', CONCAT(lang_code_fk, '$uid')
                    FROM $this->menuItemTableName
                    WHERE uid_fk=:predecessorUid
                    ");
        $stmt->bindParam(':predecessorUid', $predecessorUid);
        $stmt->execute();
    }

    /**
     * {@inheritdoc}
     * Metoda trash nastaví položky menu_item přesunuté do koše jako neaktivní typ položky (type_fk) nastaví na hodnotu určenou pro koš.
     * Hodnota typu koš je dána konstantou třídy. Metoda nastaví jako neaktivní položky ve všech jazykových verzích.
     */
    public function trash(HandlerInterface $transactionHandler, $uidsArray) {
        $this->checkTransaction($transactionHandler);
        $in = $this->getInFromUidsArray($uidsArray);

        // přesunuté do koše - nastavím neaktivní
        $transactionHandler->exec(
            "UPDATE $this->menuItemTableName
            SET active = 0, type_fk = '".$this->trashItemTypeFk."¨
            WHERE uid_fk IN ( $in )"
            );
    }

    /**
     * {@inheritdoc}
     * Metoda delete smaže položky menu_item, paper a všechny položky content. Maže položky ve všech jazykových verzích.
     *
     * Smaže položky menu_item. paper a všechny položky content. Maže položky ve všec jazykových verzích.
     * Tabulky paper a content mají constraint ON DELETE CASCADE - smazání menu_item smaže i paper a content. Použitý SQL příkaz delete
     * vybírá menu_item jen podle uid_fk => smaže všechny jazykové verze.
     */
    public function delete(HandlerInterface $transactionHandler, $uidsArray) {
        $this->checkTransaction($transactionHandler);
        $in = $this->getInFromUidsArray($uidsArray);
        // tvrdý delete

        // !! paper a content mají constraint ON DELETE CASCADE - smazání menu_item smaže i paper a content
        // !! delete vybírá menu_item jen podle uid_fk => smaže všechny jazykové verze
        $transactionHandler->exec(
            "DELETE FROM $this->menuItemTableName
            WHERE uid_fk IN ( $in )"
            );


//Sometimes, it is useful to know which table is affected by the ON DELETE CASCADE  referential action when you delete data from a table. You can query this data from the referential_constraints in the information_schema  database as follows:

//        USE information_schema;

//SELECT
//    table_name
//FROM
//    referential_constraints
//WHERE
//    constraint_schema = 'database_name'
//        AND referenced_table_name = 'parent_table'
//        AND delete_rule = 'CASCADE'


    }

    /**
     * $uidsArray je resultset z fetchAll() => je to dvourozměrné pole, každá položka obsahuje pole hodnot sloupců.
     * Očekávám číslovaná pole a jen jeden sloupec v každém řádku.
     *
     * @param type $uidsArray
     */
    private function getInFromUidsArray($uidsArray) {
        foreach ($uidsArray as $row) {
            $uids[] = $row[0];
        }
        return "'".implode("', '", $uids)."'";
    }
}
