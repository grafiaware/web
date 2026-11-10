<?php

namespace Auth\Service;

/**
 * A6: chyba při zakládání účtu – zpráva je česky pro uživatele, $httpStatus 400 (neplatný vstup) nebo 403 (oprávnění).
 */
final class AccountException extends \RuntimeException {

    public function __construct(string $message, public readonly int $httpStatus, ?\Throwable $previous = null) {
        parent::__construct($message, $httpStatus, $previous);
    }

    public static function invalid(string $message, ?\Throwable $previous = null): self {
        return new self($message, 400, $previous);
    }

    public static function forbidden(string $message): self {
        return new self($message, 403);
    }
}
