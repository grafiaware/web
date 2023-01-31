<?php
namespace Access;

use Web\Component\ViewModel\StatusViewModelInterface;
use Web\Component\View\ComponentInterface;

/**
 *
 * @author pes2704
 */
interface AccessPresentationInterface {
    public function getStatus(): StatusViewModelInterface;
    public function isAllowed(ComponentInterface $resource, $action): bool;
}
