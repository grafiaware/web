<?php

namespace Events\Model\Entity;

use Model\Entity\PersistableEntityInterface;
use Events\Model\Entity\VisitorProfileInterface;


/**
 *
 * @author pes2704
 */
interface VisitorProfileInterface extends PersistableEntityInterface {
    
    public function getLoginLoginName();

    public function getPrefix();

    public function getName();

    public function getSurname();

    public function getPostfix();

    public function getPhone();

    public function getCvEducationText();

    public function getCvSkillsText();

    public function getCvDocument();

    public function getLetterDocument();

    public function setLoginLoginName($loginLoginName): VisitorProfileInterface;

    public function setPrefix($prefix): VisitorProfileInterface;

    public function setName($name): VisitorProfileInterface;

    public function setSurname($surname): VisitorProfileInterface;

    public function setPostfix($postfix): VisitorProfileInterface; 

    public function setPhone($phone): VisitorProfileInterface;

    public function setCvEducationText($cvEducationText): VisitorProfileInterface;

    public function setCvSkillsText($cvSkillsText): VisitorProfileInterface;

    public function setCvDocument($cvDocument): VisitorProfileInterface;

    public function setLetterDocument($letterDocument): VisitorProfileInterface;
}
