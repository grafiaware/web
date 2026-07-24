<?php

namespace StaticRegistry\Model\Storage;

use PDO;
use PDOException;
use StaticRegistry\Model\Entity\StaticRegistryEntry;

/**
 * SQLite úložiště metadat static stránek na auth/events serveru.
 *
 * Auth/events nemají připojení k red DB — path a template se sem synchronizují
 * pushem z red modulu. Primární klíč je menu_item_id (stejné ID jako v red).
 *
 * @author pes2704
 */
class StaticRegistryStorage {

    private PDO $pdo;

    /**
     * @param string $dbFilePath Cesta k sqlite souboru (vytvoří adresář i schéma při prvním použití)
     */
    public function __construct(string $dbFilePath) {
        $directory = dirname($dbFilePath);
        if (!is_dir($directory) && !mkdir($directory, 0755, true) && !is_dir($directory)) {
            throw new PDOException("Nelze vytvořit adresář pro SQLite databázi: $directory");
        }
        $this->pdo = new PDO('sqlite:' . $dbFilePath);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->initSchema();
    }

    private function initSchema(): void {
        $this->pdo->exec(
            'CREATE TABLE IF NOT EXISTS static_registry (
                menu_item_id INTEGER PRIMARY KEY,
                red_static_id INTEGER NOT NULL,
                path TEXT NOT NULL DEFAULT \'\',
                template TEXT NOT NULL DEFAULT \'\',
                creator TEXT,
                updated TEXT NOT NULL,
                site_code TEXT NOT NULL
            )'
        );
        $this->pdo->exec('CREATE INDEX IF NOT EXISTS idx_static_registry_updated ON static_registry(updated)');
    }

    /**
     * Upsert = INSERT ... ON CONFLICT DO UPDATE.
     * Pokud lokální záznam má stejný nebo novější updated, zápis se přeskočí.
     *
     * @return bool true pokud byl záznam zapsán/aktualizován, false pokud byl přeskočen
     */
    public function upsert(StaticRegistryEntry $entry): bool {
        $existing = $this->getByMenuItemId($entry->getMenuItemId());
        // Porovnání ISO 8601 řetězců — starší nebo stejná verze se nepřepisuje
        if ($existing !== null && $existing->getUpdated() >= $entry->getUpdated()) {
            return false;
        }
        $stmt = $this->pdo->prepare(
            'INSERT INTO static_registry (menu_item_id, red_static_id, path, template, creator, updated, site_code)
             VALUES (:menu_item_id, :red_static_id, :path, :template, :creator, :updated, :site_code)
             ON CONFLICT(menu_item_id) DO UPDATE SET
                red_static_id = excluded.red_static_id,
                path = excluded.path,
                template = excluded.template,
                creator = excluded.creator,
                updated = excluded.updated,
                site_code = excluded.site_code'
        );
        $stmt->execute([
            ':menu_item_id' => $entry->getMenuItemId(),
            ':red_static_id' => $entry->getRedStaticId(),
            ':path' => $entry->getPath(),
            ':template' => $entry->getTemplate(),
            ':creator' => $entry->getCreator(),
            ':updated' => $entry->getUpdated(),
            ':site_code' => $entry->getSiteCode(),
        ]);
        return true;
    }

    public function delete(int $menuItemId): void {
        $stmt = $this->pdo->prepare('DELETE FROM static_registry WHERE menu_item_id = :menu_item_id');
        $stmt->execute([':menu_item_id' => $menuItemId]);
    }

    public function getByMenuItemId(int $menuItemId): ?StaticRegistryEntry {
        $stmt = $this->pdo->prepare('SELECT * FROM static_registry WHERE menu_item_id = :menu_item_id');
        $stmt->execute([':menu_item_id' => $menuItemId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row === false) {
            return null;
        }
        return $this->hydrate($row);
    }

    /**
     * @return StaticRegistryEntry[]
     */
    public function findAllForModulePush(string $siteCode, string $apiModule): array {
        unset($apiModule); // rezervováno pro budoucí filtr podle api_module
        $stmt = $this->pdo->prepare('SELECT * FROM static_registry WHERE site_code = :site_code ORDER BY menu_item_id');
        $stmt->execute([':site_code' => $siteCode]);
        $entries = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $entries[] = $this->hydrate($row);
        }
        return $entries;
    }

    /**
     * @param array<string, mixed> $row
     */
    private function hydrate(array $row): StaticRegistryEntry {
        return (new StaticRegistryEntry())
            ->setMenuItemId((int) $row['menu_item_id'])
            ->setRedStaticId((int) $row['red_static_id'])
            ->setPath((string) $row['path'])
            ->setTemplate((string) $row['template'])
            ->setCreator($row['creator'] !== null ? (string) $row['creator'] : null)
            ->setUpdated((string) $row['updated'])
            ->setSiteCode((string) $row['site_code']);
    }
}
