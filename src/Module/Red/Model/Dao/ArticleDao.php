<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Dao;

use Model\Dao\DaoEditAbstract;
use Model\Dao\DaoAutoincrementKeyInterface;
use \Model\Dao\DaoAutoincrementTrait;
use Model\RowData\RowDataInterface;

/**
 * Description of RsDao
 *
 * @author pes2704
 */
class ArticleDao extends DaoEditAbstract implements DaoAutoincrementKeyInterface {

    use DaoAutoincrementTrait;

    private $keyAttribute = 'id';

    public function getPrimaryKeyAttribute() {
        return $this->keyAttribute;
    }

    /**
     * Vrací jednu řádku tabulky 'article' ve formě asociativního pole podle primárního klíče.
     *
     * @param string $id Hodnota primárního klíče
     * @return array Asociativní pole
     * @throws StatementFailureException
     */
    public function get($id) {
        $select = $this->select("
            `article`.`id`,
            `article`.`menu_item_id_fk`,
            `article`.`article`,
            `article`.`template`,
            `article`.`editor`,
            `article`.`updated`
            ");
        $from = $this->from("`article`");
        $where = $this->where("`article`.`id` = :id");
        $touplesToBind = [':id' => $id];
        return $this->selectOne($select, $from, $where, $touplesToBind, true);
    }

    /**
     * Vrací jednu řádku tabulky 'article' ve formě asociativního pole podle cizího klíče s vazbou 1:1.
     *
     * @param string $menuItemIdFk Hodnota cizího klíče
     * @return array Asociativní pole
     * @throws StatementFailureException
     */
    public function getByFk($menuItemIdFk) {
        $select = $this->select("
            `article`.`id`,
            `article`.`menu_item_id_fk`,
            `article`.`article`,
            `article`.`template`,
            `article`.`editor`,
            `article`.`updated`
            ");
        $from = $this->from("`article`");
        $where = $this->where("
            `article`.`menu_item_id_fk` = :menu_item_id_fk
            ");
        $touplesToBind = [':menu_item_id_fk' => $menuItemIdFk];
        return $this->selectOne($select, $from, $where, $touplesToBind, true);
    }

    public function insert(RowDataInterface $rowData) {
        return $this->execInsert('article', $rowData);
    }

    public function update(RowDataInterface $rowData) {
        return $this->execUpdate('article', ['id'], $rowData);
    }

    public function delete(RowDataInterface $rowData) {
        return $this->execDelete('article', ['id'], $rowData);
    }
}

