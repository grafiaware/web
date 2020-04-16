<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Dao;
use Pes\Database\Handler\HandlerInterface;

/**
 * Description of RsDao
 *
 * @author pes2704
 */
class MenuDaoOLD extends StrankyStareDaoAbstract {

    protected $dbHandler;

    /**
     *
     * @param integer $rozsah_urovne Počet znaků identifikátoru 'list' odpovídajících jedné úrovni menu
     */
    public function __construct(HandlerInterface $dbHandler) {
        $this->dbHandler = $dbHandler;
    }

/**

-- Table "stranky" DDL

CREATE TABLE `stranky` (
  `list` varchar(50) CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  `poradi` tinyint(80) unsigned NOT NULL DEFAULT '0',
  `nazev_lan1` text CHARACTER SET utf8 COLLATE utf8_czech_ci,
  `obsah_lan1` longtext CHARACTER SET utf8 COLLATE utf8_czech_ci,
  `aktiv_lan1` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `aktiv_lan1start` date DEFAULT NULL,
  `aktiv_lan1stop` date DEFAULT NULL,
  `keywords_lan1` text CHARACTER SET utf8 COLLATE utf8_czech_ci,
  `nazev_lan2` text CHARACTER SET utf8 COLLATE utf8_czech_ci,
  `obsah_lan2` longtext CHARACTER SET utf8 COLLATE utf8_czech_ci,
  `aktiv_lan2` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `aktiv_lan2start` date DEFAULT NULL,
  `aktiv_lan2stop` date DEFAULT NULL,
  `keywords_lan2` text CHARACTER SET utf8 COLLATE utf8_czech_ci,
  `nazev_lan3` text CHARACTER SET utf8 COLLATE utf8_czech_ci,
  `obsah_lan3` longtext CHARACTER SET utf8 COLLATE utf8_czech_ci,
  `aktiv_lan3` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `aktiv_lan3start` date DEFAULT NULL,
  `aktiv_lan3stop` date DEFAULT NULL,
  `keywords_lan3` text CHARACTER SET utf8 COLLATE utf8_czech_ci,
  `aut_gen` varchar(6) CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL DEFAULT '0',
  `editor` varchar(20) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `zmena` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`list`),
  UNIQUE KEY `list` (`list`),
  FULLTEXT KEY `vyhledavani` (`nazev_lan1`,`obsah_lan1`,`nazev_lan2`,`obsah_lan2`,`nazev_lan3`,`obsah_lan3`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
 */

    public function get($lang, $list, $active, $actual) {
        $sql = "SELECT list AS uid, {$this->dbNazevName($lang)} AS title, {$this->dbAktivName($lang)} AS active, "
                . "({$this->dbAktivStartName($lang)} <= now()) AND (now() <= {$this->dbAktivStopName($lang)}) AS actual, "
                . "{$this->dbAktivStartName($lang)} AS start, {$this->dbAktivStopName($lang)} AS stop, editor "
                . "FROM stranky "
                . "WHERE (list='$list') "
                . $this->publishedConditional($lang, $active, $actual);
        $statement = $this->dbHandler->query($sql);
        if ($statement == FALSE) {
            $einfo = $this->dbHandler->errorInfo();
            throw new StatementFailureException($einfo[2].PHP_EOL.". Nevznikl PDO statement z sql příkazu: $sql", $einfo[1]);
        }
        $success = $statement->execute();
        if (!$success) {
            $einfo = $this->dbHandler->errorInfo();
            throw new StatementFailureException($einfo[2].PHP_EOL.". Nevykanal se PDO statement z sql příkazu: $sql", $einfo[1]);
        }
        return $statement->fetch(\PDO::FETCH_ASSOC);
    }


