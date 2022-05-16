<?php

namespace Events\Model\Dao;

use Model\Dao\DaoEditInterface;
use Model\Dao\DaoFkNonuniqueInterface;

/**
 *
 * @author pes2704
 */
interface VisitorJobRequestDaoInterface extends DaoEditInterface, DaoFkNonuniqueInterface {
    public function findJobRequestsByJob(array $jobId): array;
    public function findJobRequestsByLogin(array $loginName): array;

}
