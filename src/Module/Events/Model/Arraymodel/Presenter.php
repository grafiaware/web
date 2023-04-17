<?php
namespace Events\Model\Arraymodel;

use Events\Model\Repository\CompanyRepoInterface;
use Events\Model\Repository\RepresentativeRepoInterface;

/**
 * Description of Presenter
 *
 * @author pes"daikin"7"na""mdelektronik"
 */
class Presenter {

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
     * Z DB tabulky representative a tabulky company
     * @param type $loginName
     * @return type
     */
     public function getPerson($loginName, $idCompany) {
        $representativeEntity = $this->representativeRepo->get($loginName, $idCompany); //companyId, loginLoginName
        if ($representativeEntity) {
            $companyEntity = $this->companyRepo->get($representativeEntity->getCompanyId()); //id, name, eventInstitutionName30

            $retArray =  [  //representative a company
                          'regname' =>  $representativeEntity->getLoginLoginName(),
//                          
                          'regcompany' => $companyEntity->getName(),
                          'idCompany' =>  $companyEntity->getId(),
                          'name' =>  $companyEntity->getName(),
                          'eventInstitutionName' =>  $companyEntity->getEventInstitutionName30(),
                          'shortName' =>  $companyEntity->getName(),
                         ];
        }
        return  $retArray ?? [] ;
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
