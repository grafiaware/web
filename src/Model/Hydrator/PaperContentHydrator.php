<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Hydrator;

use Model\Entity\EntityInterface;
use Model\Entity\PaperContentInterface;

/**
 * Description of PaperHydrator
 *
 * @author pes2704
 */
class PaperContentHydrator implements HydratorInterface {

    /**
     *
     * @param PaperInterface $paperContent
     * @param type $row
     */
    public function hydrate(EntityInterface $paperContent, &$row) {
        /** @var PaperContentInterface $paperContent */
        $paperContent
            ->setPaperIdFk($row['paper_id_fk'])
            ->setId($row['id'])
            ->setContent($row['content'])
            ->setActive($row['active'])
            ->setShowTime($row['show_time'])
            ->setHideTime($row['hide_time'])
            ->setEditor($row['editor'])
            ->setUpdated($row['updated'] ? \DateTime::createFromFormat('Y-m-d H:i:s', $row['updated']) : NULL)
            ->setActual($row['actual']);
    }

    /**
     *
     * @param PaperInterface $paperContent
     * @param type $row
     */
    public function extract(EntityInterface $paperContent, &$row) {
        /** @var PaperContentInterface $paperContent */
        $row['paper_id_fk'] = $paperContent->getPaperIdFk();
        $row['id'] = $paperContent->getId();
        $row['content'] = $paperContent->getContent();
        $row['active'] = $paperContent->getActive();
        $row['show_time'] = $paperContent->getShowTime();
        $row['hide_time'] = $paperContent->getHideTime();
        $row['editor'] = $paperContent->getEditor();
        // updated je timestamp
        // actual je readonly
    }

}
