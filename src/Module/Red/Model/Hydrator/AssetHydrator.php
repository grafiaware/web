<?php

namespace Red\Model\Hydrator;

use Model\Entity\EntityInterface;
use Model\Hydrator\HydratorInterface;
use Model\Hydrator\TypeHydratorAbstract;

use Red\Model\Entity\AssetInterface;
use ArrayAccess;

/**
 * Description of PaperHydrator
 *
 * @author pes2704
 */
class AssetHydrator extends TypeHydratorAbstract implements HydratorInterface {
//        return ['id', 'filepath', 'mime_type', 'editor_login_name', 'created', 'updated']

    /**
     *
     * @param AssetInterface $asset
     * @param ArrayAccess $rowData
     */
    public function hydrate(EntityInterface $asset, ArrayAccess $rowData) {
        /** @var AssetInterface $asset */
        $asset
            ->setId( $this->getPhpValue($rowData,'id'))
            ->setFilepath($this->getPhpValue( $rowData,'filepath'))
            ->setMimeType($this->getPhpValue( $rowData,'mime_type'))
            ->setEditorLoginName( $this->getPhpValue( $rowData,'editor_login_name'))
            ->setCreated($this->getPhpDate( $rowData,'created'))
            ->setUpdated($this->getPhpDate( $rowData,'updated'))
            ;
    }

    /**
     *
     * @param AssetInterface $asset
     * @param ArrayAccess $rowData
     */
    public function extract(EntityInterface $asset, ArrayAccess $rowData) {
        /** @var AssetInterface $asset */
        $this->setSqlValue( $rowData, 'id', $asset->getId() );
        $this->setSqlValue( $rowData, 'filepath', $asset->getFilepath() );
        $this->setSqlValue( $rowData, 'mime_type', $asset->getMimeType() );
        $this->setSqlValue( $rowData, 'editor_login_name', $asset->getEditorLoginName() );
        // created je DEFAULT CURRENT_TIMESTAMP
        // updated je ON UPDATE CURRENT_TIMESTAMP

    }

}
