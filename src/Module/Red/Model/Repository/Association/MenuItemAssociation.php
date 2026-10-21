<?php
namespace Red\Model\Repository\Association;

use Pes\Model\Repository\Association\AssociationWithJoinOneToOneAbstract;
use Pes\Model\Repository\Association\AssociationOneToOneInterface;

use Red\Model\Entity\HierarchyAggregateInterface;
use Pes\Model\Entity\PersistableEntityInterface;
use Pes\Model\RowData\RowDataInterface;

use Red\Model\Dao\MenuItemDao;

/**
 * Description of RegistrationAssociation
 *
 * @author pes2704
 */
class MenuItemAssociation extends AssociationWithJoinOneToOneAbstract implements AssociationOneToOneInterface {

    public function getReferenceName() {
        return MenuItemDao::REFERENCE_PRIMARY;  // NESMYSL - nefukční jen dočasně pro vyhovění interfacu
    }
    public function hydrateChild(PersistableEntityInterface $parentEntity, &$childValue): void {
        /** @var HierarchyAggregateInterface $parentEntity */
        $parentEntity->setMenuItem($childValue);
    }

    public function extractChild(PersistableEntityInterface $parentEntity, &$childValue = null): void {
        /** @var HierarchyAggregateInterface $parentEntity */
        $childValue = $parentEntity->getMenuItem();

    }
}
