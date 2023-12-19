<?php

namespace Red\Model\Dao;

use Model\Dao\DaoEditAbstract;
use \Model\Dao\DaoAutoincrementTrait;

/**
 * Description of RsDao
 *
 * @author pes2704
 */
class MenuItemAssetDao extends DaoEditAbstract implements AssetDaoInterface {
    
    use DaoAutoincrementTrait;

    public function getAutoincrementFieldName() {
        return 'id';
    }
    
    public function getPrimaryKeyAttributes(): array {
        return ['id'];
    }

    public function getAttributes(): array {
        return ['id', 'filepath', 'mime_type', 'editor_login_name', 'created', 'updated'];
    }

    public function getTableName(): string {
        return 'menu_item_asset';
    }

    public function getByFilepath($filepath) {
        return $this->getUnique(['filepath'=>$filepath]);
    }    
}
