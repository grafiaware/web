<?php
namespace Events\Component\ViewModel\Data;

use Component\ViewModel\ViewModelFamilyListAbstract;
use Events\Component\ViewModel\Data\RepresentativeTrait;
use Component\ViewModel\FamilyInterface;

use Component\ViewModel\StatusViewModelInterface;
use Events\Model\Repository\CompanyRepoInterface;
use Events\Model\Repository\CompanyContactRepoInterface;

use Events\Model\Entity\CompanyContactInterface;
use Events\Model\Entity\CompanyContact;

use Access\Enum\RoleEnum;
use ArrayIterator;

/**
 * Description of RepresentativeActionViewModel
 *
 * @author pes2704
 */
class CompanyFamilyCompanyContactListViewModel extends ViewModelFamilyListAbstract {
    
    private $status;
    private $companyRepo;
    private $companyContactRepo;
   
    public function __construct(
            StatusViewModelInterface $status,
            CompanyRepoInterface $companyRepo,
            CompanyContactRepoInterface $companyContactRepo            
            ) {
        $this->status = $status;
        $this->companyRepo = $companyRepo;
        $this->companyContactRepo = $companyContactRepo;
    }

    use RepresentativeTrait;
    
    public function isListEditable(): bool {
        return $this->isCompanyEditor($this->getFamilyRouteSegment()->getParentId());
    }
        
    protected function newListEntity() {
        return new CompanyContact();  // "prázdná" entita pro formulář pro přidání
    }

    protected function cardinality() {
        return FamilyInterface::CARDINALITY_0_N;
    }    

    protected function loadListEntities() {
        if (!$this->listEntities) {
            $company = $this->companyRepo->get($this->getFamilyRouteSegment()->getParentId() );     
            $this->listEntities = $this->companyContactRepo->find( " company_id = :idCompany ",  ['idCompany'=> $company->getId()] );
        }
    }

    /**
     * 
     * @return ArrayIterator
     */
    public function getIterator() {

        $array = [         
            'listHeadline'=>'Kontakty', 
            'items' => $this->getArrayCopy()];
        $this->appendData($array);
        return parent::getIterator();        
    }
}