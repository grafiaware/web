<?php

namespace Red\Component\ViewModel\Manage;

use Red\Component\ViewModel\ViewModelAbstract;
use Red\Component\ViewModel\StatusViewModelInterface;

use ArrayIterator;

/**
 * Description of LoginLogoutViewModel
 *
 * @author pes2704
 */
class LoginLogoutViewModel extends ViewModelAbstract implements LoginLogoutViewModelInterface {

    private $status;

    public function __construct(
            StatusViewModelInterface $status) {
        $this->status = $status;
    }

    public function isUserLoggedIn(): bool {
        $loginAggregate = $this->status->getUserLoginName();
        return isset($loginAggregate) ? true : false;
    }

    public function getIterator() {
        return new ArrayIterator([
            'userName' => $this->status->getUserLoginName()
        ]);
    }
}
