<?php
namespace Events\Model\Hydrator;

use Model\Entity\EntityInterface;
use Model\Hydrator\HydratorInterface;
use ArrayAccess;
use Model\Hydrator\TypeHydratorAbstract;

use Events\Model\Entity\CompanyInfoInterface;




/**
 * Description of CompanyInfoHydrator
 *
 * @author vlse2610
 */
class CompanyInfoHydrator extends TypeHydratorAbstract implements HydratorInterface {
    
    /**
     * 
     * @param CompanyInfoInterface $companyInfo
     * @param ArrayAccess $rowData
     */
    public function hydrate(EntityInterface $companyInfo, ArrayAccess $rowData) {
        /** @var CompanyInfoInterface $companyInfo */
        $companyInfo
            ->setCompanyId($this->getPhpValue( $rowData, 'company_id'))
            ->setIntroduction($this->getPhpValue( $rowData, 'introduction'))
            ->setVideoLink($this->getPhpValue($rowData, 'video_link'))   
            ->setPositives($this->getPhpValue($rowData, 'positives'))   
            ->setSocial($this->getPhpValue($rowData, 'social'))   
            ;                
    }

    
    /**
     * 
     * @param CompanyInfoInterface $companyInfo
     * @param ArrayAccess $rowData
     */
    public function extract(EntityInterface $companyInfo, ArrayAccess $rowData) {
        /** @var CompanyInfoInterface $companyInfo */
        $this->setSqlValue( $rowData,'company_id', $companyInfo->getCompanyId());
        $this->setSqlValue( $rowData,'introduction', $companyInfo->getIntroduction());
        $this->setSqlValue( $rowData,'video_link', $companyInfo->getVideoLink());
        $this->setSqlValue( $rowData,'positives', $companyInfo->getPositives());
        $this->setSqlValue( $rowData,'social', $companyInfo->getSocial());
    }
    
 }
