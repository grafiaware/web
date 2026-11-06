<?php

namespace Auth\Middleware\Login\Controler;

use Access\Enum\RoleEnum;
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
 * POST /auth/v1/admin/unconfirmed/clean – jen supervisor.
 */
class UnconfirmedRegistrationControler extends LoginControlerAbstract {

    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusFlashRepo $statusFlashRepo,
            StatusPresentationRepo $statusPresentationRepo,
            private UnconfirmedRegistrationCleaner $cleaner) {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo);
    }

    public static function days(): int {
        return max(1, (int) (ConfigurationCache::auth()['unconfirmedRegistrationDays'] ?? 14));
    }

    public function clean(ServerRequestInterface $request): ResponseInterface {
        $loginAggregate = $this->statusSecurityRepo->get()->getLoginAggregate();
        if (!isset($loginAggregate)) {
            return $this->createUnauthorizedResponse();
        }
        if ($loginAggregate->getCredentials()?->getRoleFk() !== RoleEnum::SUPERVISOR) {
            $this->addFlashMessage("Úklid registrací smí spustit jen supervisor.", FlashSeverityEnum::WARNING);
            return $this->redirectSeeLastGet($request);
        }
        $days = self::days();
        $deleted = $this->cleaner->clean($days, $loginAggregate->getLoginName());
        $this->addFlashMessage("Smazáno nepotvrzených registrací starších než {$days} dní: {$deleted}.", FlashSeverityEnum::SUCCESS);
        return $this->redirectSeeLastGet($request);
    }
}
