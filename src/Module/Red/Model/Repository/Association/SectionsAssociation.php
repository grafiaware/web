<?php
namespace Red\Model\Repository\Association;

use Model\Repository\Association\AssociationOneToManyAbstract;
use Model\Repository\Association\AssociationOneToManyInterface;

use Red\Model\Entity\PaperAggregatePaperSectionInterface;
use Model\Entity\PersistableEntityInterface;
use Model\RowData\RowDataInterface;

use Red\Model\Dao\PaperSectionDao;

/**
 * Description of RegistrationAssociation
 *
 * @author pes2704
 */
class SectionsAssociation extends AssociationOneToManyAbstract implements AssociationOneToManyInterface {
    public function getReferenceName() {
        return PaperSectionDao::REFERENCE_PAPER;
    }

    public function hydrateChild(PersistableEntityInterface $parentEntity, &$childValue): void {
        /** @var PaperAggregatePaperSectionInterface $parentEntity */
        $parentEntity->setPaperSectionsArray($childValue);

    }

    public function extractChild(PersistableEntityInterface $parentEntity, &$childValue = null): void {
        /** @var PaperAggregatePaperSectionInterface $parentEntity */
        $childValue = $parentEntity->getPaperSectionsArray();
    }
}
