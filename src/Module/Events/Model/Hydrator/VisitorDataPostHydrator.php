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

use Events\Model\Entity\VisitorDataPostInterface;

/**
 * Description of PaperHydrator
 *
 * @author pes2704
 */
class VisitorDataPostHydrator implements HydratorInterface {

    /**
     *
     * @param VisitorDataPostInterface $visitorDataPost
     * @param type $rowData
     */
    public function hydrate(EntityInterface $visitorDataPost, RowDataInterface $rowData) {
        /** @var VisitorDataPostInterface $visitorDataPost */
        $visitorDataPost
            ->setPrefix($rowData->offsetGet('prefix'))
            ->setName($rowData->offsetGet('name'))
            ->setSurname($rowData->offsetGet('surname'))
            ->setPostfix($rowData->offsetGet('postfix'))
            ->setEmail($rowData->offsetGet('email'))
            ->setPhone($rowData->offsetGet('phone'))
            ->setCvEducationText($rowData->offsetGet('cv_education_text'))
            ->setCvSkillsText($rowData->offsetGet('cv_skills_text'))
            ->setCvDocument($rowData->offsetGet('cv_document'))
            ->setCvDocumentFilename($rowData->offsetGet('cv_document_filename'))
            ->setCvDocumentMimetype($rowData->offsetGet('cv_document_mimetype'))
            ->setLetterDocument($rowData->offsetGet('letter_document'))
            ->setLetterDocumentFilename($rowData->offsetGet('letter_document_filename'))
            ->setLetterDocumentMimetype($rowData->offsetGet('letter_document_mimetype'))
            // primary key
            ->setLoginName($rowData->offsetGet('login_name'))
            ->setShortName($rowData->offsetGet('short_name'))
            ->setPositionName($rowData->offsetGet('position_name'));
    }

    /**
     *
     * @param VisitorDataPostInterface $visitorDataPost
     * @param type $row
     */
    public function extract(EntityInterface $visitorDataPost, RowDataInterface $rowData) {
        /** @var VisitorDataPostInterface $visitorDataPost */
            $rowData->offsetSet('prefix', $visitorDataPost->getPrefix());
            $rowData->offsetSet('name', $visitorDataPost->getName());
            $rowData->offsetSet('surname', $visitorDataPost->getSurname());
            $rowData->offsetSet('postfix', $visitorDataPost->getPostfix());
            $rowData->offsetSet('email', $visitorDataPost->getEmail());
            $rowData->offsetSet('phone', $visitorDataPost->getPhone());
            $rowData->offsetSet('cv_education_text', $visitorDataPost->getCvEducationText());
            $rowData->offsetSet('cv_skills_text', $visitorDataPost->getCvSkillsText());
            $rowData->offsetSet('cv_document', $visitorDataPost->getCvDocument());
            $rowData->offsetSet('cv_document_filename', $visitorDataPost->getCvDocumentFilename());
            $rowData->offsetSet('cv_document_mimetype', $visitorDataPost->getCvDocumentMimetype());
            $rowData->offsetSet('letter_document', $visitorDataPost->getLetterDocument());
            $rowData->offsetSet('letter_document_filename', $visitorDataPost->getLetterDocumentFilename());
            $rowData->offsetSet('letter_document_mimetype', $visitorDataPost->getLetterDocumentMimetype());
            // primary key
            $rowData->offsetSet('login_name', $visitorDataPost->getLoginName());
            $rowData->offsetSet('short_name', $visitorDataPost->getShortName());
            $rowData->offsetSet('position_name', $visitorDataPost->getPositionName());
    }

}
