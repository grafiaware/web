<?php
declare(strict_types=1);

namespace Test\Integration\Auth\Model\Repository;

use Access\Enum\RoleEnum;
use Auth\Model\Dao\CredentialsDao;
use Auth\Model\Dao\LoginDao;
use Auth\Model\Dao\RegistrationDao;
use Auth\Model\Entity\Credentials;
use Auth\Model\Entity\LoginAggregateFull;
use Auth\Model\Entity\Registration;
use Auth\Model\Repository\LoginAggregateFullRepo;
use Container\AuthContainerConfigurator;
use Container\AuthDbContainerConfigurator;
use Pes\Container\Container;
use Pes\Model\RowData\RowData;
use Test\AppRunner\AppRunner;

final class LoginAggregateFullRepositoryTest extends AppRunner
{
    private const PREFIX = 'LoginAggFullTest_';
    private const LOGIN_NAME = self::PREFIX . 'user';

    private LoginAggregateFullRepo $repo;

    public static function setUpBeforeClass(): void
    {
        self::bootstrapBeforeClass();
        self::cleanup();
    }

    protected function setUp(): void
    {
        $container = (new AuthContainerConfigurator())->configure(
            (new AuthDbContainerConfigurator())->configure(new Container())
        );
        $this->repo = $container->get(LoginAggregateFullRepo::class);
    }

    protected function tearDown(): void
    {
        $this->repo->flush();
    }

    public static function tearDownAfterClass(): void
    {
        self::cleanup();
    }

    public function testAddCompleteAggregateAndReadBack(): void
    {
        $aggregate = new LoginAggregateFull();
        $aggregate->setLoginName(self::LOGIN_NAME);

        $credentials = new Credentials();
        $credentials->setLoginNameFk(self::LOGIN_NAME);
        $credentials->setPasswordHash('hash-value');
        $credentials->setRoleFk(RoleEnum::VISITOR);
        $aggregate->setCredentials($credentials);

        $registration = new Registration();
        $registration->setLoginNameFk(self::LOGIN_NAME);
        $registration->setPasswordHash('plain');
        $registration->setEmail(self::LOGIN_NAME . '@example.cz');
        $registration->setEmailTime(new \DateTime());
        $aggregate->setRegistration($registration);

        $this->repo->add($aggregate);
        $this->repo->flush();

        $loaded = $this->repo->get(self::LOGIN_NAME);
        $this->assertInstanceOf(LoginAggregateFull::class, $loaded);
        $this->assertInstanceOf(Credentials::class, $loaded->getCredentials());
        $this->assertInstanceOf(Registration::class, $loaded->getRegistration());
        $this->assertSame('hash-value', $loaded->getCredentials()->getPasswordHash());
    }

    public function testRemoveAggregate(): void
    {
        $aggregate = $this->repo->get(self::LOGIN_NAME);
        $this->assertNotNull($aggregate);
        $this->repo->remove($aggregate);
        $this->repo->flush();
        $this->assertNull($this->repo->get(self::LOGIN_NAME));
    }

    private static function cleanup(): void
    {
        $container = (new AuthContainerConfigurator())->configure(
            (new AuthDbContainerConfigurator())->configure(new Container())
        );

        /** @var RegistrationDao $registrationDao */
        $registrationDao = $container->get(RegistrationDao::class);
        foreach ($registrationDao->find('login_name_fk LIKE :login_name_like', ['login_name_like' => self::PREFIX . '%']) as $row) {
            $registrationDao->delete($row);
        }

        /** @var CredentialsDao $credentialsDao */
        $credentialsDao = $container->get(CredentialsDao::class);
        foreach ($credentialsDao->find('login_name_fk LIKE :login_name_like', ['login_name_like' => self::PREFIX . '%']) as $row) {
            $credentialsDao->delete($row);
        }

        /** @var LoginDao $loginDao */
        $loginDao = $container->get(LoginDao::class);
        foreach ($loginDao->find('login_name LIKE :login_name_like', ['login_name_like' => self::PREFIX . '%']) as $row) {
            $loginDao->delete($row);
        }
    }
}
