<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use Form\Hydrator;
use Form\EntityIterator;

use Model\Entity\EntityInterface;

class Entka implements EntityInterface {
    private $prvni;
    private $druhy;

    public function getPrvni() {
        return $this->prvni;
    }

    public function getDruhy() {
        return $this->druhy;
    }

    public function setPrvni($prvni) {
        $this->prvni = $prvni;
        return $this;
    }

    public function setDruhy($druhy) {
        $this->druhy = $druhy;
        return $this;
    }
}

/**
 * Description of RowDataTest
 *
 * @author pes2704
 */
class EntityIteratorTest extends TestCase {

    ############################
    #
    # test není unit, používí Hydrator
    #
    ############################

    public function testIterator() {
        $testArray = ['prvni'=>'raz', 'druhy'=>'dva'];
        $entity = new Entka();
        $entity->setPrvni('raz');
        $entity->setDruhy('dva');
        $hydrator = new Hydrator();
        $entityIterator = new EntityIterator($entity, $hydrator);
        foreach ($entityIterator as $key => $value) {
            $this->assertEquals($value, $testArray[$key]);
        }
        $array = iterator_to_array($entityIterator);
        $this->assertEquals($testArray, $array);
    }

    public function testGetArrayCopy() {
        $entity = new Entka();
        $entity->setPrvni('raz');
        $entity->setDruhy('dva');
        $hydrator = new Hydrator();
        $entityIterator = new EntityIterator($entity, $hydrator);
        $array = $entityIterator->getArrayCopy();
        $this->assertEquals(['prvni'=>'raz', 'druhy'=>'dva'], $array);
    }
}
