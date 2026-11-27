<?php

namespace Events\Component\ViewModel\Data;

use Access\Enum\RoleEnum;
use Component\ViewModel\StatusViewModelInterface;
use Events\Model\Repository\JobRepoInterface;
use Events\Model\Repository\RepresentativeRepoInterface;

/**
 * #171: kdo smí vidět zájmy o pozici (visitor_job_request) – jméno, e-mail, telefon, vzdělání, praxe.
 *
 * - všechny zájmy o pozici: zástupce firmy, které pozice patří (tabulka representative), a events_administrator;
 * - jeden zájem: navíc návštěvník, který ho vytvořil.
 * Ostatní (nepřihlášení, jiní návštěvníci, zástupci jiných firem) nic.
 *
 * Používají JobFamilyJobRequestViewModel a JobFamilyJobRequestListViewModel; třída musí mít $status, $jobRepo a $representativeRepo.
 *
 * @property StatusViewModelInterface $status
 * @property JobRepoInterface $jobRepo
 * @property RepresentativeRepoInterface $representativeRepo
 */
trait JobRequestAccessTrait {

    /** Smí přihlášený vidět všechny zájmy o pozici $jobId? */
    protected function canSeeAllJobRequests($jobId): bool {
        $role = $this->status->getUserRole();
        if ($role === RoleEnum::EVENTS_ADMINISTRATOR) {
            return true;
        }
        $loginName = $this->status->getUserLoginName();
        if ($role !== RoleEnum::REPRESENTATIVE || !isset($loginName) || !isset($jobId)) {
            return false;
        }
        $job = $this->jobRepo->get($jobId);
        return isset($job) && $this->representativeRepo->get($loginName, $job->getCompanyId()) !== null;
    }

    /** Smí přihlášený vidět zájem návštěvníka $requestLoginName o pozici $jobId? */
    protected function canSeeJobRequest($jobId, $requestLoginName): bool {
        $loginName = $this->status->getUserLoginName();
        if ($this->status->getUserRole() === RoleEnum::VISITOR && isset($loginName) && $loginName === $requestLoginName) {
            return true;
        }
        return $this->canSeeAllJobRequests($jobId);
    }
}
