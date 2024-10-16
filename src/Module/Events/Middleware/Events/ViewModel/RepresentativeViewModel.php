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
class RepresentativeViewModel {
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
    public function isRepresentative($loginName, $companyName): ?RepresentativeInterface {
        $company = $this->companyRepo->getByName($companyName);
        return isset($company) ? $this->representativeRepo->get($loginName, $company->getId()) : null;
    }
    
    /**
     * 
     * @param string $idCompany
     * @return CompanyInterface|null
     */
    public function getRepresentativeCompany($idCompany): ?CompanyInterface {
        return $this->companyRepo->get($idCompany);      
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
                                                    ];
        }
        //return  $allCompanyArr;
        return $allCompanyObjects;
    }

}
