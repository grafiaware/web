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

    /**
     * Metoda získá potomkovskou entoty z potomkovského repository pomocí reference. Hodnoty polí reference naplní z rodičovských dat.
     *
     * @param string $referenceName Jméno refernce z DAO
     * @param RowDataInterface $parentRowData Rodičovská data pro získání hodnot polí reference.
     * @return PersistableEntityInterface|null
     */
    public function getByParentData(string $referenceName, RowDataInterface $parentRowData): ?PersistableEntityInterface {
        $referenceKey = $this->createReferenceKeyFromParentData($referenceName, $parentRowData);
        $rowData = $this->dataManager->getByReference($referenceName, $referenceKey);
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
            $childTouples[$field] = $parentRowData->offsetGet($field);
        }
        return $childTouples;
    }
}
