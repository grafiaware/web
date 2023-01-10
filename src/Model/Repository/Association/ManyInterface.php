<?php
namespace Model\Repository\Association;

/**
 *
 * @author pes2704
 */
interface ManyInterface extends AssociationInterface {

    public function addEntities(PersistableEntityInterface $parentEntity);

    public function removeEntities(PersistableEntityInterface $parentEntity);

}
