<?php
namespace Events\Model\Repository;

use Model\Repository\RepoAbstract;

use Events\Model\Entity\CompanyInfo;
use Events\Model\Entity\CompanyInfoInterface;
use Events\Model\Dao\CompanyInfoDao;
use Events\Model\Hydrator\CompanyInfoHydrator;
use Events\Model\Repository\CompanyInfoRepoInterface;


/**
 * Description of CompanyAddressRepo
 *
 * @author vlse2610
 */
class CompanyInfoRepo  extends RepoAbstract implements CompanyInfoRepoInterface {

    public function __construct(CompanyInfoDao $companyInfoDao, CompanyInfoHydrator $companyInfoHydrator) {
        $this->dataManager = $companyInfoDao;
        $this->registerHydrator($companyInfoHydrator);
    }

    /**
     *
     * @param type $company_id
     * @return CompanyInfoInterface|null
     */
    public function get($company_id): ?CompanyInfoInterface {
        return $this->getEntity($company_id);
    }

    /**
     *
     * @param type $whereClause
     * @param type $touplesToBind
     * @return CompanyInfoInterface[]
     */
    public function find($whereClause="", $touplesToBind=[]): array {
        return $this->findEntities($whereClause, $touplesToBind);
    }

    /**
     *
     * @return CompanyInfoInterface[]
     */
    public function findAll(): array {
        return $this->findEntities();
    }

   /**
     *
     * @param CompanyInfoInterface $companyAddress
     * @return void
     */
    public function add( CompanyInfoInterface $companyAddress ) : void {
        $this->addEntity($companyAddress);
    }


    /**
     *
     * @param CompanyInfoInterface $companyAddress
     * @return void
     */
    public function remove(CompanyInfoInterface $companyAddress)  :void {
        $this->removeEntity($companyAddress);
    }

    protected function createEntity() : CompanyInfoInterface {
        return new CompanyInfo();
    }

    protected function indexFromEntity(CompanyInfoInterface $companyInfo) {
        return $companyInfo->getCompanyId();
    }

    protected function indexFromRow( $row ) {
        return $row['company_id'];
    }
}


