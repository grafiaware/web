<?php

namespace Events\Model\Hydrator;

use Model\Entity\EntityInterface;
use Model\Hydrator\HydratorInterface;
use Model\Hydrator\TypeHydratorAbstract;
use ArrayAccess;

use Events\Model\Entity\VisitorJobRequestInterface;

/**
 * Description of PaperHydrator
 *
 * @author pes2704
 */
class VisitorJobRequestHydrator extends TypeHydratorAbstract implements HydratorInterface {

    /**
     *
     * @param VisitorJobRequestInterface $visitorJobRequest
     * @param type $rowData
     */
    public function hydrate( EntityInterface $visitorJobRequest, ArrayAccess $rowData) {
        /** @var VisitorJobRequestInterface $visitorJobRequest */
        $visitorJobRequest
                 // primary key
            ->setLoginLoginName($this->getPhpValue($rowData, 'login_login_name'))
                
            ->setJobId($this->getPhpValue($rowData, 'job_id' ))    
            ->setPrefix($this->getPhpValue($rowData, 'prefix'))
            ->setName($this->getPhpValue($rowData, 'name'))
            ->setSurname($this->getPhpValue($rowData, 'surname'))
            ->setPostfix($this->getPhpValue($rowData, 'postfix'))
            ->setEmail($this->getPhpValue($rowData, 'email'))
            ->setPhone($this->getPhpValue($rowData, 'phone'))
            ->setCvEducationText($this->getPhpValue($rowData, 'cv_education_text'))
            ->setCvSkillsText($this->getPhpValue($rowData, 'cv_skills_text'))
            ->setCvDocument($this->getPhpValue($rowData, 'cv_document'))
            ->setLetterDocument($this->getPhpValue($rowData, 'letter_document'))
            ->setPositionName($this->getPhpValue($rowData, 'position_name'))
           ;
                    
    }

    /**
     *
     * @param VisitorJobRequestInterface $visitorDataPost
     * @param ArrayAccess $row
     */
    public function extract(EntityInterface $visitorDataPost, ArrayAccess $rowData) {
        /** @var VisitorJobRequestInterface $visitorDataPost */
            // primary key
            $this->setSqlValue($rowData, 'login_login_name', $visitorDataPost->getLoginLoginName());
            
            $this->setSqlValue($rowData, 'job_id', $visitorDataPost->getJobId());
            $this->setSqlValue($rowData, 'position_name', $visitorDataPost->getPositionName());
            $this->setSqlValue($rowData, 'prefix', $visitorDataPost->getPrefix());
            $this->setSqlValue($rowData, 'name', $visitorDataPost->getName());
            $this->setSqlValue($rowData, 'surname', $visitorDataPost->getSurname());
            $this->setSqlValue($rowData, 'postfix', $visitorDataPost->getPostfix());
            $this->setSqlValue($rowData, 'email', $visitorDataPost->getEmail());
            $this->setSqlValue($rowData, 'phone', $visitorDataPost->getPhone());
            $this->setSqlValue($rowData, 'cv_education_text', $visitorDataPost->getCvEducationText());
            $this->setSqlValue($rowData, 'cv_skills_text', $visitorDataPost->getCvSkillsText());
            $this->setSqlValue($rowData, 'cv_document', $visitorDataPost->getCvDocument());
            $this->setSqlValue($rowData, 'letter_document', $visitorDataPost->getLetterDocument());
                     
            
    }

}
