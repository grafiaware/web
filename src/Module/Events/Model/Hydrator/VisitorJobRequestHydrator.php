<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Events\Model\Hydrator;

use Model\Entity\EntityInterface;
use Model\Hydrator\HydratorInterface;
use Model\RowData\RowDataInterface;

use Events\Model\Entity\VisitorJobRequestInterface;

/**
 * Description of PaperHydrator
 *
 * @author pes2704
 */
class VisitorJobRequestHydrator implements HydratorInterface {

    /**
     *
     * @param VisitorJobRequestInterface $visitorJobRequest
     * @param type $rowData
     */
    public function hydrate(EntityInterface $visitorJobRequest, RowDataInterface $rowData) {
        /** @var VisitorJobRequestInterface $visitorJobRequest */
        $visitorJobRequest
            ->setPrefix($rowData->offsetGet('prefix'))
            ->setName($rowData->offsetGet('name'))
            ->setSurname($rowData->offsetGet('surname'))
            ->setPostfix($rowData->offsetGet('postfix'))
            ->setEmail($rowData->offsetGet('email'))
            ->setPhone($rowData->offsetGet('phone'))
            ->setCvEducationText($rowData->offsetGet('cv_education_text'))
            ->setCvSkillsText($rowData->offsetGet('cv_skills_text'))
            ->setCvDocument($rowData->offsetGet('cv_document'))
            ->setLetterDocument($rowData->offsetGet('letter_document'))
            // primary key
            ->setLoginLoginName($rowData->offsetGet('login_name'))
            ->setJobId($rowData->offsetGet('short_name'))
            ->setPositionName($rowData->offsetGet('position_name'));
    }

    /**
     *
     * @param VisitorJobRequestInterface $visitorDataPost
     * @param RowDataInterface $row
     */
    public function extract(EntityInterface $visitorDataPost, RowDataInterface $rowData) {
        /** @var VisitorJobRequestInterface $visitorDataPost */
            $rowData->offsetSet('prefix', $visitorDataPost->getPrefix());
            $rowData->offsetSet('name', $visitorDataPost->getName());
            $rowData->offsetSet('surname', $visitorDataPost->getSurname());
            $rowData->offsetSet('postfix', $visitorDataPost->getPostfix());
            $rowData->offsetSet('email', $visitorDataPost->getEmail());
            $rowData->offsetSet('phone', $visitorDataPost->getPhone());
            $rowData->offsetSet('cv_education_text', $visitorDataPost->getCvEducationText());
            $rowData->offsetSet('cv_skills_text', $visitorDataPost->getCvSkillsText());
            $rowData->offsetSet('cv_document', $visitorDataPost->getCvDocument());
            $rowData->offsetSet('letter_document', $visitorDataPost->getLetterDocument());
            // primary key
            $rowData->offsetSet('login_name', $visitorDataPost->getLoginLoginName());
            $rowData->offsetSet('short_name', $visitorDataPost->getJobId());
            $rowData->offsetSet('position_name', $visitorDataPost->getPositionName());
    }

}
