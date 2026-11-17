<?php
declare(strict_types=1);

namespace Test\Integration\Event\Fixture;

use Pes\Container\Container;
use Container\EventsModelContainerConfigurator;
use Test\Integration\Event\Container\TestDbEventsContainerConfigurator;
use Events\Model\Dao\EventContentTypeDao;
use Pes\Model\RowData\RowData;

/**
 * G0 (#116): typ obsahu eventu pro integrační testy místo natvrdo zadaného id 3 (v čisté DB neexistuje).
 * id() založí typ s prefixem testu (jednou za běh), remove() ho smaže, když na něj už nic neodkazuje.
 */
final class TestContentType {

    const PREFIX = 'test-integration-';

    private static ?int $id = null;

    private static function dao(): EventContentTypeDao {
        $container = (new EventsModelContainerConfigurator())->configure(
            (new TestDbEventsContainerConfigurator())->configure(new Container()));
        return $container->get(EventContentTypeDao::class);
    }

    public static function id(): int {
        if (self::$id === null) {
            $dao = self::dao();
            $row = new RowData();
            $row->import(['type' => self::PREFIX . uniqid(), 'name' => 'Typ pro integrační test']);
            $dao->insert($row);
            self::$id = (int) $dao->getLastInsertedPrimaryKey()['id'];
        }
        return self::$id;
    }

    public static function remove(): void {
        if (self::$id !== null) {
            try {
                self::dao()->delete(new RowData(['id' => self::$id]));
                self::$id = null;
            } catch (\Throwable $e) {
                // na typ ještě odkazuje obsah jiného testu – smaže se při dalším remove()
            }
        }
    }
}
