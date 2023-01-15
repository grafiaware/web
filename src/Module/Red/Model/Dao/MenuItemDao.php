<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Dao;

use Model\Dao\DaoEditAbstract;
use Model\RowData\RowDataInterface;

use Model\Dao\DaoReferenceNonuniqueInterface;

use Model\Dao\DaoReferenceNonuniqueTrait;
use Model\Dao\DaoReferenceUniqueTrait;

use Model\Dao\Exception\DaoForbiddenOperationException;

use Red\Model\Dao\Hierarchy\HierarchyAggregateEditDaoInterface;
use Red\Model\Dao\LanguageDao;

/**
 * Description of RsDao
 *
 * @author pes2704
 */
class MenuItemDao extends DaoEditAbstract implements MenuItemDaoInterface {

    const REFERENCE_PRIMARY = 'PRIMARY';
    const REFERENCE_HIERARCHY = 'hierarchy';
    const REFERENCE_LANGUAGE = 'language';
    const REFERENCE_MENU_ITEM_TYPE = 'menu_item_type';

//    use DaoReferenceNonuniqueTrait;
    use DaoReferenceUniqueTrait;

    public function getPrimaryKeyAttributes(): array {
        return ['lang_code_fk', 'uid_fk'];
    }

    public function getAttributes(): array {
        // POZOR! Musí obsahovat jméno 'list'. Není v entitě atd., ale používá se v Transformator middleware pro transformaci obsahu starých stránek.
        // Transformator middleware používá data čtená přímo pomocí tohoto Dao
        return ['lang_code_fk', 'uid_fk', 'type_fk', 'id', 'list',  'title', 'prettyuri', 'active'];
    }

    public function getReferenceAttributes($referenceName): array {
        // 'jméno referencované tabulky'=>['cizí klíč potomka (jméno sloupce v potomkovi)'=>'vlastní klíč rodiče (jméno sloupve v rodiči)']

//  PRIMARY KEY (`lang_code_fk`,`uid_fk`),
//  UNIQUE KEY `id` (`id`),
//  UNIQUE KEY `prettyuri` (`prettyuri`),
//  KEY `type_menu_item_type_fk1` (`type_fk`),
//  KEY `hierarchy_uid_fk` (`uid_fk`),
//  CONSTRAINT `hierarchy_uid_fk` FOREIGN KEY (`uid_fk`) REFERENCES `hierarchy` (`uid`),
//  CONSTRAINT `language_lang_code_fk` FOREIGN KEY (`lang_code_fk`) REFERENCES `language` (`lang_code`),
//  CONSTRAINT `type_menu_item_type_fk1` FOREIGN KEY (`type_fk`) REFERENCES `menu_item_type` (`type`) ON DELETE CASCADE ON UPDATE CASCADE

        return [
            self::REFERENCE_PRIMARY => ['lang_code_fk'=>'lang_code', 'uid_fk'=>'uid'],   // primární klíč je současně reference (1:1)
            self::REFERENCE_HIERARCHY => ['uid_fk'=>'uid'],
            self::REFERENCE_LANGUAGE => ['lang_code_fk'=>'lang_code'],
            self::REFERENCE_MENU_ITEM_TYPE => ['type_fk'=>'type']
        ][$referenceName];
    }

    public function getTableName(): string {
        return 'menu_item';
    }

    public function getContextConditions(): array {
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
     * Vrací řádek menu_item vyhledaný podle lang_code_fk a list - pro transformaci starého obsahu.
     * Kombinace lang_code_fk a list je unikátní.
     *
     * @param string $langCodeFk
     * @param string $list
     * @return array
     */
    public function getByList(array $langCodeFkAndList) {
        return $this->getUnique($langCodeFkAndList);
    }

    public function findAllLanguageVersions(array $uidFk) {
        return $this->find($this->sql->and($this->getContextConditions(), ['menu_item.uid_fk=:uid_fk']), $uidFk);
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

        $select = $this->sql->select("lang_code_fk, uid_fk, type_fk, active_menu_item.id AS id, title, prettyuri, active
                , searched_paper.headline, searched_paper.perex
                , active_content.content
                , active_menu_item.active AS active,
                score_h,
                score_c");
        $from = $this->sql->from("(SELECT lang_code_fk, uid_fk, type_fk, id, title, prettyuri, active, multipage
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
        $where = $this->sql->where("score_h > $scoreLimitHeadline
                     OR
                score_c > $scoreLimitContent
            ORDER BY score_h DESC, score_c DESC");
        $touplesToBind = [':text1' => $text, ':text2' => $text, ':lang_code_fk' => $langCodeFk];
        return $this->selectMany($select, $from, $where, $touplesToBind);

    }

    public function insert(RowDataInterface $rowData): bool {
        throw new DaoForbiddenOperationException("Nelze samostatně vložit novou položku menu_item. Nové položky lze vytvořit pouze voláním metod Node (Hierarchy) dao.");
    }

    /**
     *
     * @param RowDataInterface $rowData
     * @throws DaoForbiddenOperationException
     */
    public function delete(RowDataInterface $rowData): bool {
        throw new DaoForbiddenOperationException("Nelze samostatně smazat položku menu_item. Položky lze mazat pouze voláním metod Node (Hierarchy) dao.");
    }
}
