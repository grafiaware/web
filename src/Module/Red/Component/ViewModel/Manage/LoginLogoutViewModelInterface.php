<?php
namespace Red\Component\ViewModel\Manage;

use Red\Component\ViewModel\ViewModelInterface;

/**
 *
 * @author pes2704
 */
interface LoginLogoutViewModelInterface extends ViewModelInterface {
    public function isUserLoggedIn(): bool;
}
