<?php
/**
 * A9 (docs/vp/zadani/A9.md): hash hesla pro účet global_role_grantor.
 *
 * Použití (na serveru nebo lokálně, PHP 8.4):
 *     php scripts/global-role-grantor-hash.php
 * Skript se zeptá na heslo (dvakrát, bez zobrazení) a vypíše řádek pro app/Site/<Site>/ConfigurationRoot.local.php.
 * Heslo se nikam neukládá.
 */
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

function readHidden(string $prompt): string {
    fwrite(STDOUT, $prompt);
    $isTty = function_exists('posix_isatty') ? @posix_isatty(STDIN) : true;
    if ($isTty && DIRECTORY_SEPARATOR === '/') {
        @shell_exec('stty -echo');
        $value = fgets(STDIN);
        @shell_exec('stty echo');
        fwrite(STDOUT, PHP_EOL);
    } else {
        $value = fgets(STDIN);   // Windows: heslo se zobrazí – spusťte v soukromí
    }
    return rtrim((string) $value, "\r\n");
}

$minLength = 12;
$password = readHidden('Heslo pro global_role_grantor: ');
if (mb_strlen($password) < $minLength) {
    fwrite(STDERR, "Heslo musí mít nejméně {$minLength} znaků." . PHP_EOL);
    exit(1);
}
if (readHidden('Heslo znovu: ') !== $password) {
    fwrite(STDERR, 'Hesla se neshodují.' . PHP_EOL);
    exit(1);
}
$hash = password_hash($password, PASSWORD_DEFAULT);
fwrite(STDOUT, PHP_EOL . "Do ConfigurationRoot.local.php vložte:" . PHP_EOL . PHP_EOL
    . "    'passwordHash' => '" . $hash . "'," . PHP_EOL . PHP_EOL);
