<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Hydrator;

use Model\Hydrator\HydratorInterface;
use Model\Entity\EntityInterface;
use Model\RowData\RowDataInterface;

use Red\Model\Entity\PaperInterface;

/**
 * Description of PaperHydrator
 *
 * @author pes2704
 */
class PaperHydrator implements HydratorInterface {

    /**
     *
     * @param PaperInterface $paper
     * @param type $rowData
     */
    public function hydrate(EntityInterface $paper, RowDataInterface $rowData) {
        /** @var PaperInterface $paper */
        $paper
            ->setId($rowData->offsetGet('id'))
            ->setMenuItemIdFk($rowData->offsetGet('menu_item_id_fk'))
            ->setHeadline($rowData->offsetGet('headline'))
            ->setPerex($rowData->offsetGet('perex'))
            ->setTemplate($rowData->offsetGet('template'))
            ->setKeywords($rowData->offsetGet('keywords'))
            ->setEditor($rowData->offsetGet('editor'))
            ->setUpdated($rowData->offsetGet('updated') ? \DateTime::createFromFormat('Y-m-d H:i:s', $rowData->offsetGet('updated')) : NULL);
    }

    /**
     *
     * @param PaperInterface $paper
     * @param type $rowData
     */
    public function extract(EntityInterface $paper, RowDataInterface $rowData) {
        /** @var PaperInterface $paper */
        $rowData->offsetSet('id',  $paper->getId()); // id je autoincrement - readonly, hodnota pro where
        $rowData->offsetSet('menu_item_id_fk',  $paper->getMenuItemIdFk());
        $rowData->offsetSet('headline',  $paper->getHeadline());
        $rowData->offsetSet('perex',  $paper->getPerex());
        $rowData->offsetSet('template',  $paper->getTemplate());
        $rowData->offsetSet('keywords',  $paper->getKeywords());
        $rowData->offsetSet('editor',  $paper->getEditor());
        // updated je timestamp
        // id je autoincrement - readonly
    }

}
