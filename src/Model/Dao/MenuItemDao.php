<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Dao;

use Model\Context\PublishedContextInterface;

/**
 * Description of RsDao
 *
 * @author pes2704
 */
class MenuItemDao extends DaoAbstract {

    private $sqlGet;
    private $sqlGetByList;
    private $sqlFindByContentFulltextSearch;



    protected function getContextConditions() {
        $contextConditions = [];
        $publishedContext = $this->contextFactory->createPublishedContext();
        if ($publishedContext) {
            if ($publishedContext->getActive()) {
                $contextConditions['active'] = "menu_item.active = 1";
            }
            if ($publishedContext->getActual()) {
                $contextConditions['actual'] = "(ISNULL(menu_item.show_time) OR menu_item.show_time<=CURDATE()) AND (ISNULL(menu_item.hide_time) OR CURDATE()<=menu_item.hide_time)";
            }
        }
        return $contextConditions;
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
    public function get($langCodeFk, $uidFk) {
        if(!isset($this->sqlGet)) {
            $this->sqlGet = "SELECT lang_code_fk, uid_fk, type_fk, id, title, active, show_time, hide_time,
            (ISNULL(show_time) OR show_time<=CURDATE()) AND (ISNULL(hide_time) OR CURDATE()<=hide_time) AS actual "
                . "FROM menu_item "
                . $this->where($this->and($this->getContextConditions(), ['menu_item.lang_code_fk = :lang_code_fk', 'menu_item.uid_fk=:uid_fk']));
        }
        return $this->selectOne($this->sqlGet, [':lang_code_fk' => $langCodeFk, ':uid_fk'=> $uidFk], true);
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
    public function getByList($langCodeFk, $list, $active=true, $actual=true) {
        if (!isset($this->sqlGetByList)) {
            $this->sqlGetByList = "SELECT lang_code_fk, uid_fk, type_fk, id, title, active, show_time, hide_time,
                (ISNULL(show_time) OR show_time<=CURDATE()) AND (ISNULL(hide_time) OR CURDATE()<=hide_time) AS actual "
                . "FROM menu_item "
                . $this->where($this->and(['menu_item.lang_code_fk = :lang_code_fk', 'menu_item.list=:list']));
        }
        return $this->selectOne($this->sqlGetByList, [':lang_code_fk'=>$langCodeFk, ':list' => $list], true);
    }

    /**
     * Vrací pole řádek tabulky paper. Vrací řádky, které obsahují v polích nazev nebo obsah v zadaném jazyce slova uvedená v textu zadaném jako parametr.
     * Slova v parametru textu musí být oddělená mezerou, nejkratší vyhledávané slovo má 3 znaky.
     *
     * @param string $langCodeFk
     * @param string $text
     * @return type
     * @throws StatementFailureException
     */
    public function findByContentFulltextSearch($langCodeFk, $text) {
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

        if (!$this->sqlFindByContentFulltextSearch) {
            $scoreLimitHeadline = '1';  // musí být string - císlo 0.2 se převede na string 0,2
            $scoreLimitContent = '0.2';  // musí být string - císlo 0.2 se převede na string 0,2
            $this->sqlFindByContentFulltextSearch =
            "SELECT lang_code_fk, uid_fk, type_fk, active_menu_item.id AS id, title
                , searched_paper.headline, searched_paper.perex
                , active_content.content
                , active_menu_item.active AS active, active_menu_item.show_time AS show_time, active_menu_item.hide_time AS hide_time,
                (ISNULL(active_menu_item.show_time) OR active_menu_item.show_time<=CURDATE()) AND (ISNULL(active_menu_item.hide_time) OR CURDATE()<=active_menu_item.hide_time) AS actual,
                score_h,
                score_c
            FROM
                (SELECT lang_code_fk, uid_fk, type_fk, id, title, active, show_time, hide_time
                    FROM menu_item
                    WHERE "
                        .$this->where(['menu_item.lang_code_fk = :lang_code_fk', "menu_item.type_fk = 'paper'"])
                        ."
                ) AS active_menu_item
            INNER JOIN
                (SELECT id, menu_item_id_fk, headline, perex, MATCH (headline, perex) AGAINST(:text1) as score_h
                    FROM paper
                ) AS searched_paper
            ON (searched_paper.menu_item_id_fk=active_menu_item.id)
            LEFT JOIN
                (SELECT paper_id_fk, content, MATCH (content) AGAINST(:text2) as score_c
                    FROM paper_content
                    WHERE active = 1 AND (ISNULL(paper_content.show_time) OR paper_content.show_time<=CURDATE()) AND (ISNULL(paper_content.hide_time) OR CURDATE()<=paper_content.hide_time)
                ) AS active_content
            ON (active_content.paper_id_fk=searched_paper.id)

            WHERE
                score_h > $scoreLimitHeadline
                     OR
                score_c > $scoreLimitContent
            ORDER BY score_h DESC, score_c DESC";
        }
        $statement = $this->dbHandler->prepare($this->sqlFindByContentFulltextSearch);
        $statement->bindParam(':text1', $text, \PDO::PARAM_STR);    // PDO neumožňuje použít vícekrát stejný placeholder
        $statement->bindParam(':text2', $text, \PDO::PARAM_STR);
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
        $sql = "UPDATE menu_item SET title=:title, active=:active, show_time=:show_time, hide_time=:hide_time "
                . $this->where($this->and(['menu_item.lang_code_fk = :lang_code_fk', 'menu_item.list=:list']));
        return $this->execUpdate($sql, [':title'=>$row['title'], ':active'=>$row['active'], ':show_time'=>$row['show_time'], ':hide_time'=>$row['hide_time'],
            ':lang_code_fk' => $row['lang_code_fk'], ':uid_fk'=> $row['uid_fk']]);
    }

    public function delete($row) {
        throw new \LogicException("Nelze samostatně smazat položku menu_item.");
    }
}
