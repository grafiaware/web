<?php
namespace Component\ViewModel;

use Component\ViewModel\ViewModelInterface;
use Component\ViewModel\ViewModelListInterface;

/**
 *
 * @author pes2704
 */
interface ViewModelLimitedListInterface extends ViewModelListInterface {

    public function isItemCountUnderLimit(): bool;
}
