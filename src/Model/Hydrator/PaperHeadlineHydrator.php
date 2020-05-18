<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Hydrator;

use Model\Entity\EntityInterface;
use Model\Entity\PaperInterface;

/**
 * Description of PaperHydrator
 *
 * @author pes2704
 */
class PaperHeadlineHydrator implements HydratorInterface {

    /**
     *
     * @param PaperInterface $paper
     * @param type $row
     */
    public function hydrate(EntityInterface $paper, &$row) {
        /** @var PaperInterface $paper */
        $paper
            ->setMenuItemIdFk($row['menu_item_id_fk'])
            ->setHeadline($row['headline'])
            ->setKeywords($row['keywords'])
            ->setEditor($row['editor'])
            ->setUpdated($row['updated'] ? \DateTime::createFromFormat('Y-m-d H:i:s', $row['updated']) : NULL);
    }

    /**
     *
     * @param PaperInterface $paper
     * @param type $row
     */
    public function extract(EntityInterface $paper, &$row) {
        /** @var PaperInterface $paper */
        $row['menu_item_id_fk'] = $paper->getMenuItemIdFk();
        $row['headline'] = $paper->getHeadline();
        $row['keywords'] = $paper->getKeywords();
        $row['editor'] = $paper->getEditor();
        // updated je timestamp
    }

}
