<?php

namespace Auth\Service;

use Container\EventsDbContainerConfigurator;
use Container\EventsModelContainerConfigurator;
use Events\Model\Dao\LoginDao as EventsLoginDao;
use Pes\Container\Container;
use Pes\Model\RowData\RowData;

/**
 * A6: zapíše login do events.login hned po založení účtu (jinak se tam dostane až po prvním přihlášení – Z1).
 * Stejné chování jako Events\Service\LoginService::setAddUserNameToEventsLogin, jen bez odloženého flush repository.
 * Až auth a events poběží na různých doménách (Z12, #68), nahradí se voláním API events.
 */
class EventsLoginWriter {

    private ?EventsLoginDao $dao = null;

    public function __invoke(string $loginName): void {
        $dao = $this->dao ??= (new EventsModelContainerConfigurator())->configure(
                (new EventsDbContainerConfigurator())->configure(new Container())
            )->get(EventsLoginDao::class);
        if ($dao->get(['login_name' => $loginName]) === null) {
            $row = new RowData();
            $row->import(['login_name' => $loginName, 'deleted_due_to_auth' => 0]);
            $dao->insert($row);
        }
    }
}
