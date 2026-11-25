<?php

namespace Events\Service\Promo;

use Auth\Service\RegistrationSourceService;
use Container\AuthContainerConfigurator;
use Container\AuthDbContainerConfigurator;
use Pes\Container\Container;

/**
 * F1 (#47): registrace podle zdroje jsou v DB auth (registration_source). Přehled odkazů je čte přes službu auth
 * jako AuthLookup v D1; až auth a events poběží na různých doménách (Z12, #68), nahradí se voláním API auth.
 */
class PromoAuthLookup {

    private ?RegistrationSourceService $service = null;

    private function service(): RegistrationSourceService {
        return $this->service ??= (new AuthContainerConfigurator())->configure(
                (new AuthDbContainerConfigurator())->configure(new Container())
            )->get(RegistrationSourceService::class);
    }

    /** Období [od, do) jako 'Y-m-d H:i:s' nebo null. */
    public function registrationCounts(?string $from, ?string $to): array {
        return $this->service()->counts($from, $to);
    }
}
