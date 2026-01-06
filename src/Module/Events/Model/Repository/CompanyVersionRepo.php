<?php
namespace Events\Model\Repository;

use Model\Repository\RepoAbstract;

use Events\Model\Entity\CompanyVersion;
use Events\Model\Entity\CompanyVersionInterface;
use Events\Model\Dao\CompanyVersionDao;
use Events\Model\Hydrator\CompanyVersionHydrator;
use Events\Model\Repository\CompanyVersionRepoInterface;


/**
 * Description of CompanyAddressRepo
 *
 * @author vlse2610
 */
class CompanyVersionRepo  extends RepoAbstract implements CompanyVersionRepoInterface {

    public function __construct(CompanyVersionDao $companyVersionDao, CompanyVersionHydrator $companyVersionHydrator) {
        $this->dataManager = $companyVersionDao;
        $this->registerHydrator($companyVersionHydrator);
    }

    /**
     *
     * @param string $version
     * @return CompanyVersionInterface|null
     */
    public function get($version): ?CompanyVersionInterface {
        return $this->getEntity($version);
    }

    /**
     *
     * @param type $whereClause
     * @param type $touplesToBind
     * @return CompanyVersionInterface[]
     */
    public function find($whereClause="", $touplesToBind=[]): array {
        return $this->findEntities($whereClause, $touplesToBind);
    }

    /**
     *
     * @return CompanyVersionInterface[]
     */
    public function findAll(): array {
        return $this->findEntities();
    }

   /**
     *
     * @param CompanyVersionInterface $companyVersion
     * @return void
     */
    public function add( CompanyVersionInterface $companyVersion ) : void {
        $this->addEntity($companyVersion);
    }


    /**
     *
     * @param CompanyVersionInterface $companyVersion
     * @return void
     */
    public function remove(CompanyVersionInterface $companyVersion)  :void {
        $this->removeEntity($companyVersion);
    }

    protected function createEntity() : CompanyVersionInterface {
        return new CompanyVersion();
    }

    protected function indexFromEntity(CompanyVersionInterface $companyVersion) {
        return $companyVersion->getCompanyId();
    }

    protected function indexFromRow( $row ) {
        return $row['version'];
    }
}


