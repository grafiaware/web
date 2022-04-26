<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Dao;

use Model\Dao\DaoContextualAbstract;
use Model\RowData\RowDataInterface;

use Model\Dao\DaoReadonlyFkInterface;
use \Model\Dao\DaoReadonlyFkTrait;

use Model\Dao\Exception\DaoForbiddenOperationException;

/**
 * Description of RsDao
 *
 * @author pes2704
 */
class MenuItemDao extends DaoContextualAbstract implements DaoReadonlyFkInterface {

    use DaoReadonlyFkTrait;

    public function getPrimaryKeyAttribute(): array {
        return ['lang_code_fk', 'uid_fk'];
    }

    public function getAttributes(): array {
        return ['lang_code_fk', 'uid_fk', 'type_fk', 'id', 'title', 'prettyuri', 'active'];
    }
    
    public function getForeignKeyAttributes(): array {
        return [
            'lang_code_fk'=>['lang_code_fk'],
            'uid_fk'=>['uid_fk'],
            'type_fk'=>['type_fk']
        ];
    }
    public function getTableName(): string {
        return 'menu_item';
    }

    protected function getContextConditions() {
        $contextConditions = [];
        if (isset($this->contextFactory)) {
            $publishedContext = $this->contextFactory->createPublishedContext();
            if ($publishedContext) {
                if ($publishedContext->selectPublished()) {
                    $contextConditions['active'] = "menu_item.active = 1";
                }
            }
        }
        return $contextConditions;
    }


    /**
     * Vrací řádek menu_item vyhledaný podle lang_code_fk a prettyuri - pro statické stránky
     *
     * @param type $langCodeFk
     * @param type $prettyUri
     * @return type
     */
    public function getById($id) {
        $select = $this->select("lang_code_fk, uid_fk, type_fk, id, title, prettyuri, active ");
        $from = $this->from("menu_item ");
        $where = $this->where($this->and($this->getContextConditions(), ['menu_item.id=:id']));
        $touplesToBind = [':id'=> $id];
        return $this->selectOne($select, $from, $where, $touplesToBind, true);
    }

    /**
     * Vrací řádek menu_item vyhledaný podle prettyuri - pro statické stránky
     *
     * @param string $prettyUri
     * @return type
     */
    public function getByPrettyUri(array $prettyUri) {
        $select = $this->sql->select($this->getAttributes());
        $from = $this->sql->from($this->getTableName());
        $where = $this->sql->where($this->sql->and($this->sql->touples(['prettyuri=:prettyuri'])));
        $touplesToBind = $this->getTouplesToBind($prettyUri);
        return $this->selectOne($select, $from, $where, $touplesToBind, true);
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
        $select = $this->select("lang_code_fk, uid_fk, type_fk, id, title, prettyuri, active ");
        $from = $this->from("menu_item ");
        $where = $this->where($this->and(['menu_item.lang_code_fk = :lang_code_fk', 'menu_item.list=:list']));
        $touplesToBind = [':lang_code_fk'=>$langCodeFk, ':list' => $list];
        return $this->selectOne($select, $from, $where, $touplesToBind, true);
    }

    public function findAllLanguageVersions($uidFk) {
        $select = $this->select("lang_code_fk, uid_fk, type_fk, id, title, prettyuri, active ");
        $from = $this->from("menu_item ");
        $where = $this->where($this->and($this->getContextConditions(), ['menu_item.uid_fk=:uid_fk']));
        $touplesToBind = [':uid_fk' => $uidFk];
        return $this->selectMany($select, $from, $where, $touplesToBind);
    }

    /**
     * Vrací pole řádek tabulky paper. Vrací řádky, které obsahují v polích nazev nebo obsah v zadaném jazyce slova uvedená v textu zadaném jako parametr.
     * Slova v parametru textu musí být oddělená mezerou, nejkratší vyhledávané slovo má 3 znaky.
     *
     * @param string $langCodeFk
     * @param string $text
     * @return type
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

        $scoreLimitHeadline = '1';  // musí být string - císlo 0.2 se převede na string 0,2
        $scoreLimitContent = '0.2';  // musí být string - císlo 0.2 se převede na string 0,2

        $select = $this->select("lang_code_fk, uid_fk, type_fk, active_menu_item.id AS id, title, prettyuri, active
                , searched_paper.headline, searched_paper.perex
                , active_content.content
                , active_menu_item.active AS active,
                score_h,
                score_c");
        $from = $this->from("(SELECT lang_code_fk, uid_fk, type_fk, id, title, prettyuri, active, multipage
                    FROM menu_item "
                        .$this->where($this->and(['menu_item.lang_code_fk = :lang_code_fk', "menu_item.type_fk = 'paper'"]))
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
            ");
        $where = $this->where("score_h > $scoreLimitHeadline
                     OR
                score_c > $scoreLimitContent
            ORDER BY score_h DESC, score_c DESC");
        $touplesToBind = [':text1' => $text, ':text2' => $text, ':lang_code_fk' => $langCodeFk];
        return $this->selectMany($select, $from, $where, $touplesToBind);

    }

    public function insert(RowDataInterface $rowData) {
        throw new DaoForbiddenOperationException("Nelze samostatně vložit novou položku menu_item. Nové položky lze vytvořit pouze voláním metod Node (Hierarchy) dao.");
    }

    /**
     *
     * @param RowDataInterface $rowData
     * @throws DaoForbiddenOperationException
     */
    public function delete(RowDataInterface $rowData) {
        throw new DaoForbiddenOperationException("Nelze samostatně smazat položku menu_item. Položky lze mazat pouze voláním metod Node (Hierarchy) dao.");
    }
}
