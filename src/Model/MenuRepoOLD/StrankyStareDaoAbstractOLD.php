<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Dao;

/**
 * Description of StrankyStareDaoAbstract
 *
 * @author pes2704
 */
class StrankyStareDaoAbstractOLD {

    private $rozsahyUrovne = [
        's'=>3,
        'l'=>3,
        'p'=>3,
        'a'=>3
    ];

    protected function publishedConditional($lang, $active, $actual) {
        $c = [];
        $cstr = "";
        if ($active) {
            $c[] = "{$this->dbAktivName($lang)}=1";
        }
        if ($actual) {
            $c[] = "({$this->dbAktivName($lang)}=2 AND {$this->dbAktivStartName($lang)}<=CURDATE() AND CURDATE()<={$this->dbAktivStopName($lang)})";
        }
        $cstr = implode(" OR ", $c);

        return $cstr
                ? "AND ($cstr) "
                : "";
    }

    protected function getRozsahUrovne($list) {
        $prefix = substr($list, 0, 1);
        return $this->rozsahyUrovne[$prefix];
    }

    protected function dbObsahName($lang) {
        return 'obsah_'.$lang;
    }

    protected function dbNazevName($lang) {
        return 'nazev_'.$lang;
    }

    protected function dbAktivName($lang) {
        return 'aktiv_'.$lang;
    }

    protected function dbAktivStartName($lang) {
        return 'aktiv_'.$lang.'start';
    }

    protected function dbAktivStopName($lang) {
        return 'aktiv_'.$lang.'stop';
    }

}
