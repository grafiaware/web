<?php
namespace Component\ViewModel;

/**
 *
 * @author pes2704
 */
interface ViewModelListInterface {
    public function provideDataCollection(): iterable;
}
