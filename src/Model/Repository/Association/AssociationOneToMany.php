<?php
namespace Model\Repository\Association;

use Model\Repository\RepoAssotiatedManyInterface;
use Model\Entity\PersistableEntityInterface;
use Model\RowData\RowDataInterface;

/**
 * Description of AssociationOneToManyFactory
 *
 * @author pes2704
 */
class AssociationOneToMany extends AssociationAbstract implements AssociationOneToManyInterface {

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
    public function __construct(string $parentTable, RepoAssotiatedManyInterface $childRepo, RowHydratorInterface $childHydrator) {
        parent::__construct($parentTable, $childHydrator);
        $this->childRepo = $childRepo;
    }

    public function getAllAssociatedEntities(&$row): iterable {
        $childKey = $this->getReferenceKey($row);
        $children = $this->childRepo->findByReference($childKey);
//        if (!$children) {
//            throw new UnableToCreateAssotiatedChildEntity("Nelze vytvořit asociované entity pro vlastnost rodiče '$this->parentPropertyName'. Nebyla načtena entita.");
//        }
        return $children;
    }
}
