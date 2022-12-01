<?php

namespace Red\Model\Hydrator;

use Model\Entity\EntityInterface;
use Model\Hydrator\HydratorInterface;
use Model\RowData\RowDataInterface;
use Model\Hydrator\TypeHydratorAbstract;

use Red\Model\Entity\MenuItemAssetInterface;

/**
 * Description of PaperHydrator
 *
 * @author pes2704
 */
class MenuItemAssetHydrator extends TypeHydratorAbstract implements HydratorInterface {
//        return ['id', 'menu_item_id_FK', 'filepath', 'editor_login_name', 'created'];

    /**
     *
     * @param MenuItemAssetInterface $menuItemAsset
     * @param type $rowData
     */
    public function hydrate(EntityInterface $menuItemAsset, RowDataInterface $rowData) {
        /** @var MenuItemAssetInterface $menuItemAsset */
        $menuItemAsset
            ->setId( $this->getPhpValue( $rowData,'id') )
            ->setMenuItemIdFk( $this->getPhpValue( $rowData,'menu_item_id_FK') )
            ->setFilepath( $this->getPhpValue( $rowData,'filepath') )
            ->setMimeType($this->getPhpValue( $rowData,'mime_type'))
            ->setEditorLoginName( $this->getPhpValue( $rowData,'editor_login_name') )
            ->setCreated( $this->getPhpValue( $rowData,'created') )
            ->setUpdated( $this->getPhpValue( $rowData,'updated') )
            ;
    }

    /**
     *
     * @param MenuItemAssetInterface $menuItemAsset
     * @param type $rowData
     */
    public function extract(EntityInterface $menuItemAsset, RowDataInterface $rowData) {
        /** @var MenuItemAssetInterface $menuItemAsset */
        // id je autoincrement
        $this->setSqlValue( $rowData, 'menu_item_id_FK', $menuItemAsset->getMenuItemIdFk() );
        $this->setSqlValue( $rowData, 'filepath', $menuItemAsset->getFilepath() );
        $this->setSqlValue( $rowData, 'mime_type', $menuItemAsset->getMimeType() );
        $this->setSqlValue( $rowData, 'editor_login_name', $menuItemAsset->getEditorLoginName() );
        // created je DEFAULT CURRENT_TIMESTAMP
        // updated je ON UPDATE CURRENT_TIMESTAMP

    }

}
