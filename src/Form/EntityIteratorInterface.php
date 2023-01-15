<?php
namespace Form;

use IteratorAggregate;
use Traversable;

/**
 *
 * @author pes2704
 */
interface EntityIteratorInterface extends IteratorAggregate {

    /**
     * Vrací iterátor. Hodnoty poskytované iterátorem jsou získané voláním všech metod getXXX (gettrů) entity.
     * Klíče iterátoru jsou pojmenvány podle jména getteru, kterým byly získány tak, že je vynechán prefix get a první písmeno převedeno na malé,
     *
     * Např. z návratové hosnoty "Adam" vracené metodou entity getName() vznikne ["name"=>"adam"]
     *
     * @return Traversable
     */
    public function getIterator(): Traversable;

}
