<?php

namespace Auth\Middleware\Login\Controler;

use Auth\Service\DisplayNameService;
use Pes\Http\Request\RequestParams;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Status\Model\Entity\SecurityInterface;
use Status\Model\Enum\FlashSeverityEnum;
use Status\Model\Repository\StatusFlashRepo;
use Status\Model\Repository\StatusPresentationRepo;
use Status\Model\Repository\StatusSecurityRepo;

/**
 * A8: osobní jméno přihlášeného uživatele (docs/vp/zadani/A8.md).
 *
 * POST /auth/v1/displayname – pole displayName; prázdné jméno smaže.
 */
class DisplayNameControler extends LoginControlerAbstract {

    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusFlashRepo $statusFlashRepo,
            StatusPresentationRepo $statusPresentationRepo,
            private DisplayNameService $displayNameService) {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo);
    }

    public function change(ServerRequestInterface $request): ResponseInterface {
        $security = $this->statusSecurityRepo->get();
        $loginAggregate = $security->getLoginAggregate();
        if (!isset($loginAggregate)) {
            return $this->createUnauthorizedResponse();
        }
        $raw = (string) (new RequestParams())->getParsedBodyParam($request, 'displayName', '');
        $saved = $this->displayNameService->set($loginAggregate->getLoginName(), $raw);
        $security->setInfo(SecurityInterface::INFO_DISPLAY_NAME, $saved);
        if ($saved === null) {
            $this->addFlashMessage("Jméno jsme odebrali, zobrazuje se přihlašovací jméno.", FlashSeverityEnum::SUCCESS);
        } else {
            $this->addFlashMessage("Jméno jsme uložili.", FlashSeverityEnum::SUCCESS);
        }
        return $this->redirectSeeLastGet($request);
    }
}
