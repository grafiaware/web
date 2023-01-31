<?php
namespace Web\Component\ViewModel\Manage;

use Web\Component\ViewModel\StatusViewModelInterface;

/**
 *
 * @author pes2704
 */
interface LoginLogoutViewModelInterface {
    public function isUserLoggedIn(): bool;
}
