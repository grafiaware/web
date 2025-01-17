<?php
namespace Events\Model\Repository;

use Model\Repository\RepoAbstract;
use Events\Model\Repository\CompanytoNetworkRepoInterface;

use Events\Model\Entity\CompanytoNetworkInterface;
use Events\Model\Entity\CompanytoNetwork;
use Events\Model\Dao\CompanyToNetworkDao;
use Events\Model\Hydrator\CompanytoNetworkHydrator;

//use Model\Repository\Exception\UnableRecreateEntityException;

/**
 * Description of JobToTagRepo
 *
 * @author vlse2610
 */
class CompanytoNetworkRepo extends RepoAbstract implements CompanytoNetworkRepoInterface {

    public function __construct(CompanyToNetworkDao $companyToNetworkDao, CompanytoNetworkHydrator $companytoNetworkHydrator) {
        $this->dataManager = $companyToNetworkDao;
        $this->registerHydrator($companytoNetworkHydrator);
    }

    /**
     * 
     * @param type $companyId
     * @param type $networkId
     * @return CompanytoNetworkInterface|null
     */
    public function get($companyId, $networkId): ?CompanytoNetworkInterface {
        return $this->getEntity($companyId, $networkId);
    }
    
    /**
     *
     * @param type $whereClause
     * @param type $touplesToBind
     * @return CompanytoNetworkInterface[]
     */
    public function find($whereClause="", $touplesToBind=[]): array {
        return $this->findEntities($whereClause, $touplesToBind);
    }
    
    /**
     *
     * @param type $companyId
     * @return CompanytoNetworkInterface[]
     */
    public function findByCompanyId($companyId) : array {
        return $this->findEntities("company_id = :company_id", [":company_id"=>$companyId] );
    }
    
    /**
     * 
     * @param type $networkId
     * @return CompanytoNetworkInterface[]
     */
    public function findByNetworkId($networkId) : array {
        return $this->findEntities("network_id = :network_id", [":network_id"=>$networkId] );
    }

    /**
     *
     * @return CompanytoNetworkInterface[]
     */
    public function findAll(): array  {
        return $this->findEntities();
    }

    /**
     *
     * @param CompanytoNetworkInterface $companyToNetwork
     * @return void
     */
    public function add(CompanytoNetworkInterface $companyToNetwork) :void {
        $this->addEntity($companyToNetwork);
    }

    /**
     *
     * @param CompanytoNetworkInterface $companyToNetwork
     * @return void
     */
    public function remove(CompanytoNetworkInterface $companyToNetwork)  :void {
        $this->removeEntity($companyToNetwork);
    }

    protected function createEntity() {
        return new CompanytoNetwork();
    }

    protected function indexFromEntity(CompanytoNetworkInterface $companyToNetwork) {
       return $companyToNetwork->getCompanyId() . $companyToNetwork->getNetworkId() ;
    }

    protected function indexFromRow($row) {
        return $row['company_id']. $row['network_id'] ;
    }

}
