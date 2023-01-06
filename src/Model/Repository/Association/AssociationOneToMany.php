<?php
namespace Model\Repository\Association;

use Model\Repository\RepoAssotiatedManyInterface;
use Model\Entity\PersistableEntityInterface;
use Model\RowData\RowDataInterface;

use Model\Repository\Association\Exception\BadTypeOfIterableParameterMember;

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
    public function __construct(RepoAssotiatedManyInterface $childRepo) {
        $this->childRepo = $childRepo;
    }

    /**
     *
     * @param iterable $parentRowData iterable of RowDataInterface
     * @return array[PersistableEntityInterface]
     */
    protected function recreateChildren(iterable $parentRowData): array {
        return $this->recreateEntitiesByRowDataArray($parentRowData);
    }

    /**
     *
     * @param RowDataInterface[] $parentRowDataArray array of RowDataInterface
     * @return iterable
     */
    protected function findChildren(array $parentRowDataArray): iterable {
        return $this->childRepo->findByParentData($parentTouplesArray);
    }

    /**
     *
     * @param iterable $parentEntities PersistableEntityInterface
     * @return void
     */
    protected function addChildren(iterable $parentEntities): void {
        foreach ($parentEntities as $persistableEntity) {
            /** @var PersistableEntityInterface $persistableEntity */
            if (!$persistableEntity instanceof PersistableEntityInterface) {
                $cls = PersistableEntityInterface::class;
                throw new BadTypeOfIterableParameterMember("Prvky předaného iterable parametru metody musí být typu $cls");
            }
            if ($parentEntities->isPersisted()) {
                $this->childRepo->add($persistableEntity);
            }
        }
    }

    protected function removeChildren(iterable $parentEntities): void {
        foreach ($parentEntities as $persistableEntity) {
            /** @var PersistableEntityInterface $persistableEntity */
            if (!$persistableEntity instanceof PersistableEntityInterface) {
                $cls = PersistableEntityInterface::class;
                throw new BadTypeOfIterableParameterMember("Prvky předaného iterable parametru metody musí být typu $cls");
            }
            if ($persistableEntity->isPersisted()) {
                $this->childRepo->remove($persistableEntity);
            }
        }

    }
}
