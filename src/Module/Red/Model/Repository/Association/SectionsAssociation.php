<?php
namespace Red\Model\Repository\Association;

use Model\Repository\Association\AssociationOneToMany;
use Model\Repository\Association\AssociationOneToManyInterface;

use Red\Model\Entity\PaperAggregatePaperSectionInterface;
use Model\Entity\PersistableEntityInterface;
use Model\RowData\RowDataInterface;

/**
 * Description of RegistrationAssociation
 *
 * @author pes2704
 */
class SectionsAssociation extends AssociationOneToMany implements AssociationOneToManyInterface {

   /**
    *
    * @param PaperAggregatePaperSectionInterface $parentEntity
    * @param RowDataInterface $parentRowData
    * @return void
    */
    public function recreateEntities(PersistableEntityInterface $parentEntity, RowDataInterface $parentRowData): void {
        /** @var PaperAggregatePaperSectionInterface $parentEntity */
        $parentEntity->exchangePaperSectionsArray($this->recreateChildren($parentRowData));
    }

   /**
    * Přidá Section entity k rodičovské entitě Paper. Entity Section přidá seřatené podle priority.
    *
    * @param PersistableEntityInterface $parentEntity
    */
    public function addEntities(PersistableEntityInterface $parentEntity) {
        /** @var PaperAggregatePaperSectionInterface $parentEntity */
        $this->addChildren($parentEntity->getPaperSectionsArraySorted());
    }

   /**
    *
    * @param PersistableEntityInterface $parentEntity
    */
    public function removeEntities(PersistableEntityInterface $parentEntity) {
        /** @var PaperAggregatePaperSectionInterface $parentEntity */
        $this->removeChildren($parentEntity->getPaperContentsArray());
    }
}
