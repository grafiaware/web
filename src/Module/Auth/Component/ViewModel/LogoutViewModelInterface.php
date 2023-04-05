<?php
namespace Auth\Component\ViewModel;

use Component\ViewModel\ViewModelInterface;

/**
 *
 * @author pes2704
 */
interface LogoutViewModelInterface extends ViewModelInterface {
    public function getUserLoginName(): string;
    public function getUserRole(): string;
}
