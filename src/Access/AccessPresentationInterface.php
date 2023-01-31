<?php
namespace Access;

use Red\Component\ViewModel\StatusViewModelInterface;
use Red\Component\View\ComponentInterface;

/**
 *
 * @author pes2704
 */
interface AccessPresentationInterface {
    public function getStatus(): StatusViewModelInterface;
    public function isAllowed(ComponentInterface $resource, $action): bool;
}
