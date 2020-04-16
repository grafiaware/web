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
class ArticleDaoOLD extends StrankyStareDaoAbstract {

    protected $dbHandler;

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

    /**
     * Vrací jednu řádku tabulky 'stranky' ve formě asociativního pole.
     *
     * Podle označení jazyka se vybírají příslušné sloupce z tabulky 'stranky', neovlivňuje výběr řádek. Přípustné hodnoty odpovídají značení sloupců tabulky (lan1, lan2, lan3).
     * Kritériem pro výběr řádek je hodnota identifikároru tabulky 'list'.
     * Automatickým kritériem, které použije vždy je hodnota aktivní stránka (např. aktiv_lan2) rovna 1 nebo hodnota kivní stránky je rovna 2 a aktuální datum je v rozsahu datumů start-stop.
     *
     * @param string $lang
     * @param string $list
     * @return array Asociativní pole obsahující sloupce vybrané řádky.
     * @throws StatementFailureException
     */
    public function get($lang, $list) {
//    private $id;
//    private $langCodeFk;
//    private $uidFk;
//    private $list;
//    private $headline;
//    private $content;
//    private $keywords;
//    private $editor;
//    private $updated;
        $sql = "SELECT list, {$this->dbObsahName($lang)} AS content, {$this->dbNazevName($lang)} AS headline, editor, zmena AS updated "
                . "FROM stranky "
                . "WHERE (list='$list') "
               ;
        $statement = $this->dbHandler->query($sql);
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

        // nula řádek - např. stránka není publikovaná
        if ($num_rows > 1) {
            user_error("V databázi existuje duplicitní záznam list=$list", E_USER_WARNING);
        }

        return $num_rows ? $statement->fetch(\PDO::FETCH_ASSOC) : [];
    }

    /**
     * Vrací pole řádek tabulky "stranky". Vrací řádky, které obsahují v polích nazev nebo obsah v "libovolném" (reálně pro lan 1,2,3) jazyce text zadaný jako parametr.
     * @param type $lang
     * @param type $text
     * @param type $publishedOnly Nepovinný parametr, default=TRUE. Defaultně metoda hledá jen oublikované stránky.
     * @return type
     * @throws StatementFailureException
     */
    public function fulltextsearch($lang, $text, $publishedOnly=\TRUE) {
        //InnoDB tables require a FULLTEXT index on all columns of the MATCH() expression to perform boolean queries. Boolean queries against a MyISAM search index can work even without a FULLTEXT index, although a search executed in this fashion would be quite slow.
        // starý web je: FULLTEXT KEY `vyhledavani` (`nazev_lan1`,`obsah_lan1`,`nazev_lan2`,`obsah_lan2`,`nazev_lan3`,`obsah_lan3`)) a typ MyISAM
        //
        //   The minimum length of the word for full-text searches as of follows :
        //
        //    Three characters for InnoDB search indexes.
        //    Four characters for MyISAM search indexes.
        //    - dal jsem min=4 a max=200 do <input> v hledani.php - jenže při hledání se minimum 4 znaky vztahuje na každé jednotlivé slovo - nenajde to např. "kde to"

        //pouze pro IN NATURAL LANGUAGE MODE:
        //the rows returned are automatically sorted with the highest relevance first.
        //
        //Relevance values are nonnegative floating-point numbers.
        //Zero relevance means no similarity.
        //Relevance is computed based on -
        //    the number of words in the row
        //    the number of unique words in that row
        //    the total number of words in the collection
        //    the number of documents (rows) that contain a particular word.

        //    $sql = "SELECT list,$db_nazev,$db_obsah FROM stranky WHERE MATCH (nazev_lan1,obsah_lan1,nazev_lan2,obsah_lan2,nazev_lan3,obsah_lan3) AGAINST('$text')";
        $scoreLimit = '0.2';  // musí být string - císlo 0.2 se převede na string 0,2
        $sql = "SELECT list, {$this->dbObsahName($lang)} AS content, {$this->dbNazevName($lang)} AS headline, editor, zmena AS updated "
                . ", MATCH (nazev_lan1,obsah_lan1,nazev_lan2,obsah_lan2,nazev_lan3,obsah_lan3) AGAINST('$text') as score "
                . "FROM stranky WHERE MATCH(nazev_lan1,obsah_lan1,nazev_lan2,obsah_lan2,nazev_lan3,obsah_lan3) AGAINST('$text') > $scoreLimit "
                . $this->publishedConditional($lang, $publishedOnly)
                . "ORDER BY score DESC";
        $statement = $this->dbHandler->query($sql);
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

    public function update($param) {

    }

}
