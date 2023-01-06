<?php
namespace Model\Dao;

/**
 *
 * @author pes2704
 */
interface DaoReferenceUniqueInterface extends DaoWithReferenceInterface {

    public function getByReference($referenceName, array $referenceTouples);

}
