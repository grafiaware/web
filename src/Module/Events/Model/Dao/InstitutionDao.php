<?php
namespace Events\Model\Dao;


use Pes\Database\Handler\HandlerInterface;

use Model\Dao\DaoTableAbstract;
use Model\Dao\DaoKeyDbVerifiedInterface;
use Model\RowData\RowDataInterface;


use Model\Dao\Exception\DaoForbiddenOperationException;

/**
 * Description of InstitutionDao
 *
 * @author vlse2610
 */
class InstitutionDao  extends DaoTableAbstract implements  DaoAutoincrementKeyInterface {

    use LastInsertIdTrait;

    /**
     * Vrací jednu řádku tabulky 'institution' ve formě asociativního pole podle primárního klíče.
     *
     * @param string $id Hodnota primárního klíče
     * @return array Asociativní pole
     * @throws StatementFailureException
     */
    public function get($id) {
        $select = $this->select("
            `institution`.`id`,
            `institution`.`name`,
            `institution`.`institution_type_id`
            ");
        $from = $this->from("`events`.`institution`");
        $where = $this->where("`institution`.`id` = :id");
        $touplesToBind = [':id' => $id];
        return $this->selectOne($select, $from, $where, $touplesToBind, true);
    }

    public function find($whereClause="", $touplesToBind=[]) {
        $select = $this->select("
            `institution`.`id`,
            `institution`.`name`,
            `institution`.`institution_type_id`
            ");
        $from = $this->from("`events`.`institution`");
        $where = $this->where($whereClause);
        return $this->selectMany($select, $from, $where, $touplesToBind);
    }

    public function insert(RowDataInterface $rowData) {
        return $this->execInsert('institution', $rowData);
    }

    public function update(RowDataInterface $rowData) {
        return $this->execUpdate('institution', ['id'], $rowData);
    }

    public function delete(RowDataInterface $rowData) {
        return $this->execDelete('institution', ['id'], $rowData);
    }
}
