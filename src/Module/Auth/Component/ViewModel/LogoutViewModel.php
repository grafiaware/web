<?php

namespace Auth\Component\ViewModel;

use Component\ViewModel\ViewModelAbstract;
use Component\ViewModel\StatusViewModelInterface;

use ArrayIterator;

/**
 * Description of LoginLogoutViewModel
 *
 * @author pes2704
 */
class LogoutViewModel extends ViewModelAbstract implements LogoutViewModelInterface {

    private $status;

    public function __construct(StatusViewModelInterface $status) {
        $this->status = $status;
    }

    public function getUserLoginName(): string {
        return $this->status->getUserLoginName() ?? '';
    }

    public function getUserRole(): string {
        return $this->status->getUserRole() ?? '';
    }

    public function getIterator(): ArrayIterator {
        return new ArrayIterator([
            'userName' => $this->getUserLoginName(),
            'userRole' => $this->getUserRole()
        ]);
    }
}