    /**
     * Vrací pole záznamů obsahujících list a nazev z tabulky 'stranky'. Kritériem je označení jazyka, prefix identifikátoru 'list' a délka identifikátoru 'list'.
     *
     * Podle označení jazyka se vybírají příslušné sloupce z tabulky 'stranky', neovlivňuje výběr řádek. Přípustné hodnoty odpovídají značení sloupců tabulky (lan1, lan2, lan3).
     *
     * Podle prefixu a délky identifikátoru se vybírají řádky. Např. pro prefix 's' a délku 3 vybere řádky s identifikátorem 'list' = 's01', 's02¨ atd.
     * Pro prefix 's01' a délku 6 vyberu podpoložky položly 's01' = 's01_01', s'01_02' atd.
     * Automatickým kritériem, které použije vždy je hodnota aktivní stránka (např. aktiv_lan2) rovna 1 nebo hodnota kivní stránky je rovna 2 a aktuální datum je v rozsahu datumů start-stop
     *
     * @param string $lang Označení jazyka (lan1, lan2, ...)
     * @param string $parentList Hodnota list rodiče všech vybíraných položek menu
     * @return array
     * @throws StatementFailureException
     */
    public function findChildren($lang, $parentList, $active, $actual) {
        $parentListLength = strlen($parentList);
        $childLevel = (int) ($parentListLength/$this->getRozsahUrovne($parentList)+1);  // od nuly
        $childListLength = $childLevel*$this->getRozsahUrovne($parentList);
        $sql = "SELECT list AS uid, {$this->dbNazevName($lang)} AS title, {$this->dbAktivName($lang)} AS active, "
                . "({$this->dbAktivStartName($lang)} <= now()) AND (now() <= {$this->dbAktivStopName($lang)}) AS actual, "
                . "{$this->dbAktivStartName($lang)} AS start, {$this->dbAktivStopName($lang)} AS stop, editor "
                . "FROM stranky "
                . "WHERE (left(list,$parentListLength)='$parentList') AND (char_length(list)='$childListLength') "
                . $this->publishedConditional($lang, $active, $actual)
                . "ORDER BY `poradi` ASC";
        $statement = $this->dbHandler->query($sql);
        if ($statement == FALSE) {
            $einfo = $this->dbHandler->errorInfo();
            throw new StatementFailureException($einfo[2].PHP_EOL.". Nevznikl PDO statement z sql příkazu: $sql", $einfo[1]);
        }
        $success = $statement->execute();
        if (!$success) {
            $einfo = $this->dbHandler->errorInfo();
            throw new StatementFailureException($einfo[2].PHP_EOL.". Nevykanal se PDO statement z sql příkazu: $sql", $einfo[1]);
        }
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function findFlatennedTreeBySelected($lang, $list, $active, $actual) {
        $listLnegth = strlen($list);
        $sql = "SELECT list AS uid, left(list, ) AS parent, {$this->dbNazevName($lang)} AS title, {$this->dbAktivName($lang)} AS active, "
                . "({$this->dbAktivStartName($lang)} <= now()) AND (now() <= {$this->dbAktivStopName($lang)}) AS actual, "
                . "{$this->dbAktivStartName($lang)} AS start, {$this->dbAktivStopName($lang)} AS stop, editor "
                . "FROM stranky "
                . "WHERE (left(list,$listLnegth)='$list') "
                . $this->publishedConditional($lang, $active, $actual)
                . "ORDER BY `poradi` ASC, `list` ASC";
        $statement = $this->dbHandler->query($sql);
        if ($statement == FALSE) {
            $einfo = $this->dbHandler->errorInfo();
            throw new StatementFailureException($einfo[2].PHP_EOL.". Nevznikl PDO statement z sql příkazu: $sql", $einfo[1]);
        }
        $success = $statement->execute();
        if (!$success) {
            $einfo = $this->dbHandler->errorInfo();
            throw new StatementFailureException($einfo[2].PHP_EOL.". Nevykanal se PDO statement z sql příkazu: $sql", $einfo[1]);
        }
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }

}
