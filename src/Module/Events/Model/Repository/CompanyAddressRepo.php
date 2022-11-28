<?php
namespace Events\Model\Repository;

use Model\Repository\RepoAbstract;

use Events\Model\Entity\CompanyAddress;
use Events\Model\Entity\CompanyAddressInterface;
use Events\Model\Dao\CompanyAddressDao;
use Events\Model\Hydrator\CompanyAddressHydrator;
use Events\Model\Repository\CompanyAddressRepoInterface;


/**
 * Description of CompanyAddressRepo
 *
 * @author vlse2610
 */
class CompanyAddressRepo  extends RepoAbstract implements CompanyAddressRepoInterface {

    public function __construct(CompanyAddressDao $companyAddressDao, CompanyAddressHydrator $companyAddressHydrator) {
        $this->dataManager = $companyAddressDao;
        $this->registerHydrator($companyAddressHydrator);
    }


    /**
     *
     * @param type $company_id
     * @return CompanyAddressInterface|null
     */
    public function get($company_id): ?CompanyAddressInterface {
        $key = $this->dataManager->getPrimaryKeyTouples(['company_id'=>$company_id]);
        return $this->getEntity($key);
    }
    

    /**
     *
     * @param type $whereClause
     * @param type $touplesToBind
     * @return CompanyAddressInterface[]
     */
    public function find($whereClause="", $touplesToBind=[]): array {
        return $this->findEntities($whereClause, $touplesToBind);
    }

    /**
     *
     * @return CompanyAddressInterface[]
     */
    public function findAll(): array {
        return $this->findEntities();
    }

   /**
     *
     * @param CompanyAddressInterface $companyAddress
     * @return void
     */
    public function add( CompanyAddressInterface $companyAddress ) : void {
        $this->addEntity($companyAddress);
    }


    /**
     *
     * @param CompanyAddressInterface $companyAddress
     * @return void
     */
    public function remove(CompanyAddressInterface $companyAddress)  :void {
        $this->removeEntity($companyAddress);
    }




    protected function createEntity() : CompanyAddressInterface {
        return new CompanyAddress();
    }

    protected function indexFromEntity(CompanyAddressInterface $companyAddress) {
        return $companyAddress->getCompanyId();
    }

    protected function indexFromRow( $row ) {
        return $row['company_id'];
    }
}


