<?php
namespace Model\Builder;


/**
 *
 * @author pes2704
 */
interface SqlInterface {
    public function select(array $fields = []): string;
    public function from(string $name): string;
    public function where(string $condition = ""): string;

    public function columns(array $cols): string;
    public function values(array $names): string;
    public function set(array $setTouples): string;

    /**
     * Z pole jmen vytvoří pole výrazů sloupec = :placeholder. Sloupec je jméno použité jako identifikátor a placeholder je placeholder pro prepared statement.
     * Pokud je zadán parametr placeholder prefix, jsou placeholdery prefixovány. To je potřebné pro případ, že je nutné vytvořit sql s opakujícími se (duplikátními)
     * placeholdery. Opakovaným voláním metody bez prefixu a s různými prefixy lze předejít duplicitám.
     *
     * @param array $names
     * @param string $placeholderPrefix
     * @return array
     */
    public function touples(array $names, $placeholderPrefix=''): array;

    public function and(...$conditions): string;
    public function or(...$conditions): string;
}
