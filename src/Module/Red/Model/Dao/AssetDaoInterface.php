<?php
namespace Red\Model\Dao;

use Model\Dao\DaoEditAutoincrementKeyInterface;

/**
 *
 * @author pes2704
 */
interface AssetDaoInterface extends DaoEditAutoincrementKeyInterface {
    public function getByFilepath($filepath);
}
