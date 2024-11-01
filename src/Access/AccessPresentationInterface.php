<?php
namespace Access;

use Component\ViewModel\StatusViewModelInterface;
use Component\View\ComponentInterface;

/**
 *
 * @author pes2704
 */
interface AccessPresentationInterface {
//    public function getStatus(): StatusViewModelInterface;
    
    /**
     * 
     * @param type $resource
     * @param type $action
     * @return bool
     */
    public function isAllowed($resource, $action): bool;
}
