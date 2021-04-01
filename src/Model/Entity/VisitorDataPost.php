<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Entity;

/**
 * Description of VisitorData
 *
 * @author pes2704
 */
class VisitorDataPost extends EntityAbstract implements VisitorDataPostInterface {

    private $loginName;
    private $shortName;
    private $prefix;
    private $name;
    private $surname;
    private $postfix;
    private $email;
    private $phone;
    private $cvEducationText;
    private $cvSkillsText;
    private $cvDocument;
    private $cvDocumentFilename;
    private $cvDocumentMimetype;
    private $letterDocument;
    private $letterDocumentFilename;
    private $letterDocumentMimetype;

    private $keyAttribute = 'login_name';

    public function getKeyAttribute() {
        return $this->keyAttribute;
    }

    public function getLoginName() {
        return $this->loginName;
    }
    public function getShortName() {
        return $this->shortName;
    }

    public function getPrefix() {
        return $this->prefix;
    }

    public function getName() {
        return $this->name;
    }

    public function getSurname() {
        return $this->surname;
    }

    public function getPostfix() {
        return $this->postfix;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getPhone() {
        return $this->phone;
    }

    public function getCvEducationText() {
        return $this->cvEducationText;
    }

    public function getCvSkillsText() {
        return $this->cvSkillsText;
    }

    public function getCvDocument() {
        return $this->cvDocument;
    }

    public function getCvDocumentFilename() {
        return $this->cvDocumentFilename;
    }

    public function getCvDocumentMimetype() {
        return $this->cvDocumentMimetype;
    }

    public function getLetterDocument() {
        return $this->letterDocument;
    }

    public function getLetterDocumentFilename() {
        return $this->letterDocumentFilename;
    }

    public function getLetterDocumentMimetype() {
        return $this->letterDocumentMimetype;
    }

    public function setLoginName($loginName): VisitorDataPostInterface {
        $this->loginName = $loginName;
        return $this;
    }

    public function setShortName($shortName): VisitorDataPostInterface {
        $this->shortName = $shortName;
        return $this;
    }

    public function setPrefix($prefix): VisitorDataPostInterface {
        $this->prefix = $prefix;
        return $this;
    }

    public function setName($name): VisitorDataPostInterface {
        $this->name = $name;
        return $this;
    }

    public function setSurname($surname): VisitorDataPostInterface {
        $this->surname = $surname;
        return $this;
    }

    public function setPostfix($postfix): VisitorDataPostInterface {
        $this->postfix = $postfix;
        return $this;
    }

    public function setEmail($email): VisitorDataPostInterface {
        $this->email = $email;
        return $this;
    }

    public function setPhone($phone): VisitorDataPostInterface {
        $this->phone = $phone;
        return $this;
    }

    public function setCvEducationText($cvEducationText): VisitorDataPostInterface {
        $this->cvEducationText = $cvEducationText;
        return $this;
    }

    public function setCvSkillsText($cvSkillsText): VisitorDataPostInterface {
        $this->cvSkillsText = $cvSkillsText;
        return $this;
    }

    public function setCvDocument($cvDocument): VisitorDataPostInterface {
        $this->cvDocument = $cvDocument;
        return $this;
    }

    public function setCvDocumentFilename($cvDocumentFilename): VisitorDataPostInterface {
        $this->cvDocumentFilename = $cvDocumentFilename;
        return $this;
    }

    public function setCvDocumentMimetype($cvDocumentMimetype): VisitorDataPostInterface {
        $this->cvDocumentMimetype = $cvDocumentMimetype;
        return $this;
    }

    public function setLetterDocument($letterDocument): VisitorDataPostInterface {
        $this->letterDocument = $letterDocument;
        return $this;
    }

    public function setLetterDocumentFilename($letterDocumentFilename): VisitorDataPostInterface {
        $this->letterDocumentFilename = $letterDocumentFilename;
        return $this;
    }

    public function setLetterDocumentMimetype($letterDocumentMimetype): VisitorDataPostInterface {
        $this->letterDocumentMimetype = $letterDocumentMimetype;
        return $this;
    }


}
