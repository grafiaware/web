<?php
namespace Model\Repository;

use Model\Entity\PersistableEntityInterface;
use Model\RowData\RowData;
use Model\RowData\RowDataInterface;

use Model\Repository\RepoJoinedOneInterface;  // použito jen v komentáři

use UnexpectedValueException;

/**
 * Trait s implementací RepoJoinedOneInterface interface pro POTOMKOVSKÉ repository s asociací 1:1
 *
 * @author pes2704
 */
trait RepoJoinedOneTrait {

    /**
     * Metoda vytvoří entitu v potomkovské repository be čtšní dat z úložiště. Potomkovskou entitu vytvoří z rodičovských dat.
     *
     * @param RowDataInterface $parentRowData Rodičovská data pro získání všech hodnot potomka
     * @return PersistableEntityInterface|null
     */
    public function recreateEntityByParentData(RowDataInterface $parentRowData): ?PersistableEntityInterface {
        $rowData = $this->createChildDataFromParentData($parentRowData->getArrayCopy());
        return $this->recreateEntityByRowData($rowData);
    }

    private function createChildDataFromParentData(array $parentTouples) {
        //TODO: parametr zaměň za RowData a aplikuj NominateFilter nastavený podle atributů
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
