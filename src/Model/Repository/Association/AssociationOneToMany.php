<?php
namespace Model\Repository\Association;

use Model\Repository\RepoAssotiatedManyInterface;
use Model\Entity\PersistableEntityInterface;
use Model\RowData\RowDataInterface;

use Model\Repository\Association\Exception\BadTypeOfIterableParameterMember;
use UnexpectedValueException;

/**
 * Description of AssociationOneToManyFactory
 *
 * @author pes2704
 */
abstract class AssociationOneToMany extends AssociationAbstract implements AssociationOneToManyInterface {

    /**
     * @var RepoAssotiatedManyInterface
     */
    protected $childRepo;

    /**
     *
     * @param RepoAssotiatedManyInterface $childRepo Repo pro získání, ukládání a mazání asociovaných entit.
     */
    public function __construct( RepoAssotiatedManyInterface $childRepo) {
        $this->childRepo = $childRepo;
    }

    /**
     * Metoda získá pole potomkovských entit z potomkovského repository pomocí metody findByReference(). Hodnoty polí reference naplní z rodičovských dat.
     *
     *
     * @param PersistableEntityInterface[] $parentEntities
     * @param RowDataInterface[] $parentRowdataArray
     * @return void
     */
    public function recreateChildEntities(array $parentEntities, array $parentRowdataArray): void {
        if (count($parentEntities)!=count($parentRowdataArray)) {
            throw new UnexpectedValueException("Pole rodičovských entit má jiný počet prvků než pole rodičovských dat.");
        }
        foreach ($parentEntities as $key => $parentEntity) {
            $parentRowData = $parentRowdataArray[$key];
            $childEntities = $this->childRepo->findByParentData($referenceName, $parentRowData);
            $this->hydrateChild($parentEntity, $childEntities);
        }


    }

    /**
     *
     * @param iterable $parentEntities PersistableEntityInterface
     * @return void
     */
    public function addEntities(iterable $parentEntities): void {
        foreach ($parentEntities as $parentEntity) {
            /** @var PersistableEntityInterface $parentEntity */
            if (!$parentEntity instanceof PersistableEntityInterface) {
                $cls = PersistableEntityInterface::class;
                throw new BadTypeOfIterableParameterMember("Prvky předaného iterable parametru metody musí být typu $cls");
            }
            $this->addChild($parentEntity);
        }
    }

    public function removeEntities(iterable $parentEntities): void {
        foreach ($parentEntities as $parentEntity) {
            /** @var PersistableEntityInterface $parentEntity */
            if (!$parentEntity instanceof PersistableEntityInterface) {
                $cls = PersistableEntityInterface::class;
                throw new BadTypeOfIterableParameterMember("Prvky předaného iterable parametru metody musí být typu $cls");
            }
            $this->removeChild($parentEntity);
        }

    }
}
