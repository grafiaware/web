<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\HierarchyHooks;

use Pes\Database\Handler\HandlerInterface;
use Red\Model\Dao\Hierarchy\HookedActorAbstract;
use Red\Service\ItemCreator\Enum\ApiGeneratorEnum;

/**
 * Description of HookedArticleActions
 *
 * @author pes2704
 */
class HookedMenuItemActor extends HookedActorAbstract {

    const NEW_TITLE = 'Title';
    
    const DEFAULT_MODULE = 'red';
    const DEFAULT_GENERATOR = ApiGeneratorEnum::SELECT_GENERATOR;
    
    private $menuItemTableName;
    private $newTitle;
    private $defaultModule;
    private $defaultGenerator;
    private $trashItemTypeFk;

    /**
     * @param string $menuItemTableName Jméno tabulky pro položky menu
     * @param string $newTitle Titulek nově vytvořené položky. Default hodnota zadána konstantou třídy.
     * @param type $newItemTypeFk Typ nově vytvořené položky. Default hodnota zadána konstantou třídy.
     * @param type $trashItemTypeFk Typ položky, která je v koši. Default hodnota zadána konstantou třídy.
     */
    public function __construct($menuItemTableName, $newTitle = self::NEW_TITLE, $defaultModule = self::DEFAULT_MODULE, $defaultGenerator = self::DEFAULT_GENERATOR) {
        $this->menuItemTableName = $menuItemTableName;
        //TODO: Svoboda Message
        $this->newTitle = $newTitle;
        $this->defaultModule = $defaultModule;
        $this->defaultGenerator = $defaultGenerator;
    }

    /**

     * Metoda add
     *
     * {@inheritdoc}
     *
     * Titulek nové položky menu je dán instanční proměnnou (default hodnota zadána konstantou třídy).
     *
     * Vkládá do tabulky tyto hodnoty:
     * - lang_code_fk - zkopíruje z předchůdce (rodiče nebo sourozence), uid předchůdce je zadáno jako parameetr $predecessorUuid,
     *                  Hodnoty lang_code_fk čte vnořeným selectem, select vrací a insert vloží tolik jazykových mutací položky,
     *                  kolik mutací má předchůdce.
     * - nové uid - zadáno jako parametr metody
     * - title zadané jako instanční proměnná třídy nebo default hodnota zadána konstantou třídy
     * - prettyUri - prettyUri - zřetězení lang_code_fk a nového uid
     * 
     * Ostatní sloupce mají default hodnoty databázové tabulky.
     *
     * @param HandlerInterface $transactionHandler
     * @param string$predecessorUid
     * @param string $uid
     */
    public function add(HandlerInterface $transactionHandler, $predecessorUid, $uid) {
        $this->checkTransaction($transactionHandler);
        // tabulka menu_item: unique key a) kombinace lang_code a uid, b) prettyUri
        // lang_code_fk zkopírované z předchůdce (rodiče nebo sourozence), nové uid, type_fk zkopírované z předchůdce (rodiče nebo sourozence), prettyUri - zřetězení lang_code_fk a nového uid
        // SELECT podle uid předchůdce vybere všechny jazykové verze předchůdce -> přidává item ve všech jazycích, ve kterých je předchůdce
        // default title zadané jako instanční proměnná, prettyuri zřetězení z lang_code_fk a nového uid
        // ostatní sloupce mají default hodnoty dané definicí tabulky.
        // select vrací a insert vloží tolik položek, kolik je verzí předchůdce se stejným uid_fk - standartně verze pro všechny jazyky
        $defaultModule = $this->defaultModule ? "'$this->defaultModule'" : "NULL";
        $defaultGenerator = $this->defaultGenerator ? "'$this->defaultGenerator'" : "NULL";
        $newTitle = $this->newTitle ? "'$this->newTitle'" : "''";
        $stmt = $transactionHandler->prepare(
                " INSERT INTO $this->menuItemTableName (lang_code_fk, uid_fk, api_module_fk, api_generator_fk, title, prettyuri)
                    SELECT lang_code_fk, '$uid', $defaultModule, $defaultGenerator, $newTitle, CONCAT(lang_code_fk, '$uid')
                    FROM $this->menuItemTableName
                    WHERE uid_fk=:predecessorUid
                    ");
        $stmt->bindParam(':predecessorUid', $predecessorUid);
        $stmt->execute();
    }

    /**
     * {@inheritdoc}
     * Metoda delete smaže položky menu_item, paper a všechny položky content. Maže položky ve všech jazykových verzích.
     *
     * Smaže položky menu_item. paper a všechny položky content. Maže položky ve všec jazykových verzích.
     * Tabulky article, paper a content, mulripage mají constraint ON DELETE CASCADE - smazání menu_item smaže i paper a content. Použitý SQL příkaz delete
     * vybírá menu_item jen podle uid_fk => smaže všechny jazykové verze.
     */
    public function delete(HandlerInterface $transactionHandler, $uidsArray) {
        $this->checkTransaction($transactionHandler);
        $uidsIn = $this->getInFromUidsArray($uidsArray);

        // !! article, paper a content, multipage i menu_item_asset mají constraint ON DELETE CASCADE
        //  - smazání menu_item smaže i položky v těchro tabulkách
        // !! delete vybírá menu_item jen podle uid_fk => smaže všechny jazykové verze
        $transactionHandler->exec(
            "DELETE FROM $this->menuItemTableName
            WHERE uid_fk IN ( $uidsIn )"
            );
        // promazání asset - smaže assety, kzeré nemají cizí klíč c menu_tem_asset
        // !! nemaže soubory
        $transactionHandler->exec(
            "DELETE a FROM asset AS a
                INNER JOIN
            (SELECT 
                id, asset_id_fk
            FROM
                asset
            LEFT JOIN menu_item_asset ON (asset.id = menu_item_asset.asset_id_fk)

            WHERE
                menu_item_asset.asset_id_fk IS NULL) AS p2 USING (id)"
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
