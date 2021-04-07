<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Entity;

/**
 *
 * @author pes2704
 */
interface VisitorDataPostInterface extends EntityInterface {

    public function getLoginName();

    public function getShortName();

    public function getPositionName();

    public function getPrefix();

    public function getName();

    public function getSurname();

    public function getPostfix();

    public function getEmail();

    public function getPhone();

    public function getCvEducationText();

    public function getCvSkillsText();

    public function getCvDocument();

    public function getCvDocumentFilename();

    public function getCvDocumentMimetype();

    public function getLetterDocument();

    public function getLetterDocumentFilename();

    public function getLetterDocumentMimetype();

    public function setLoginName($loginName): VisitorDataPostInterface;

    public function setShortName($shortName): VisitorDataPostInterface;

    public function setPositionName($positionName): VisitorDataPostInterface;

    public function setPrefix($prefix): VisitorDataPostInterface;

    public function setName($name): VisitorDataPostInterface;

    public function setSurname($surname): VisitorDataPostInterface;

    public function setPostfix($postfix): VisitorDataPostInterface;

    public function setEmail($email): VisitorDataPostInterface;

    public function setPhone($phone): VisitorDataPostInterface;

    public function setCvEducationText($cvEducationText): VisitorDataPostInterface;

    public function setCvSkillsText($cvSkillsText): VisitorDataPostInterface;

    public function setCvDocument($cvDocument): VisitorDataPostInterface;

    public function setCvDocumentFilename($cvDocumentFilename): VisitorDataPostInterface;

    public function setCvDocumentMimetype($cvDocumentMimetype): VisitorDataPostInterface;

    public function setLetterDocument($letterDocument): VisitorDataPostInterface;

    public function setLetterDocumentFilename($letterDocumentFilename): VisitorDataPostInterface;

    public function setLetterDocumentMimetype($letterDocumentMimetype): VisitorDataPostInterface;

}
