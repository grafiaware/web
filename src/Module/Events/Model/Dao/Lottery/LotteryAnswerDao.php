<?php

namespace Events\Model\Dao\Lottery;

use Pes\Model\Dao\DaoEditAbstract;
use Pes\Model\Dao\DaoEditAutoincrementKeyInterface;
use Pes\Model\Dao\DaoAutoincrementTrait;

/**
 * D1 (#46): tabulka lottery_answer (migrace events 20261116000001_d1_create_lottery). Viz docs/vp/zadani/D1.md.
 */
class LotteryAnswerDao extends DaoEditAbstract implements DaoEditAutoincrementKeyInterface {

    use DaoAutoincrementTrait;
    use LotteryQueryTrait;

    public function getAutoincrementFieldName() {
        return 'id';
    }

    public function getPrimaryKeyAttributes(): array {
        return ['id'];
    }

    public function getAttributes(): array {
        return [
            'id',
            'question_id_fk',
            'login_login_name_fk',
            'answer',
            'created',
        ];
    }

    public function getTableName(): string {
        return 'lottery_answer';
    }
}
