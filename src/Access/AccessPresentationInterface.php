<?php
namespace Access;

/**
 *
 * @author pes2704
 */
interface AccessPresentationInterface {
    public function isAllowed($resource, $permissions, $action): bool;
}
