<?php

namespace Events\Model\Dao\Lottery;

use Pes\Model\Dao\DaoEditAbstract;
use Pes\Model\Dao\DaoEditAutoincrementKeyInterface;
use Pes\Model\Dao\DaoAutoincrementTrait;

/**
 * D1 (#46): tabulka lottery_round (migrace events 20261116000001_d1_create_lottery). Viz docs/vp/zadani/D1.md.
 */
class LotteryRoundDao extends DaoEditAbstract implements DaoEditAutoincrementKeyInterface {

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
            'draw_at',
            'substitute',
            'status',
            'drawn',
            'drawn_by',
        ];
    }

    public function getTableName(): string {
        return 'lottery_round';
    }
}
