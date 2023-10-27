<?php
namespace Events\Middleware\Events\ViewModel;

use Events\Model\Repository\CompanyRepoInterface;
use Events\Model\Repository\RepresentativeRepoInterface;

use Events\Model\Entity\RepresentativeInterface;
use Events\Model\Entity\CompanyInterface;

/**
 * Description of Presenter
 *
 * @author pes"daikin"7"na""mdelektronik"
 */
class RepresenrativeViewModel {
    /**
     * @var CompanyRepoInterface
     */
    private $companyRepo;
    /**
     * @var RepresentativeRepoInterface
     */
    private $representativeRepo;
    
    

    public function __construct(
            CompanyRepoInterface $companyRepo,
            RepresentativeRepoInterface $representativeRepo
        ) {
        $this->companyRepo = $companyRepo;
        $this->representativeRepo = $representativeRepo;
    }

    /**
     * 
     * @param type $loginName
     * @param type $idCompany
     * @return RepresentativeInterface|null
     */
     public function getRepresentative($loginName, $idCompany): ?RepresentativeInterface {
        return $this->representativeRepo->get($loginName, $idCompany);
    }
    
    /**
     * 
     * @param RepresentativeInterface $representative
     * @return CompanyInterface|null
     */
    public function getRepresentativeCompany(RepresentativeInterface $representative): ?CompanyInterface {
        return $this->companyRepo->get($representative->getCompanyId());      
    }
    
    /**
     * Z DB
     * @return array
     */
    public function getCompanyList() {
        $allCompanyObjects = $this->companyRepo->findAll();

        foreach  ($allCompanyObjects as $company) {
            $allCompanyArr [$company->getName()] =  ['id' => $company->getId(),
                                                     'name' => $company->getName(),
                                                     'eventInstitutionName30' => $company->getEventInstitutionName30() ];
        }
        //return  $allCompanyArr;
        return $allCompanyObjects;
    }

}
