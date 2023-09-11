<?php

namespace Web\Middleware\Page\Prepare;

use Status\Model\Repository\StatusPresentationRepo;
use Status\Model\Repository\StatusSecurityRepo;
use Red\Model\Repository\LanguageRepo;
use Red\Service\ItemAction\ItemActionServiceInterface;

/**
 *
 * @author pes2704
 */
interface PrepareInterface {
    public function prepareStatus(
            StatusPresentationRepo $statusPresentationRepo, 
            LanguageRepo $languageRepo,
            StatusSecurityRepo $statusSecurityRepo,
            ItemActionServiceInterface $itemActionService);
}
