<?php
namespace Model\Repository;

use Model\Entity\PersistableEntityInterface;
use Model\RowData\RowData;
use Model\RowData\RowDataInterface;

use Model\Repository\RepoAssotiatedOneInterface;  // použito jen v komentáři

use UnexpectedValueException;

/**
 * Trait s implementací RepoAssotiatedOneInterface interface pro POTOMKOVSKÉ repository s asociací 1:1
 *
 * @author pes2704
 */
trait RepoAssotiatedOneTrait {

    /** @var RepoAbstract $this */
    public function getByReference(string $referenceName, ...$referenceParams): ?PersistableEntityInterface {
        // vždy čte data - neví jestli jsou v $this->data
        $referenceKey = $this->createReferenceKey($referenceName, $referenceParams);
        $rowData = $this->dataManager->getByReference($referenceName, $referenceKey);
        return $this->addEntityByRowData($rowData);
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
     * @param RowDataInterface $parentRowData
     * @return PersistableEntityInterface|null
     */
    public function getByParentData(RowDataInterface $parentRowData): ?PersistableEntityInterface {
        $rowData = $this->createChildDataFromParentData($parentRowData->getArrayCopy());
        return $this->recreateEntityByRowData($rowData);
    }

    private function createChildDataFromParentData(array $parentTouples) {
        $childTouples = [];
        foreach ($this->dataManager->getAttributes() as $field) {
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
        return new RowData($childTouples);
    }
}
