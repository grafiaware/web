<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Events\Model\Entity;

/**
 * Description of VisitorData
 *
 * @author pes2704
 */
class VisitorJobRequest extends EntityAbstract implements VisitorJobRequestInterface {

    private $loginLoginName;
    private $jobId;
    private $positionName;
    private $prefix;
    private $name;
    private $surname;
    private $postfix;
    private $email;
    private $phone;
    private $cvEducationText;
    private $cvSkillsText;
    private $cvDocument;
    private $letterDocument;

    private $keyAttribute = 'login_name';

    public function getKeyAttribute() {
        return $this->keyAttribute;
    }

    public function getLoginLoginName() {
        return $this->loginLoginName;
    }
    public function getJobId() {
        return $this->jobId;
    }
    public function getPositionName() {
        return $this->positionName;
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

    public function getLetterDocument() {
        return $this->letterDocument;
    }

    public function setLoginLoginName($loginName): VisitorJobRequestInterface {
        $this->loginLoginName = $loginName;
        return $this;
    }

    public function setJobId($shortName): VisitorJobRequestInterface {
        $this->jobId = $shortName;
        return $this;
    }

    public function setPositionName($positionName): VisitorJobRequestInterface {
        $this->positionName = $positionName;
        return $this;
    }

    public function setPrefix($prefix): VisitorJobRequestInterface {
        $this->prefix = $prefix;
        return $this;
    }

    public function setName($name): VisitorJobRequestInterface {
        $this->name = $name;
        return $this;
    }

    public function setSurname($surname): VisitorJobRequestInterface {
        $this->surname = $surname;
        return $this;
    }

    public function setPostfix($postfix): VisitorJobRequestInterface {
        $this->postfix = $postfix;
        return $this;
    }

    public function setEmail($email): VisitorJobRequestInterface {
        $this->email = $email;
        return $this;
    }

    public function setPhone($phone): VisitorJobRequestInterface {
        $this->phone = $phone;
        return $this;
    }

    public function setCvEducationText($cvEducationText): VisitorJobRequestInterface {
        $this->cvEducationText = $cvEducationText;
        return $this;
    }

    public function setCvSkillsText($cvSkillsText): VisitorJobRequestInterface {
        $this->cvSkillsText = $cvSkillsText;
        return $this;
    }

    public function setCvDocument($cvDocument): VisitorJobRequestInterface {
        $this->cvDocument = $cvDocument;
        return $this;
    }

    public function setLetterDocument($letterDocument): VisitorJobRequestInterface {
        $this->letterDocument = $letterDocument;
        return $this;
    }
}