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

use Events\Model\Entity\VisitorProfileInterface;

/**
 * Description of PaperHydrator
 *
 * @author pes2704
 */
class VisitorProfileHydrator implements HydratorInterface {

    /**
     *
     * @param VisitorProfileInterface $visitorProfile
     * @param type $rowData
     */
    public function hydrate(EntityInterface $visitorProfile, RowDataInterface $rowData) {
        /** @var VisitorProfileInterface $visitorProfile */
        $visitorProfile
                ->setLoginLoginName($rowData->offsetGet('login_login_name'))
                ->setPrefix($rowData->offsetGet('prefix'))
                ->setName($rowData->offsetGet('name'))
                ->setSurname($rowData->offsetGet('surname'))
                ->setPostfix($rowData->offsetGet('postfix'))
                ->setEmail($rowData->offsetGet('email'))
                ->setPhone($rowData->offsetGet('phone'))
                ->setCvEducationText($rowData->offsetGet('cv_education_text'))
                ->setCvSkillsText($rowData->offsetGet('cv_skills_text'))
                ->setCvDocument($rowData->offsetGet('cv_document'))
                ->setLetterDocument($rowData->offsetGet('letter_document'));
    }

    /**
     *
     * @param VisitorProfileInterface $visitorProfile
     * @param type $rowData
     */
    public function extract(EntityInterface $visitorProfile, RowDataInterface $rowData) {
        /** @var VisitorProfileInterface $visitorProfile */
            $rowData->offsetSet('login_login_name', $visitorProfile->getLoginLoginName());
            $rowData->offsetSet('prefix', $visitorProfile->getPrefix());
            $rowData->offsetSet('name', $visitorProfile->getName());
            $rowData->offsetSet('postfix', $visitorProfile->getPostfix());
            $rowData->offsetSet('email', $visitorProfile->getEmail());
            $rowData->offsetSet('phone', $visitorProfile->getPhone());
            $rowData->offsetSet('cv_education_text', $visitorProfile->getCvEducationText());
            $rowData->offsetSet('cv_skills_text', $visitorProfile->getCvSkillsText());
            $rowData->offsetSet('cv_document', $visitorProfile->getCvDocument());
            $rowData->offsetSet('letter_document', $visitorProfile->getLetterDocument());
    }

}
