<?php
declare(strict_types=1);
namespace Tests\Unit\Form\EntityArrayCopyTest;

use PHPUnit\Framework\TestCase;

use Form\Hydrator;
use Form\EntityArrayCopy;

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
class EntityArrayCopyTest extends TestCase {

    ############################
    #
    # test není unit, používá Hydrator
    #
    ############################

    public function testGetArrayCopy() {
        $entity = new Entka();
        $entity->setPrvni('raz');
        $entity->setDruhy('dva');
        $hydrator = new Hydrator();
        $entityArrayCopy = new EntityArrayCopy($entity, $hydrator);
        $array = $entityArrayCopy->getArrayCopy();
        $this->assertEquals(['prvni'=>'raz', 'druhy'=>'dva'], $array);
    }
}
