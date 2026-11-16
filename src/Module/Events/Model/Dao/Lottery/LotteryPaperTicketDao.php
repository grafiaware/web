<?php

namespace Events\Model\Dao\Lottery;

use Pes\Model\Dao\DaoEditAbstract;
use Pes\Model\Dao\DaoEditAutoincrementKeyInterface;
use Pes\Model\Dao\DaoAutoincrementTrait;

/**
 * D1c (#108): tabulka lottery_paper_ticket (migrace events 20261116000002_d1c_lottery_paper). Viz docs/vp/zadani/D1c.md.
 */
class LotteryPaperTicketDao extends DaoEditAbstract implements DaoEditAutoincrementKeyInterface {

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
            'number',
            'code',
            'created',
            'login_login_name_fk',
        ];
    }

    public function getTableName(): string {
        return 'lottery_paper_ticket';
    }
}
