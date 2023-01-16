<?php
namespace Form;

/**
 *
 * @author pes2704
 */
interface EntityArrayCopyInterface {

    /**
     * Vrací asociativní pole hodnot získaných voláním všech metod getXXX (gettrů) entity. Klíče pole jsou pojmenvány podle
     * jména getteru, kterým byly získány tak, že je vynechán prefix get a prví písmeno převedeno na malé,
     *
     * jména getteru, kterým byly získány tak, že je vynechán prefix get a první písmeno převedeno na malé,
     *
     * @return array
     */
    public function getArrayCopy(): array;

}
