<?php
namespace Model\Repository;

use Model\Repository\Association\AssociationOneToManyInterface;
use Model\RowData\RowData;
use Model\RowData\RowDataInterface;

use Model\Repository\RepoAssotiatedManyInterface;  // použito jen v komentáři

/**
 * Trait s implementací RepoAssotiatedManyInterface interface pro POTOMKOVSKÉ repository s asociací 1:N
 *
 * @author pes2704
 */
trait RepoAssotiatedManyTrait {

    /**
     *
     * @param string $referenceName Jméno reference definované v DAO (obvykle jméno rodičovské tabulky)
     * @param ...$referenceParams
     * @return iterable
     */
    public function findByReference(string $referenceName, ...$referenceParams): iterable {
                $referenceKey = $this->createReferenceKey($referenceName, $referenceParams);
        $rowDataArray = $this->dataManager->findByReference($referenceName, $referenceKey);
        return $this->recreateEntitiesByRowDataArray($rowDataArray);
    }

    private function createReferenceKey($referenceName, array $referenceParams): array {
        /** @var DaoWithReferenceInterface $this->dataManager */
        $refAttribute = $this->dataManager->getReferenceAttributes($referenceName);
        $key = array_combine(array_keys($refAttribute), $referenceParams);
        if ($key===false) {
            $daoCls = $daoCls($this->dataManager);
            throw new UnexpectedValueException("Nelze vytvořit referenci pro volání Dao. Počet parametrů předaných metodě typu getByReference() neodpovídá počtu polí reference se jménem $referenceName v DAO $daoCls.");
        }
        return $key;
    }


    /**
     *
     * @param string $referenceName
     * @param RowDataInterface[] $parentTouplesArray parent data - dvourozměrné pole
     * @return PersistableEntityInterface|null
     */
    public function findByParentData(array $parentTouplesArray): ?PersistableEntityInterface {
        $rowDataArray = $this->createChildDataFromParentData($parentTouplesArray);
        return $this->recreateEntitiesByRowDataArray($rowDataArray);
    }

    private function createChildDataFromParentData(array $parentRowDataArray) {
        $childDataArray = [];
        $attributes = $this->dataManager->getAttributes();
        foreach ($parentRowDataArray as $parentRowData) {
            /** @var RowDataInterface $parentRowData */
            $parentTouples = $parentRowData->getArrayCopy();
            $childTouples = [];
            foreach ($attributes as $field) {
                if( ! array_key_exists($field, $parentTouples)) {
                    $daoCls = get_class($this->dataManager);
                    throw new UnexpectedValueException("Nelze vytvořit dvojice jméno/hodnota z rodičovských dat. Atributy potomka $daoCls obsahují pole '$field' a pole rodičovských dat pro vytvoření klíče neobsahuje prvek s takovým jménem.");
                }
                if (is_scalar($parentTouples[$field]) OR $parentTouples[$field]===null) {
                    $childTouples[$field] = $parentTouples[$field];
                } else {
                    $t = gettype($parentTouples[$field]);
                    throw new UnexpectedValueException("Nelze vytvořit dvojice jméno/hodnota z rodičovských dat. Rodičovská data obsahují v položce '$field' neskalární hodnotu. Hodnota v položce '$field' je typu '$t'.");
                }
            }
            $childDataArray[] = new RowData($childTouples);
        }

        return $childDataArray;
    }

}
