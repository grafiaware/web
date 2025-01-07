<?php
namespace Events\Component\ViewModel\Data;

use Component\ViewModel\ViewModelFamilyListAbstract;
use Events\Component\ViewModel\Data\RepresentativeTrait;
use Component\ViewModel\FamilyInterface;

use Component\ViewModel\StatusViewModelInterface;
use Events\Model\Repository\CompanyRepoInterface;
use Events\Model\Repository\CompanyInfoRepoInterface;
use Events\Model\Entity\CompanyInfo;

use Access\Enum\RoleEnum;
use ArrayIterator;

/**
 * 
 */
class CompanyFamilyCompanyInfoListViewModel extends ViewModelFamilyListAbstract {

    private $status;

    private $companyRepo;
    private $companyInfoRepo;
    
    public function __construct(
            StatusViewModelInterface $status,
            CompanyRepoInterface $companyRepo,
            CompanyInfoRepoInterface $companyInfoRepo
            ) {
        $this->status = $status;
        $this->companyRepo = $companyRepo;
        $this->companyInfoRepo = $companyInfoRepo;
    }

    use RepresentativeTrait;
    
    public function isListEditable(): bool {
        return $this->isCompanyEditor($this->getFamilyRouteSegment()->getParentId());
    }
    
    protected function newListEntity() {
        return new CompanyInfo();  // "prázdná" entita pro formulář pro přidání
    }
    
    protected function cardinality() {
        return FamilyInterface::CARDINALITY_0_1;
    }
    
    protected function loadListEntities() {
        if (!$this->listEntities) {
            $company = $this->companyRepo->get($this->getFamilyRouteSegment()->getParentId() );
            $this->listEntities = $this->companyInfoRepo->find( " company_id = :idCompany ",  ['idCompany'=> $company->getId()] );
        }
    }
    
    /**
     * 
     * @return ArrayIterator
     */
    public function getIterator() {
        $array = [         
            'listHeadline'=>'Zaměstnavatel', 
            'items' => $this->getArrayCopy()];
        $this->appendData($array);
        return parent::getIterator();        
    }
    
    
    
    
    
}    
