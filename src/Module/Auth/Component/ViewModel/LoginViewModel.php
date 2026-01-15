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
class LoginViewModel extends ViewModelAbstract implements LoginViewModelInterface {

    private $status;

    public function __construct(
            StatusViewModelInterface $status) {
        $this->status = $status;
    }

    public function isUserLoggedIn(): bool {
        $loginAggregate = $this->status->getUserLoginName();
        return isset($loginAggregate) ? true : false;
    }

    public function getIterator(): ArrayIterator {
        return new ArrayIterator([
            'userName' => $this->status->getUserLoginName()
        ]);
    }
}
