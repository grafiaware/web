<?php

namespace Events\Model\Dao;

use Model\Dao\DaoEditInterface;
use Model\Dao\DaoReadonlyFkInterface;

/**
 *
 * @author pes2704
 */
interface VisitorJobRequestDaoInterface extends DaoEditInterface, DaoReadonlyFkInterface {
    public function findJobRequestsByJob(array $jobId): array;
    public function findJobRequestsByLogin(array $loginName): array;

}
