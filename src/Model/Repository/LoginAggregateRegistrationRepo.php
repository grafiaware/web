<?php
namespace Model\Repository;

use Model\Repository\LoginRepo;

use Model\Dao\LoginDao;
use Model\Hydrator\HydratorInterface;

use Model\Repository\RegistrationRepo;

use Model\Hydrator\LoginChildRegistrationHydrator;

use Model\Entity\LoginAggregateRegistration;
use Model\Entity\LoginAggregateRegistrationInterface;
use Model\Entity\LoginInterface;
use Model\Entity\RegistrationInterface;

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
        /** @var LoginAggregateRegistrationInterface $loginAggregate */
        parent::add($loginAggregate);    //zapise jen  Login
       $this->addAssociated(RegistrationInterface::class, $loginAggregate->getRegistration());  
    }
    public function remove(LoginInterface $loginAggregate) {
        /** @var LoginAggregateRegistrationInterface $loginAggregate */
        $this->removeAssociated(RegistrationInterface::class, $loginAggregate->getRegistration()); 
        parent::remove($loginAggregate);
    }

}
