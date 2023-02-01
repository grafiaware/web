<?php
namespace Red\Component\ViewModel\Manage;

use Red\Component\ViewModel\StatusViewModelInterface;

/**
 *
 * @author pes2704
 */
interface LoginLogoutViewModelInterface {
    public function isUserLoggedIn(): bool;
}
