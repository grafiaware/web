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

    public function touples(array $names, $placeholderPrefix=''): array;

    public function and(...$conditions): string;
    public function or(...$conditions): string;
}
