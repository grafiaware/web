<?php
namespace Auth\Component\ViewModel;

use Component\ViewModel\ViewModelInterface;

/**
 *
 * @author pes2704
 */
interface LoginViewModelInterface extends ViewModelInterface {
    public function isUserLoggedIn(): bool;
}
