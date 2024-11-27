<?php
namespace Component\ViewModel;

use Component\ViewModel\ViewModelInterface;

/**
 *
 * @author pes2704
 */
interface ViewModelListInterface extends ViewModelInterface {
    public function provideItemDataCollection(): iterable;
}
