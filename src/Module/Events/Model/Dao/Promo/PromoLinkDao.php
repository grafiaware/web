<?php

namespace Events\Model\Dao\Promo;

use Events\Model\Dao\Lottery\LotteryQueryTrait;
use Pes\Model\Dao\DaoEditAbstract;
use Pes\Model\Dao\DaoEditKeyDbVerifiedInterface;

/**
 * F2 (#47, docs/vp/zadani/F1.md): trvalý odkaz (tabulka promo_link), historie cíle (promo_link_target_log)
 * a kanály sociálních sítí z tabulky network. Dao + služba s poli (deklarovaná odchylka ADR 001 jako D1 a R1).
 */
class PromoLinkDao extends DaoEditAbstract implements DaoEditKeyDbVerifiedInterface {

    use LotteryQueryTrait;

    public function getPrimaryKeyAttributes(): array {
        return ['id'];
    }

    public function getAttributes(): array {
        return ['id', 'code', 'name', 'owner_type', 'company_id_fk', 'target', 'target_path', 'note', 'texts', 'active',
            'created', 'created_by', 'updated'];
    }

    public function getTableName(): string {
        return 'promo_link';
    }
}
