<?php

namespace Events\Model\Dao\Promo;

use Events\Model\Dao\Lottery\LotteryQueryTrait;
use Pes\Model\Dao\DaoEditAbstract;
use Pes\Model\Dao\DaoEditKeyDbVerifiedInterface;

/**
 * F3 (#47, docs/vp/zadani/F1.md): QR kódy z QRdirectoru nahrané k odkazu jako vyčištěné SVG (tabulka promo_qr_svg).
 * Dao + služba s poli (deklarovaná odchylka ADR 001 jako D1, R1 a F2).
 */
class PromoQrSvgDao extends DaoEditAbstract implements DaoEditKeyDbVerifiedInterface {

    use LotteryQueryTrait;

    public function getPrimaryKeyAttributes(): array {
        return ['id'];
    }

    public function getAttributes(): array {
        return ['id', 'link_id_fk', 'channel', 'label', 'svg', 'decoded_url', 'created', 'created_by'];
    }

    public function getTableName(): string {
        return 'promo_qr_svg';
    }
}
