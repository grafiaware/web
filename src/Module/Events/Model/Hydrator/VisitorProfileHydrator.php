<?php
namespace Events\Model\Hydrator;

use Model\Entity\EntityInterface;
use Model\Hydrator\HydratorInterface;
use Model\RowData\RowDataInterface;
use Model\Hydrator\TypeHydratorAbstract;


use Events\Model\Entity\VisitorProfileInterface;

/**
 * Description of PaperHydrator
 *
 */
class VisitorProfileHydrator extends TypeHydratorAbstract implements HydratorInterface {

    /**
     *
     * @param VisitorProfileInterface $visitorProfile
     * @param type $rowData
     */
    public function hydrate(EntityInterface $visitorProfile, RowDataInterface $rowData) {
        /** @var VisitorProfileInterface $visitorProfile */
        $visitorProfile
                ->setLoginLoginName($this->getPhpValue($rowData,'login_login_name'))
                ->setPrefix($this->getPhpValue  ($rowData, 'prefix'))
                ->setName($this->getPhpValue    ($rowData, 'name'))
                ->setSurname($this->getPhpValue ($rowData, 'surname'))
                ->setPostfix($this->getPhpValue ($rowData, 'postfix'))
                ->setEmail($this->getPhpValue   ($rowData, 'email'))
                ->setPhone($this->getPhpValue   ($rowData, 'phone'))
                ->setCvEducationText($this->getPhpValue ($rowData,'cv_education_text'))
                ->setCvSkillsText($this->getPhpValue    ($rowData,'cv_skills_text'))
                ->setCvDocument($this->getPhpValue      ($rowData,'cv_document'))
                ->setLetterDocument($this->getPhpValue  ($rowData,'letter_document'));
        
    }

    /**
     *
     * @param VisitorProfileInterface $visitorProfile
     * @param RowDataInterface $rowData
     */
    public function extract(EntityInterface $visitorProfile, RowDataInterface $rowData) {
        /** @var VisitorProfileInterface $visitorProfile */
             $this->setSqlValue($rowData, 'login_login_name', $visitorProfile->getLoginLoginName());
             $this->setSqlValue($rowData, 'prefix', $visitorProfile->getPrefix());
             $this->setSqlValue($rowData, 'name', $visitorProfile->getName());
             $this->setSqlValue($rowData, 'surname', $visitorProfile->getSurname());
             $this->setSqlValue($rowData, 'postfix', $visitorProfile->getPostfix());
             $this->setSqlValue($rowData, 'email', $visitorProfile->getEmail());
             $this->setSqlValue($rowData, 'phone', $visitorProfile->getPhone());
             $this->setSqlValue($rowData, 'cv_education_text', $visitorProfile->getCvEducationText());
             $this->setSqlValue($rowData, 'cv_skills_text', $visitorProfile->getCvSkillsText());
             $this->setSqlValue($rowData, 'cv_document', $visitorProfile->getCvDocument());
             $this->setSqlValue($rowData, 'letter_document', $visitorProfile->getLetterDocument());
                        
    }

}
