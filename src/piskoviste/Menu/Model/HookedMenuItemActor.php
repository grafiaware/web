<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\HierarchyHooks;

use Pes\Database\Handler\HandlerInterface;
use Model\Dao\Hierarchy\HookedActorAbstract;

/**
 * Description of HookedArticleActions
 *
 * @author pes2704
 */
class HookedMenuItemActor extends HookedActorAbstract {

    const NEW_TITLE = 'Title';
    const NEW_ITEM_TYPE_FK = 'empty';

    private $menuItemTableName;
    private $newTitle;

    public function __construct($menuItemTableName, $newTitle = self::NEW_TITLE) {
        $this->menuItemTableName = $menuItemTableName;
        //TODO: Svoboda Message
        $this->newTitle = $newTitle;
    }

    /**
     * Metoda add
     *
     * {@inheritdoc}
     *
     * Typ nové položky menu je dán konstantou třídy (hodnota pro prázdnou položku) a titulek nové položky menu je dán instanční proměnnou.
     *
     * Vkládá:
     * - lang_code_fk -  zkopíruje z předchůdce (rodiče nebo sourozence), uid předchůdce ja zadáno jako parameetr $predecessorUuid
     * - type_fk - nový type_fk zadaný konstantou třídy, musí odpovídat hodnotě vyhrazené v databázi pro prázdnou položku menu
     * - nové uid - zadáno jako parametr metody
     * - title zadané jako instanční proměnná třídy nebo konstantou třídy
     * Ostatní sloupce mají default hodnoty databázové tabulky.
     * Hodnoty lang_code_fk čte vnořeným selectem, select vrací a insert vloží tolik položek, kolik je verzí předchůdce se stejným uid_fk,
     * tedy standartně verze pro všechny jazyky.
     *
     */
    public function add(HandlerInterface $transactionHandler, $predecessorUid, $uid) {
        $this->checkTransaction($transactionHandler);

        $newType = self::NEW_ITEM_TYPE_FK;
        // lang_code_fk zkopírované z předchůdce (rodiče nebo sourozence), nové uid, type_fk zkopírované z předchůdce (rodiče nebo sourozence) a default title zadané jako instanční proměnná,
        // ostatní sloupce mají default hodnoty dané definicé tabulky.
        // select vrací a insert vloží tolik položek, kolik je verzí předchůdce se stejným uid_fk - standartně verze pro všechny jazyky
        $stmt = $transactionHandler->prepare(
                " INSERT INTO $this->menuItemTableName (lang_code_fk, uid_fk, type_fk, title)
                    SELECT lang_code_fk, '$uid', type_fk, '$this->newTitle'
                    FROM $this->menuItemTableName
                    WHERE uid_fk=:predecessorUid
                    ");
        $stmt->bindParam(':predecessorUid', $predecessorUid);
        $stmt->execute();
    }

    /**
     * Metoda trash
     *
     * @inheritdoc
     *
     * Nastaví položky menu přesunuté do koše jako neaktivní.
     */
    public function trash(HandlerInterface $transactionHandler, $uidsArray) {
        $this->checkTransaction($transactionHandler);
        $in = "'".implode("', '", $uidsArray)."'";

        // přesunuté do koše - nastavím neaktivní
        $transactionHandler->exec(
            "UPDATE $this->menuItemTableName
            SET active = 0
            WHERE uid_fk IN ( $in )"
            );
    }

    /**
     * Metoda delete
     *
     * {@inheritdoc}
     *
     * Smaže položky menu. Podmínkou je, že id položky menu nebyla nikde použita jako cizí klíč.
     * Pokud je použita jako cizí klíč (paper, block) nastane chyba constraint violation.
     */
    public function delete(HandlerInterface $transactionHandler, $uidsArray) {
        $this->checkTransaction($transactionHandler);
        $in = "'".implode("', '", $uidsArray)."'";
        // tvrdý delete

        // !! nelze mazat - foreign keys v paper, block - nutno smazat nejdříve paper a block položky
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
}
