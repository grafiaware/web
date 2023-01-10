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
     * Metoda získá potomkovskou entoty z potomkovského repository pomocí reference. Hodnoty polí reference naplní z rodičovských dat.
     *
     * @param string $referenceName Jméno refernce z DAO
     * @param RowDataInterface $parentRowData Rodičovská data pro získání hodnot polí reference.
     * @return iterable
     */
    public function findByParentData(string $referenceName, RowDataInterface $parentRowData): iterable {
        $referenceKey = $this->createReferenceKeyFromParentData($referenceName, $parentRowData);
        $rowData = $this->dataManager->findByReference($referenceName, $referenceKey);
        return $this->recreateEntityByRowData($rowData);
    }

    private function createReferenceKeyFromParentData(string $referenceName, RowDataInterface $parentRowData) {
        //TODO: aplikuj NominateFilter nastavený podle atributů
        /** @var DaoWithReferenceInterface $this->dataManager */
        $refAttribute = $this->dataManager->getReferenceAttributes($referenceName);
        $childTouples = [];
        foreach ($refAttribute as $field) {
            if( ! $parentRowData->offsetExists($field)) {
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
