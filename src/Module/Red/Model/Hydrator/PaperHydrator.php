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
     * @param PaperInterface $paperHeadline
     * @param type $rowData
     */
    public function hydrate(EntityInterface $paperHeadline, RowDataInterface $rowData) {
        /** @var PaperInterface $paperHeadline */
        $paperHeadline
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
     * @param PaperInterface $paperHeadline
     * @param type $rowData
     */
    public function extract(EntityInterface $paperHeadline, RowDataInterface $rowData) {
        /** @var PaperInterface $paperHeadline */
        $rowData->offsetSet('id',  $paperHeadline->getId()); // id je autoincrement - readonly, hodnota pro where
        $rowData->offsetSet('menu_item_id_fk',  $paperHeadline->getMenuItemIdFk());
        $rowData->offsetSet('headline',  $paperHeadline->getHeadline());
        $rowData->offsetSet('perex',  $paperHeadline->getPerex());
        $rowData->offsetSet('template',  $paperHeadline->getTemplate());
        $rowData->offsetSet('keywords',  $paperHeadline->getKeywords());
        $rowData->offsetSet('editor',  $paperHeadline->getEditor());
        // updated je timestamp
        // id je autoincrement - readonly
    }

}
