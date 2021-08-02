<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Hydrator;

use Model\Hydrator\HydratorInterface;

use Model\Entity\EntityInterface;
use Red\Model\Entity\PaperContentInterface;
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
            ->setTemplateName($row['template_name'])
            ->setTemplate($row['template'])
            ->setActive($row['active'])
            ->setPriority($row['priority'])
            ->setShowTime($row['show_time'] ? \DateTime::createFromFormat('Y-m-d', $row['show_time']) : NULL)
            ->setHideTime($row['hide_time'] ? \DateTime::createFromFormat('Y-m-d', $row['hide_time']) : NULL)
            ->setEventStartTime($row['event_start_time'] ? \DateTime::createFromFormat('Y-m-d', $row['event_start_time']) : NULL)
            ->setEventEndTime($row['event_end_time'] ? \DateTime::createFromFormat('Y-m-d', $row['event_end_time']) : NULL)
            ->setEditor($row['editor'])
            ->setUpdated(\DateTime::createFromFormat('Y-m-d H:i:s', $row['updated']) )  // včetně času
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
        $row['template_name'] = $paperContent->getTemplateName();
        $row['template'] = $paperContent->getTemplate();
        $row['active'] = $paperContent->getActive();
        $row['priority'] = $paperContent->getPriority();
        $row['show_time'] = $paperContent->getShowTime() ? $paperContent->getShowTime()->format("Y-m-d") : null;
        $row['hide_time'] = $paperContent->getHideTime() ? $paperContent->getHideTime()->format("Y-m-d") : null;
        $row['event_start_time'] = $paperContent->getEventStartTime() ? $paperContent->getEventStartTime()->format("Y-m-d") : null;
        $row['event_end_time'] = $paperContent->getEventEndTime() ? $paperContent->getEventEndTime()->format("Y-m-d") : null;
        $row['editor'] = $paperContent->getEditor();
        // updated je timestamp
        // actual je readonly
    }

}
