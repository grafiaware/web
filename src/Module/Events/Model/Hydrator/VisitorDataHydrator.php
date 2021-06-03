<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Events\Model\Hydrator;

use Model\Entity\EntityInterface;
use Model\Hydrator\HydratorInterface;

use Events\Model\Entity\VisitorDataInterface;

/**
 * Description of PaperHydrator
 *
 * @author pes2704
 */
class VisitorDataHydrator implements HydratorInterface {

    /**
     *
     * @param EnrollInterface $enroll
     * @param type $row
     */
    public function hydrate(EntityInterface $visitorData, &$row) {
        /** @var VisitorDataInterface $visitorData */
        $visitorData
            ->setPrefix($row['prefix'])
            ->setName($row['name'])
            ->setSurname($row['surname'])
            ->setPostfix($row['postfix'])
            ->setEmail($row['email'])
            ->setPhone($row['phone'])
            ->setCvEducationText($row['cv_education_text'])
            ->setCvSkillsText($row['cv_skills_text'])
            ->setCvDocument($row['cv_document'])
            ->setCvDocumentFilename($row['cv_document_filename'])
            ->setCvDocumentMimetype($row['cv_document_mimetype'])
            ->setLetterDocument($row['letter_document'])
            ->setLetterDocumentFilename($row['letter_document_filename'])
            ->setLetterDocumentMimetype($row['letter_document_mimetype'])

            ->setLoginName($row['login_name']);
    }

    /**
     *
     * @param EnrollInterface $enroll
     * @param type $row
     */
    public function extract(EntityInterface $visitorData, &$row) {
        /** @var VisitorDataInterface $visitorData */
            $row['prefix'] = $visitorData->getPrefix();
            $row['name'] = $visitorData->getName();
            $row['surname'] = $visitorData->getSurname();
            $row['postfix'] = $visitorData->getPostfix();
            $row['email'] = $visitorData->getEmail();
            $row['phone'] = $visitorData->getPhone();
            $row['cv_education_text'] = $visitorData->getCvEducationText();
            $row['cv_skills_text'] = $visitorData->getCvSkillsText();
            $row['cv_document'] = $visitorData->getCvDocument();
            $row['cv_document_filename'] = $visitorData->getCvDocumentFilename();
            $row['cv_document_mimetype'] = $visitorData->getCvDocumentMimetype();
            $row['letter_document'] = $visitorData->getLetterDocument();
            $row['letter_document_filename'] = $visitorData->getLetterDocumentFilename();
            $row['letter_document_mimetype'] = $visitorData->getLetterDocumentMimetype();

            $row['login_name'] = $visitorData->getLoginName();
    }

}
