<?php

namespace Events\Model\Dao\Lottery;

use Pes\Model\Dao\DaoEditAbstract;
use Pes\Model\Dao\DaoEditKeyDbVerifiedInterface;

/**
 * D1 (#46): osobní kód návštěvníka pro QR (tabulka visitor_code).
 * Kód je identifikátor, ne tajný údaj – v DB čitelně (AGENTS.md, rozhodnutí 16. 11. 2026).
 */
class VisitorCodeDao extends DaoEditAbstract implements DaoEditKeyDbVerifiedInterface {

    use LotteryQueryTrait;

    public function getPrimaryKeyAttributes(): array {
        return ['login_login_name_fk'];
    }

    public function getAttributes(): array {
        return ['login_login_name_fk', 'code', 'created'];
    }

    public function getTableName(): string {
        return 'visitor_code';
    }
}
