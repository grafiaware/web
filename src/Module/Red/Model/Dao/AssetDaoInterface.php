<?php
namespace Red\Model\Dao;

use Pes\Model\Dao\DaoEditAutoincrementKeyInterface;

/**
 *
 * @author pes2704
 */
interface AssetDaoInterface extends DaoEditAutoincrementKeyInterface {
    public function getByFilepath($filepath);
}
