<?php
namespace Auth\Model\Hydrator;

use Model\Entity\EntityInterface;
use Model\Hydrator\HydratorInterface;
use ArrayAccess;

use Auth\Model\Entity\RoleInterface;

/**
 * Description of RoleHydrator
 *
 * @author vlse2610
 */
class RoleHydrator implements HydratorInterface {

    /**
     *
     * @param EntityInterface $role
     * @param ArrayAccess $rowData
     */
    public function hydrate(EntityInterface $role, ArrayAccess $rowData) {
        /** @var RoleInterface $role */
        $role->setRole($rowData->offsetGet('role'));
    }

    /**
     *
     * @param EntityInterface $role
     * @param ArrayAccess $rowData
     */
    public function extract(EntityInterface $role, ArrayAccess $rowData) {
        /** @var RoleInterface $role */
        $rowData->offsetSet('role', $role->getRole());
    }

}
