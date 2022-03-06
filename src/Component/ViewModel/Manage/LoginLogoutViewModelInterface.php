<?php
namespace Component\ViewModel\Manage;

use Component\ViewModel\StatusViewModelInterface;

/**
 *
 * @author pes2704
 */
interface LoginLogoutViewModelInterface {
    public function isUserLoggedIn(): bool;
}
