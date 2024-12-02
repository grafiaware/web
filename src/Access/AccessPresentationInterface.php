<?php
namespace Access;

use Component\ViewModel\StatusViewModelInterface;
use Component\View\ComponentInterface;

/**
 *
 * @author pes2704
 */
interface AccessPresentationInterface {
    
    /**
     * 
     * @param string $resource
     * @return bool
     */
    public function hasAnyPermission($resource): bool;
    
    /**
     * 
     * @param string $resource
     * @param string $action
     * @return bool
     */
    public function isAllowed($resource, $action): bool;
}
