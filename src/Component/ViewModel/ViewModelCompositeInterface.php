<?php
namespace Component\ViewModel;

/**
 *
 * @author pes2704
 */
interface ViewModelCompositeInterface extends ViewModelInterface {
    public function provideData($name);
}
