<?php

namespace Events\Model\Dao\Lottery;

/**
 * D1: pomocné dotazy nad tabulkami losování (prepared statement, parametry jako pole jméno => hodnota).
 *
 * Hodnota null se váže jako SQL NULL (na rozdíl od find(), který null hodnoty nenaváže).
 */
trait LotteryQueryTrait {

    /**
     * @return array<int, array<string, mixed>>
     */
    public function rows(string $sql, array $params = []): array {
        $stmt = $this->getPreparedStatement($sql);
        foreach ($params as $name => $value) {
            $stmt->bindValue(':' . $name, $value, $value === null ? \PDO::PARAM_NULL : (is_int($value) ? \PDO::PARAM_INT : \PDO::PARAM_STR));
        }
        $stmt->execute();
        $ret = [];
        foreach ($stmt->fetchAll() as $row) {
            $ret[] = $row instanceof \ArrayObject ? $row->getArrayCopy() : (array) $row;
        }
        return $ret;
    }

    public function row(string $sql, array $params = []): ?array {
        $rows = $this->rows($sql, $params);
        return $rows[0] ?? null;
    }

    /**
     * INSERT/UPDATE/DELETE, vrací počet dotčených řádků.
     */
    public function exec(string $sql, array $params = []): int {
        $stmt = $this->getPreparedStatement($sql);
        foreach ($params as $name => $value) {
            $stmt->bindValue(':' . $name, $value, $value === null ? \PDO::PARAM_NULL : (is_int($value) ? \PDO::PARAM_INT : \PDO::PARAM_STR));
        }
        $stmt->execute();
        $this->rowCount = $stmt->rowCount();
        return $this->rowCount;
    }

    /**
     * Provede $work v transakci (pokud už transakce neběží). Výjimka = rollback.
     */
    public function transaction(callable $work): mixed {
        if ($this->dbHandler->inTransaction()) {
            return $work();
        }
        $this->dbHandler->beginTransaction();
        try {
            $ret = $work();
            $this->dbHandler->commit();
            return $ret;
        } catch (\Throwable $e) {
            $this->dbHandler->rollBack();
            throw $e;
        }
    }

    public function lastId(): int {
        return (int) $this->dbHandler->lastInsertId();
    }
}
