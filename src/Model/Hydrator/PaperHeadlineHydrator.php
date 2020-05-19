<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Hydrator;

use Model\Entity\EntityInterface;
use Model\Entity\PaperHeadlineInterface;

/**
 * Description of PaperHydrator
 *
 * @author pes2704
 */
class PaperHeadlineHydrator implements HydratorInterface {

    /**
     *
     * @param PaperHeadlineInterface $paperHeadline
     * @param type $row
     */
    public function hydrate(EntityInterface $paperHeadline, &$row) {
        /** @var PaperHeadlineInterface $paperHeadline */
        $paperHeadline
            ->setMenuItemIdFk($row['menu_item_id_fk'])
            ->setHeadline($row['headline'])
            ->setKeywords($row['keywords'])
            ->setEditor($row['editor'])
            ->setUpdated($row['updated'] ? \DateTime::createFromFormat('Y-m-d H:i:s', $row['updated']) : NULL);
    }

    /**
     *
     * @param PaperHeadlineInterface $paperHeadline
     * @param type $row
     */
    public function extract(EntityInterface $paperHeadline, &$row) {
        /** @var PaperHeadlineInterface $paperHeadline */
        $row['menu_item_id_fk'] = $paperHeadline->getMenuItemIdFk();
        $row['headline'] = $paperHeadline->getHeadline();
        $row['keywords'] = $paperHeadline->getKeywords();
        $row['editor'] = $paperHeadline->getEditor();
        // updated je timestamp
    }

}
