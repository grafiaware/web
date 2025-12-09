<?php
namespace Red\Model\Repository\Association;

//use Model\Repository\Association\AssociationWithJoinOneToOneAbstract;
use Model\Repository\Association\AssociationOneToOneAbstract;
use Model\Repository\Association\AssociationOneToOneInterface;


use Red\Model\Entity\MenuItemAggregatePaper;
use Model\Entity\PersistableEntityInterface;
use Model\RowData\RowDataInterface;

use Red\Model\Dao\PaperDao;

/**
 * Description of PaperAssociation
 *
 * @author pes2704
 */
class PaperAssociation extends AssociationOneToOneAbstract implements AssociationOneToOneInterface {

    public function getReferenceName() {
        return PaperDao::REFERENCE_MENU_ITEM;
    }
    
    public function hydrateChild(PersistableEntityInterface $parentEntity, &$childValue): void {
        /** @var MenuItemAggregatePaper $parentEntity */
        $parentEntity->setPaper($childValue);
    }

    public function extractChild(PersistableEntityInterface $parentEntity, &$childValue = null): void {
        /** @var MenuItemAggregatePaper $parentEntity */
        $childValue = $parentEntity->getPaper();

    }
}
