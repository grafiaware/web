<?php

namespace Events\Model\Dao\Lottery;

use Pes\Model\Dao\DaoEditAbstract;
use Pes\Model\Dao\DaoEditAutoincrementKeyInterface;
use Pes\Model\Dao\DaoAutoincrementTrait;

/**
 * D1 (#46): tabulka lottery_winner (migrace events 20261116000001_d1_create_lottery). Viz docs/vp/zadani/D1.md.
 */
class LotteryWinnerDao extends DaoEditAbstract implements DaoEditAutoincrementKeyInterface {

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
            'prize_id_fk',
            'login_login_name_fk',
            'replaces_id_fk',
            'drawn',
            'drawn_by',
            'status',
            'claimed',
            'claimed_by',
            'forfeited',
            'forfeited_by',
            'notified',
            'pickup_until',
        ];
    }

    public function getTableName(): string {
        return 'lottery_winner';
    }
}
