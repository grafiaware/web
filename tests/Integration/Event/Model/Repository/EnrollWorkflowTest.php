<?php
declare(strict_types=1);

namespace Test\Integration\Event\Model\Repository;

use Container\EventsModelContainerConfigurator;
use Events\Model\Dao\EnrollDao;
use Events\Model\Dao\EventDao;
use Events\Model\Dao\LoginDao;
use Events\Model\Entity\Enroll;
use Events\Model\Repository\EnrollRepo;
use Pes\Container\Container;
use Pes\Model\RowData\RowData;
use Test\AppRunner\AppRunner;
use Test\Integration\Event\Container\TestDbEventsContainerConfigurator;

/**
 * Business workflow: visitor enrollment do události přes EnrollRepo.
 */
final class EnrollWorkflowTest extends AppRunner
{
    private EnrollRepo $enrollRepo;
    private static string $loginName;
    private static int $eventId;

    public static function setUpBeforeClass(): void
    {
        self::bootstrapBeforeClass();
        $container = (new EventsModelContainerConfigurator())->configure(
            (new TestDbEventsContainerConfigurator())->configure(new Container())
        );

        $prefix = 'EnrollWorkflowTest_';
        /** @var LoginDao $loginDao */
        $loginDao = $container->get(LoginDao::class);
        do {
            self::$loginName = $prefix . uniqid();
        } while ($loginDao->get(['login_name' => self::$loginName]) !== null);
        $loginData = new RowData();
        $loginData->offsetSet('login_name', self::$loginName);
        $loginDao->insert($loginData);

        /** @var EventDao $eventDao */
        $eventDao = $container->get(EventDao::class);
        $eventData = new RowData();
        $eventData->offsetSet('published', 1);
        $eventDao->insert($eventData);
        self::$eventId = (int) $eventDao->getLastInsertedPrimaryKey()['id'];
    }

    protected function setUp(): void
    {
        $container = (new EventsModelContainerConfigurator())->configure(
            (new TestDbEventsContainerConfigurator())->configure(new Container())
        );
        $this->enrollRepo = $container->get(EnrollRepo::class);
    }

    protected function tearDown(): void
    {
        $this->enrollRepo->flush();
    }

    public static function tearDownAfterClass(): void
    {
        $container = (new EventsModelContainerConfigurator())->configure(
            (new TestDbEventsContainerConfigurator())->configure(new Container())
        );

        /** @var EnrollDao $enrollDao */
        $enrollDao = $container->get(EnrollDao::class);
        foreach ($enrollDao->find('login_login_name_fk = :login', ['login' => self::$loginName]) as $row) {
            $enrollDao->delete($row);
        }

        /** @var EventDao $eventDao */
        $eventDao = $container->get(EventDao::class);
        $eventRow = $eventDao->get(['id' => self::$eventId]);
        if ($eventRow !== null) {
            $eventDao->delete($eventRow);
        }

        /** @var LoginDao $loginDao */
        $loginDao = $container->get(LoginDao::class);
        $loginRow = $loginDao->get(['login_name' => self::$loginName]);
        if ($loginRow !== null) {
            $loginDao->delete($loginRow);
        }
    }

    public function testVisitorCanEnrollToEvent(): void
    {
        $enroll = new Enroll();
        $enroll->setLoginLoginNameFk(self::$loginName)->setEventIdFk(self::$eventId);
        $this->enrollRepo->add($enroll);
        $this->enrollRepo->flush();

        $this->assertTrue($enroll->isPersisted());

        $container = (new EventsModelContainerConfigurator())->configure(
            (new TestDbEventsContainerConfigurator())->configure(new Container())
        );
        /** @var EnrollDao $enrollDao */
        $enrollDao = $container->get(EnrollDao::class);
        $row = $enrollDao->get([
            'login_login_name_fk' => self::$loginName,
            'event_id_fk' => self::$eventId,
        ]);
        $this->assertNotNull($row);
    }
}
