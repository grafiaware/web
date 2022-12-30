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
     * @param array $childKeyAttribute Atribut cizího klíče, klíče který je referencí na data rodiče v úložišti dat. V databázi jde o jméno sloupce (sloupců) s cizím klíčem v potomkovské tabulce.
     * @param array $parentKeyAttribute Atribut vlastního klíče, klíče na který vede reference na data rodiče v úložišti dat. V databázi jde o jméno sloupce (sloupců) s vlastním (obvykle primárním)  klíčem v potomkovské tabulce.
     * @param RepoAssotiatedManyInterface $childRepo Repo pro získání, ukládání a mazání asociovaných entit.
     * @param HydratorInterface $childHydrator Hydrátor pro nastavení potomkovských entity do rodičovské entity a získání z rodičovské entity.
     */
    /**
     *
     * @param string $parentTable Jméno rodičovské tabulky (index
     * @param RepoAssotiatedManyInterface $childRepo Repo pro získání, ukládání a mazání asociovaných entit.
     * @param HydratorInterface $childHydrator Hydrátor pro nastavení potomkovských entity do rodičovské entity a získání z rodičovské entity.
     */
    public function __construct(string $parentTable, RepoAssotiatedManyInterface $childRepo, HydratorInterface $childHydrator) {
        parent::__construct($parentTable, $childHydrator);
        $this->childRepo = $childRepo;
    }

    /**
     *
     * @param RowDataInterface $parentRowData
     * @return PersistableEntityInterface|null
     */
    protected function findChildren(RowDataInterface $parentRowData): iterable {
        return $this->childRepo->findByReference($this->referenceName, $parentRowData->getArrayCopy());
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
                $this->childRepo->add($parentEntities);
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
