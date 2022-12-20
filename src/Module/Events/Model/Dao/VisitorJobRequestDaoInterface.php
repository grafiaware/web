<?php

namespace Events\Model\Dao;

use Model\Dao\DaoEditInterface;
use Model\Dao\DaoReferenceNonuniqueInterface;

/**
 *
 * @author pes2704
 */
interface VisitorJobRequestDaoInterface extends DaoEditInterface, DaoReferenceNonuniqueInterface {
    public function findJobRequestsByJob(array $jobId): array;
    public function findJobRequestsByLogin(array $loginName): array;

}
