<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Hydrator;

use Model\Entity\EntityInterface;
use Model\Entity\PaperContentInterface;
use Pes\Type\Date;

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
            ->setPriority($row['priority'])
            ->setShowTime($row['show_time'] ? Date::createFromSqlDate($row['show_time'])->getCzechStringDate() : NULL)
            ->setHideTime($row['hide_time'] ? Date::createFromSqlDate($row['hide_time'])->getCzechStringDate() : NULL)
            ->setEventTime($row['event_time'] ? Date::createFromSqlDate($row['event_time'])->getCzechStringDate() : NULL)
            ->setEditor($row['editor'])
            ->setUpdated($row['updated'] ? \DateTime::createFromFormat('Y-m-d H:i:s', $row['updated']) : NULL)  // včetně času
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
        $row['priority'] = $paperContent->getPriority();
        $row['show_time'] = $paperContent->getShowTime() ? Date::createFromCzechStringDate($paperContent->getShowTime())->getSqlDate() : null;
        $row['hide_time'] = $paperContent->getHideTime() ? Date::createFromCzechStringDate($paperContent->getHideTime())->getSqlDate() : null;
        $row['event_time'] = $paperContent->getEventTime() ? Date::createFromCzechStringDate($paperContent->getEventTime())->getSqlDate() : null;
        $row['editor'] = $paperContent->getEditor();
        // updated je timestamp
        // actual je readonly
    }

}
