<?php
namespace Auth\Model\Repository;

use Model\Repository\RepoAbstract;
use Auth\Model\Repository\LoginAggregateRegistrationRepoInterface;

use Auth\Model\Hydrator\LoginHydrator;
use Auth\Model\Dao\LoginDao;
use Auth\Model\Hydrator\LoginChildRegistrationHydrator;

use Model\Repository\RepoAssotiatingOneTrait;

use Auth\Model\Entity\LoginAggregateRegistration;
use Auth\Model\Entity\LoginAggregateRegistrationInterface;


/**
 * Description of LoginAggregateRegistrationRepo
 *
 * @author vlse2610
 */
class LoginAggregateRegistrationRepo  extends RepoAbstract implements LoginAggregateRegistrationRepoInterface {

    /**
     *
     * @param LoginDao $loginDao
     * @param LoginHydrator $loginHydrator
     * @param LoginChildRegistrationHydrator $loginRegistrationHydrator Hydrátor pro nastavení potomkovské entity do rodičovské entity a získání z rodičovské entity.

     */
    public function __construct(LoginDao $loginDao, LoginHydrator $loginHydrator) {
        $this->dataManager = $loginDao;
        $this->registerHydrator($loginHydrator);
    }

    use RepoAssotiatingOneTrait;

    protected function createEntity() {
        return new LoginAggregateRegistration();
    }
    
    public function get($loginName): ?LoginAggregateRegistrationInterface {
        return $this->getEntity($loginName);
    }

    /**
     *
     * @param LoginAggregateRegistrationInterface $loginAgg
     */
    public function add(LoginAggregateRegistrationInterface $loginAgg) {
        $this->addEntity($loginAgg);
    }

    /**
     *
     * @param LoginAggregateRegistrationInterface $loginAgg
     */
    public function remove(LoginAggregateRegistrationInterface $loginAgg) {
        $this->removeEntity($loginAgg);
    }

    #### protected ###########

    protected function indexFromEntity(LoginAggregateRegistrationInterface $loginAggReg) {
        return $loginAggReg->getLoginName();
    }

    protected function indexFromRow($row) {
        return $row['login_name'];
    }

}
