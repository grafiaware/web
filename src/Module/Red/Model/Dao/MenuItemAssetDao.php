<?php

namespace Red\Model\Dao;

use Model\Dao\DaoEditAbstract;
use Model\Dao\DaoReferenceUniqueTrait;

/**
 * Description of RsDao
 *
 * @author pes2704
 */
class MenuItemAssetDao extends DaoEditAbstract implements MenuItemAssetDaoInterface {

    const REFERENCE_MENU_ITEM = 'menu_item';

//    use DaoReferenceNonuniqueTrait;
    use DaoReferenceUniqueTrait;

    public function getPrimaryKeyAttributes(): array {
        return ['menu_item_id_FK', 'filepath'];
    }

    public function getAttributes(): array {
        return ['menu_item_id_FK', 'filepath', 'mime_type', 'editor_login_name', 'created', 'updated'];
    }

    public function getReferenceAttributes($referenceName): array {
        return [
        self::REFERENCE_MENU_ITEM=>['menu_item_id_FK'=>'id']
        ][$referenceName];
    }

    public function getTableName(): string {
        return 'menu_item_asset';
    }

    public function findByMenuItemIdFk(array $menuItemIdFk) {
        return $this->findByFk('menu_item_id_FK', $menuItemIdFk);
    }
}
