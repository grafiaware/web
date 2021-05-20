<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Hydrator;

use Model\Hydrator\HydratorInterface;

use Model\Entity\EntityInterface;
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
     * @param type $row
     */
    public function hydrate(EntityInterface $paperHeadline, &$row) {
        /** @var PaperInterface $paperHeadline */
        $paperHeadline
            ->setId($row['id'])
            ->setMenuItemIdFk($row['menu_item_id_fk'])
            ->setHeadline($row['headline'])
            ->setPerex($row['perex'])
            ->setTemplate($row['template'])
            ->setKeywords($row['keywords'])
            ->setEditor($row['editor'])
            ->setUpdated($row['updated'] ? \DateTime::createFromFormat('Y-m-d H:i:s', $row['updated']) : NULL);
    }

    /**
     *
     * @param PaperInterface $paperHeadline
     * @param type $row
     */
    public function extract(EntityInterface $paperHeadline, &$row) {
        /** @var PaperInterface $paperHeadline */
        $row['id'] = $paperHeadline->getId(); // id je autoincrement - readonly, hodnota pro where
        $row['menu_item_id_fk'] = $paperHeadline->getMenuItemIdFk();
        $row['headline'] = $paperHeadline->getHeadline();
        $row['perex'] = $paperHeadline->getPerex();
        $row['template'] = $paperHeadline->getTemplate();
        $row['keywords'] = $paperHeadline->getKeywords();
        $row['editor'] = $paperHeadline->getEditor();
        // updated je timestamp
        // id je autoincrement - readonly
    }

}
