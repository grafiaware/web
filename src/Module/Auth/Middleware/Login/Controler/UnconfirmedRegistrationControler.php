<?php

namespace Auth\Middleware\Login\Controler;

use Auth\Service\RoleGrantPolicy;
use Auth\Service\UnconfirmedRegistrationCleaner;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Site\ConfigurationCache;
use Status\Model\Enum\FlashSeverityEnum;
use Status\Model\Repository\StatusFlashRepo;
use Status\Model\Repository\StatusPresentationRepo;
use Status\Model\Repository\StatusSecurityRepo;

/**
 * A3: ruční úklid nepotvrzených registrací z administrace účtů (docs/vp/zadani/A3.md).
 *
 * POST /auth/v1/admin/unconfirmed/clean – kdo smí, určuje RoleGrantPolicy::canCleanUnconfirmed()
 * (bez přepínače A9 supervisor, s ním global_role_grantor a auth_role_grantor).
 */
class UnconfirmedRegistrationControler extends LoginControlerAbstract {

    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusFlashRepo $statusFlashRepo,
            StatusPresentationRepo $statusPresentationRepo,
            private UnconfirmedRegistrationCleaner $cleaner,
            private ?RoleGrantPolicy $policy = null) {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo);
    }

    private function policy(): RoleGrantPolicy {
        return $this->policy ??= RoleGrantPolicy::fromConfiguration();
    }

    public static function days(): int {
        return max(1, (int) (ConfigurationCache::auth()['unconfirmedRegistrationDays'] ?? 14));
    }

    public function clean(ServerRequestInterface $request): ResponseInterface {
        $loginAggregate = $this->statusSecurityRepo->get()->getLoginAggregate();
        if (!isset($loginAggregate)) {
            return $this->createUnauthorizedResponse();
        }
        if (!$this->policy()->canCleanUnconfirmed($loginAggregate->getCredentials()?->getRoleFk())) {
            $this->addFlashMessage("Nemáte oprávnění spustit úklid registrací.", FlashSeverityEnum::WARNING);
            return $this->redirectSeeLastGet($request);
        }
        $days = self::days();
        $deleted = $this->cleaner->clean($days, $loginAggregate->getLoginName());
        $this->addFlashMessage("Smazáno nepotvrzených registrací starších než {$days} dní: {$deleted}.", FlashSeverityEnum::SUCCESS);
        return $this->redirectSeeLastGet($request);
    }
}
