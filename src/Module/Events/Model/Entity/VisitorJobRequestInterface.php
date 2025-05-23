<?php


namespace Events\Model\Entity;

use Model\Entity\PersistableEntityInterface;
use Events\Model\Entity\VisitorJobRequestInterface;


/**
 *
 * @author pes2704
 */
interface VisitorJobRequestInterface extends PersistableEntityInterface {

    public function getLoginLoginName();

    public function getJobId();

    public function getPrefix();

    public function getName();

    public function getSurname();

    public function getPostfix();
    
    public function getEmail();

    public function getPhone();

    public function getCvEducationText();

    public function getCvSkillsText();

    public function getCvDocument();

    public function getLetterDocument();
    
    public function getCreated();
    
    public function setLoginLoginName($loginLoginName): VisitorJobRequestInterface;

    public function setJobId($jobId): VisitorJobRequestInterface;

    public function setPrefix($prefix): VisitorJobRequestInterface;

    public function setName($name): VisitorJobRequestInterface;

    public function setSurname($surname): VisitorJobRequestInterface;

    public function setPostfix($postfix): VisitorJobRequestInterface;

    public function setEmail($email): VisitorJobRequestInterface;
    
    public function setPhone($phone): VisitorJobRequestInterface;

    public function setCvEducationText($cvEducationText): VisitorJobRequestInterface;

    public function setCvSkillsText($cvSkillsText): VisitorJobRequestInterface;

    public function setCvDocument($cvDocument): VisitorJobRequestInterface;

    public function setLetterDocument($letterDocument): VisitorJobRequestInterface;

    public function setCreated($created): VisitorJobRequestInterface;

}
