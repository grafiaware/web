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

use Red\Model\Entity\StaticItemInterface;

/**
 * Description of StaticHydrator
 *
 * @author pes2704
 */
class StaticItemHydrator extends TypeHydratorAbstract implements HydratorInterface {

    /**
     *
     * @param StaticItemInterface $static
     * @param type $rowData
     */
    public function hydrate(EntityInterface $static, ArrayAccess $rowData) {
        /** @var StaticItemInterface $static */
        $static
            ->setId($this->getPhpValue($rowData,'id'))
            ->setMenuItemIdFk($this->getPhpValue($rowData,'menu_item_id_fk'))
            ->setPath($this->getPhpValue($rowData,'path'))
            ->setTemplate($this->getPhpValue($rowData,'template'))
            ->setCreator($this->getPhpValue($rowData,'creator'))
            ->setUpdated($this->getPhpDatetime($rowData,'updated'));
    }

    /**
     *
     * @param StaticItemInterface $static
     * @param type $rowData
     */
    public function extract(EntityInterface $static, ArrayAccess $rowData) {
        /** @var StaticItemInterface $static */
        $this->setSqlValue($rowData, 'id',  $static->getId()); // id je autoincrement - readonly, hodnota pro where
        $this->setSqlValue($rowData, 'menu_item_id_fk',  $static->getMenuItemIdFk());
        $this->setSqlValue($rowData, 'path',  $static->getPath());
        $this->setSqlValue($rowData, 'template',  $static->getTemplate());
        $this->setSqlValue($rowData, 'creator',  $static->getCreator());
        // updated je timestamp
        // id je autoincrement - readonly
    }

}
