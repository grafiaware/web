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
class PaperDao extends DaoAbstract {

    /**
     * Vrací jednu řádku tabulky 'paper' ve formě asociativního pole.
     *
     * @param string $menuItemId
     * @return array Asociativní pole
     * @throws StatementFailureException
     */
    public function get($menuItemId) {

        $sql = "SELECT menu_item_id_fk, headline, content, keywords, editor, updated "
                . "FROM paper "
                . "WHERE (menu_item_id_fk=:menu_item_id_fk) "
               ;
        return $this->selectOne($sql, [':menu_item_id_fk' => $menuItemId], TRUE);
    }

    public function insert($row) {
        $sql = "INSERT INTO paper (menu_item_id_fk, headline, content, keywords, editor)
                VALUES (:menu_item_id_fk, :headline, :content, :keywords, :editor)";
        return $this->execInsert($sql, [':menu_item_id_fk'=>$row['menu_item_id_fk'], ':headline'=>$row['headline'], ':content'=>$row['content'], ':keywords'=>$row['keywords'], ':editor'=>$row['editor'],
            ]);
    }

    public function update($row) {
        $sql = "UPDATE paper SET headline = :headline, content = :content, keywords = :keywords, editor = :editor
                WHERE menu_item_id_fk = :menu_item_id_fk";
        return $this->execUpdate($sql, [':headline'=>$row['headline'], ':content'=>$row['content'], ':keywords'=>$row['keywords'], ':editor'=>$row['editor'],
             ':menu_item_id_fk'=>$row['menu_item_id_fk']]);
    }

    public function delete($row) {
        $sql = "DELETE FROM paper WHERE menu_item_id_fk = :menu_item_id_fk";
        return $this->execDelete($sql, [':menu_item_id_fk'=>$row['menu_item_id_fk']]);
    }
}
