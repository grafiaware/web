<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Dao;

/**
 * Description of RsDao
 *
 * @author pes2704
 */
class MenuItemDao extends DaoAbstract {

    /**
     *
     * @param type $langCode
     * @param type $active
     * @param type $actual
     * @param type $condition
     * @return type
     */
    private function menuItemCondition($langCode, $active, $actual, $condition = []) {
        if ($langCode) {
            $condition[] = "menu_item.lang_code_fk = :lang_code_fk";
        }
        if ($active) {
            $condition[] = "menu_item.active = 1";
        }
        if ($actual) {
            $condition[] = "(ISNULL(menu_item.show_time) OR menu_item.show_time<=CURDATE()) AND (ISNULL(menu_item.hide_time) OR CURDATE()<=menu_item.hide_time)";
        }
        return $condition ? implode(" AND ", $condition) : "";
    }

    /**
     * Vrací jednu řádku tabulky 'menu_item' ve formě asociativního pole vybranou podle atributů primárního klíče
     *
     * @param string $langCodeFk
     * @param string $uidFk
     * @param bool $active Nepovinný parametr, default=TRUE. Defaultně metoda hledá jen aktivní (zveřejněné) stránky.
     * @param bool $actual Nepovinný parametr, default=TRUE. Defaultně metoda hledá jen aktuální stránky.
     * @return array
     * @throws StatementFailureException
     */
    public function get($langCodeFk, $uidFk, $active=\TRUE, $actual=\TRUE) {

        $sql = "SELECT lang_code_fk, uid_fk, type_fk, id, title, active, show_time, hide_time,
	(ISNULL(show_time) OR show_time<=CURDATE()) AND (ISNULL(hide_time) OR CURDATE()<=hide_time) AS actual "
                . "FROM menu_item "
                . "WHERE ".$this->menuItemCondition($langCodeFk, $active, $actual, ['menu_item.uid_fk=:uid_fk']);
        return $this->selectOne($sql, [':lang_code_fk' => $langCodeFk, ':uid_fk'=> $uidFk], TRUE);
    }

    /**
     * Vrací řádek menu_item vyhledaný podle lang_code_fk a list - pro transformaci starého obsahu.
     *
     * @param string $langCodeFk
     * @param string $list
     * @param bool $active Nepovinný parametr, default=TRUE. Defaultně metoda hledá jen aktivní (zveřejněné) stránky.
     * @param bool $actual Nepovinný parametr, default=TRUE. Defaultně metoda hledá jen aktuální stránky.
     * @return array
     */
    public function getByList($langCodeFk, $list, $active=\TRUE, $actual=\TRUE) {
        $sql = "SELECT lang_code_fk, uid_fk, type_fk, id, title, active, show_time, hide_time,
	(ISNULL(show_time) OR show_time<=CURDATE()) AND (ISNULL(hide_time) OR CURDATE()<=hide_time) AS actual "
                . "FROM menu_item "
                . "WHERE ".$this->menuItemCondition($langCodeFk, $active, $actual, ['menu_item.list=:list']);
        return $this->selectOne($sql, [':lang_code_fk'=>$langCodeFk, ':list' => $list], TRUE);
    }

