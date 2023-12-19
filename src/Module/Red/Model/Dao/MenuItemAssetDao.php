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
    const REFERENCE_ASSET = 'asset';

//    use DaoReferenceNonuniqueTrait;
    use DaoReferenceUniqueTrait;

    public function getPrimaryKeyAttributes(): array {
        return ['menu_item_id_fk', 'asset_id_fk'];
    }

    public function getAttributes(): array {
        return ['menu_item_id_fk', 'asset_id_fk'];
    }

    public function getReferenceAttributes($referenceName): array {
        return [
        self::REFERENCE_MENU_ITEM=>['menu_item_id_FK'=>'id'],
        self::REFERENCE_ASSET=>['asset_id_fk'=>'id']
        ][$referenceName];
    }

    public function getTableName(): string {
        return 'menu_item_asset';
    }

    public function findByMenuItemIdFk(array $menuItemIdFk) {
        return $this->findByFk('menu_item_id_fk', $menuItemIdFk);
    }

    public function findByAssetIdFk(array $assetIdFk) {
        return $this->findByFk('asset_id_fk', $assetIdFk);
    }
    
}
