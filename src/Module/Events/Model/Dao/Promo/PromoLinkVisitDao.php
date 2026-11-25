<?php

namespace Events\Model\Dao\Promo;

use Events\Model\Dao\Lottery\LotteryQueryTrait;
use Pes\Model\Dao\DaoEditAbstract;
use Pes\Model\Dao\DaoEditKeyDbVerifiedInterface;

/**
 * F2 (#47): počítadlo návštěv odkazu – jen součty za den a kanál (tabulka promo_link_visit), žádné údaje o návštěvnících.
 */
class PromoLinkVisitDao extends DaoEditAbstract implements DaoEditKeyDbVerifiedInterface {

    use LotteryQueryTrait;

    public function getPrimaryKeyAttributes(): array {
        return ['link_id_fk', 'day', 'channel'];
    }

    public function getAttributes(): array {
        return ['link_id_fk', 'day', 'channel', 'visits'];
    }

    public function getTableName(): string {
        return 'promo_link_visit';
    }

    public function increment(int $linkId, string $day, string $channel): void {
        $this->exec("INSERT INTO promo_link_visit (link_id_fk, day, channel, visits) VALUES (:link, :day, :channel, 1)
                     ON DUPLICATE KEY UPDATE visits = visits + 1", ['link' => $linkId, 'day' => $day, 'channel' => $channel]);
    }
}