    /**
     * Vrací pole řádek tabulky paper. Vrací řádky, které obsahují v polích nazev nebo obsah v zadaném jazyce slova uvedená v textu zadaném jako parametr.
     * Slova v parametru textu musí být oddělená mezerou, nejkratší vyhledávané slovo má 3 znaky.
     *
     * @param string $langCodeFk
     * @param string $text
     * @param bool $active Nepovinný parametr, default=TRUE. Defaultně metoda hledá jen aktivní (zveřejněné) stránky.
     * @param bool $actual Nepovinný parametr, default=TRUE. Defaultně metoda hledá jen aktuální stránky.
     * @return type
     * @throws StatementFailureException
     */
    public function findByPaperFulltextSearch($langCodeFk, $text, $active=\TRUE, $actual=\TRUE) {
        //InnoDB tables require a FULLTEXT index on all columns of the MATCH() expression to perform boolean queries. Boolean queries against a MyISAM search index can work even without a FULLTEXT index, although a search executed in this fashion would be quite slow.
        // starý web je: FULLTEXT KEY `vyhledavani` (`nazev_lan1`,`obsah_lan1`,`nazev_lan2`,`obsah_lan2`,`nazev_lan3`,`obsah_lan3`)) a typ MyISAM
        //
        //   The minimum length of the word for full-text searches as of follows :
        //
        //    Three characters for InnoDB search indexes (default).
        //    Four characters for MyISAM search indexes (default).

        //pouze pro IN NATURAL LANGUAGE MODE:
        //
        //Relevance values are nonnegative floating-point numbers.
        //Zero relevance means no similarity.
        //Relevance is computed based on -
        //    the number of words in the row
        //    the number of unique words in that row
        //    the total number of words in the collection
        //    the number of documents (rows) that contain a particular word.

        // čti dokumentaci - umí "word" - slovo musí být uvedeno

        $scoreLimit = '0.2';  // musí být string - císlo 0.2 se převede na string 0,2
        $sql = "SELECT lang_code_fk, uid_fk, type_fk, id, title, active, show_time, hide_time,
                    (ISNULL(show_time) OR show_time<=CURDATE()) AND (ISNULL(hide_time) OR CURDATE()<=hide_time) AS actual "
                    .", MATCH (headline, content) AGAINST(:text1) as score
                FROM
                        paper
                        INNER JOIN
                        (SELECT
                                lang_code_fk, uid_fk, type_fk, id, title, active, show_time, hide_time
                        FROM
                                menu_item
                        WHERE "
                        .$this->menuItemCondition($langCodeFk, $active, $actual, ["menu_item.type_fk = 'paper'"])
                        .") AS menu_item ON (paper.menu_item_id_fk=menu_item.id)
                WHERE
                        MATCH(headline, content) AGAINST(:text2) > $scoreLimit
                ORDER BY score DESC";
        $statement = $this->dbHandler->prepare($sql);
        $statement->bindParam(':text1', $text, \PDO::PARAM_STR);
        $statement->bindParam(':text2', $text, \PDO::PARAM_STR);    // PDO neumožňuje použít vícekrát stejný placeholder
        $statement->bindParam(':lang_code_fk', $langCodeFk, \PDO::PARAM_STR);
        if ($statement == FALSE) {
            $einfo = $this->dbHandler->errorInfo();
            throw new StatementFailureException($einfo[2].PHP_EOL.". Nevznikl PDO statement z sql příkazu: $sql", $einfo[1]);
        }
        $success = $statement->execute();
        if (!$success) {
            $einfo = $this->dbHandler->errorInfo();
            throw new StatementFailureException($einfo[2].PHP_EOL.". Nevykonal se PDO statement z sql příkazu: $sql", $einfo[1]);
        }
        $num_rows = $statement->rowCount();
        return $num_rows ? $statement->fetchAll(\PDO::FETCH_ASSOC) : [];
    }

    public function insert($row) {
        throw \LogicException("Nelze samostatně vložit novou položku menu_item.");
    }

    /**
     * Zapisuje jen title, active, show_time, hide_time - ostatní jsou měněny jen pomocí hierarchy (active je readonly)
     * @param type $row
     * @return type
     */
    public function update($row) {
        $sql = "UPDATE menu_item SET title=:title, active=:active, show_time=:show_time, hide_time=:hide_time
                WHERE lang_code_fk=:lang_code_fk AND uid_fk=:uid_fk ";
        return $this->execUpdate($sql, [':title'=>$row['title'], ':active'=>$row['active'], ':show_time'=>$row['show_time'], ':hide_time'=>$row['hide_time'],
            ':lang_code_fk' => $row['lang_code_fk'], ':uid_fk'=> $row['uid_fk']]);
    }

    public function delete($row) {
        throw \LogicException("Nelze samostatně smazat položku menu_item.");
    }
}
