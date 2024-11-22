<?php
namespace Component\ViewModel;

/**
 *
 * @author pes2704
 */
interface ViewModelCollectionInterface extends ViewModelInterface {
    public function provideDataCollection(): iterable;
}
