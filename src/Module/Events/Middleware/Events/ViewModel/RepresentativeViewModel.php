<?php
namespace Events\Middleware\Events\ViewModel;

use Component\ViewModel\StatusViewModelInterface;
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
     * 
     * @var StatusViewModelInterface
     */
    private $statusViewModel;
    
    /**
     * @var CompanyRepoInterface
     */
    private $companyRepo;
    /**
     * @var RepresentativeRepoInterface
     */
    private $representativeRepo;
    
    

    public function __construct(
            StatusViewModelInterface $statusViewModel,
            CompanyRepoInterface $companyRepo,
            RepresentativeRepoInterface $representativeRepo
        ) {
        $this->statusViewModel = $statusViewModel;
        $this->companyRepo = $companyRepo;
        $this->representativeRepo = $representativeRepo;
    }
    
    public function getSelectedCompany() {
        $this->statusViewModel->getSecurityInfos();
    }
    
    /**
     * 
     * @param string $loginName Přihlašovací jméno reprezentanta
     * @return RepresentativeInterface[] Pole entit RepresentativeInterface
     */
    public function getRepresentativesList($loginName): array {
        return $this->representativeRepo->findByLoginName($loginName);
    }
    
    #########
    
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
     * Z DB
     * @return array
     */
    public function getCompanyList() {
        $allCompanyObjects = $this->companyRepo->findAll();
        return $allCompanyObjects;
    }

}
