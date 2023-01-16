<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Hydrator;

use Model\Hydrator\HydratorInterface;
use Model\Hydrator\TypeHydratorAbstract;

use Model\Entity\EntityInterface;
use ArrayAccess;

use Red\Model\Entity\MultipageInterface;

/**
 * Description of PaperHydrator
 *
 * @author pes2704
 */
class MultipageHydrator extends TypeHydratorAbstract implements HydratorInterface {

    /**
     *
     * @param MultipageInterface $multipage
     * @param type $rowData
     */
    public function hydrate(EntityInterface $multipage, ArrayAccess $rowData) {
        /** @var MultipageInterface $multipage */
        $multipage
            ->setId($this->getPhpValue($rowData,'id'))
            ->setMenuItemIdFk($this->getPhpValue($rowData,'menu_item_id_fk'))
            ->setTemplate($this->getPhpValue($rowData,'template'))
            ->setEditor($this->getPhpValue($rowData,'editor'))
            ->setUpdated($this->getPhpDatetime($rowData,'updated'));
    }

    /**
     *
     * @param MultipageInterface $multipage
     * @param type $rowData
     */
    public function extract(EntityInterface $multipage, ArrayAccess $rowData) {
        /** @var MultipageInterface $multipage */
        $this->setSqlValue($rowData, 'id', $multipage->getId()); // id je autoincrement - readonly, hodnota pro where
        $this->setSqlValue($rowData, 'menu_item_id_fk', $multipage->getMenuItemIdFk());
        $this->setSqlValue($rowData, 'template', $multipage->getTemplate());
        $this->setSqlValue($rowData, 'editor', $multipage->getEditor());
        // updated je timestamp
        // id je autoincrement - readonly
    }

}
