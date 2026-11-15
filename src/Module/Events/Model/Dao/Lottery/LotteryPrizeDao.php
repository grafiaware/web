<?php

namespace Events\Model\Dao\Lottery;

use Pes\Model\Dao\DaoEditAbstract;
use Pes\Model\Dao\DaoEditAutoincrementKeyInterface;
use Pes\Model\Dao\DaoAutoincrementTrait;

/**
 * D1 (#46): tabulka lottery_prize (migrace events 20261116000001_d1_create_lottery). Viz docs/vp/zadani/D1.md.
 */
class LotteryPrizeDao extends DaoEditAbstract implements DaoEditAutoincrementKeyInterface {

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
            'lottery_id_fk',
            'round_id_fk',
            'name',
            'quantity',
            'company_id_fk',
            'priority',
        ];
    }

    public function getTableName(): string {
        return 'lottery_prize';
    }
}
