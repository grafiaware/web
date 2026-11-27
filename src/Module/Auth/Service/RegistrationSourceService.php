<?php

namespace Auth\Service;

use Auth\Model\Dao\RegistrationSourceDao;
use Auth\Service\Source\VisitSource;

/**
 * F1 (#47, docs/vp/zadani/F1.md): zápis zdroje k registraci a souhrny pro přehled odkazů (modul Events čte přes službu,
 * až Z12 přes API).
 */
class RegistrationSourceService {

    public function __construct(private RegistrationSourceDao $dao) {
    }

    public function record(string $loginName, VisitSource $source): void {
        $this->dao->insertIgnore($loginName, [
            'link_code' => $source->linkCode,
            'channel' => $source->linkCode !== null ? ($source->channel ?? null) : null,
            'utm_source' => $source->utmSource,
            'utm_medium' => $source->utmMedium,
            'utm_campaign' => $source->utmCampaign,
            'landing_at' => $source->landingAt,
        ]);
    }

    /** Řádky přehledu: zdroj => počty. Období [od, do) jako 'Y-m-d H:i:s' nebo null. */
    public function counts(?string $from = null, ?string $to = null): array {
        return $this->dao->countsBySource($from, $to);
    }
}
