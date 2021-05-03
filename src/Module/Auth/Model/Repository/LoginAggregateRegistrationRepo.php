<?php
namespace Auth\Model\Repository;

use Model\Hydrator\HydratorInterface;

use Auth\Model\Repository\LoginRepo;
use Auth\Model\Dao\LoginDao;
use Auth\Model\Repository\RegistrationRepo;
use Auth\Model\Hydrator\LoginChildRegistrationHydrator;

use Auth\Model\Entity\LoginAggregateRegistration;
use Auth\Model\Entity\LoginAggregateRegistrationInterface;
use Auth\Model\Entity\LoginInterface;
use Auth\Model\Entity\RegistrationInterface;

/**
 * Description of LoginAggregateRegistrationRepo
 *
 * @author vlse2610
 */
class LoginAggregateRegistrationRepo extends LoginRepo implements LoginRepoInterface {



    public function __construct(
            LoginDao $loginDao,
            HydratorInterface $loginHydrator,
            RegistrationRepo $registrationRepo,
            LoginChildRegistrationHydrator $loginRegistrationHydrator)
    {
        parent::__construct($loginDao, $loginHydrator);
        $this->registerOneToOneAssociation(RegistrationInterface::class, 'login_name', $registrationRepo);
        $this->registerHydrator($loginRegistrationHydrator);
    }

    protected function createEntity() {
        return new LoginAggregateRegistration();
    }

    public function add(LoginInterface $loginAggregate) {
        $this->addEntity($loginAggregate);
    }
    public function remove(LoginInterface $loginAggregate) {
        $this->removeEntity($loginAggregate);
    }

}
