<?php

declare(strict_types=1);

namespace Build\Migration;

/**
 * Upraví mysqldump / baseline SQL tak, aby ho zvládnul běžný Build DBA bez SYSTEM_USER/SUPER
 * a aby Manipulator::executeSequence (split jen mimo uvozovky, bez pochopení -- komentářů)
 * nedostal ne-SQL fragmenty.
 */
final class BaselineDumpSanitizer
{
    public static function sanitize(string $sql): string
    {
        $sql = self::stripPlainComments($sql);
        $sql = self::stripDefinerClauses($sql);
        $sql = self::rewriteSecurityDefinerToInvoker($sql);
        $sql = self::stripPrivilegeAndSystemStatements($sql);
        $sql = self::stripEmptyVersionedComments($sql);
        $sql = self::stripOrphanOldVarRestores($sql);

        return trim($sql) . "\n";
    }

    /**
     * Odstraní řádkové `--` komentáře a běžné block komentáře (ponechá MySQL/MariaDB versioned).
     * Manipulator::sqlExplode dělí podle `;` i uvnitř `--` komentářů → `;` v komentáři rozbije import.
     */
    private static function stripPlainComments(string $sql): string
    {
        $sql = preg_replace('#/\*(?!\!|M!)[\s\S]*?\*/#', '', $sql) ?? $sql;

        $lines = preg_split("/\r\n|\n|\r/", $sql) ?: [];
        $out = [];
        foreach ($lines as $line) {
            if (preg_match('/^\s*--/', $line) === 1) {
                continue;
            }
            $out[] = $line;
        }

        return implode("\n", $out);
    }

    private static function stripDefinerClauses(string $sql): string
    {
        // DEFINER=`user`@`host` / DEFINER='user'@'host' / DEFINER=user@host
        $sql = preg_replace(
            '/\s*DEFINER\s*=\s*(?:`[^`]+`|\'[^\']+\'|"[^"]+"|[^\s@\'"`]+)\s*@\s*(?:`[^`]+`|\'[^\']+\'|"[^"]+"|[^\s\'"`]+)/i',
            '',
            $sql
        ) ?? $sql;

        return $sql;
    }

    /**
     * SQL SECURITY DEFINER (i bez DEFINER=user) ponechává security_type=DEFINER;
     * INVOKER = bezpečné pro Build DBA rebuild smyčku.
     */
    private static function rewriteSecurityDefinerToInvoker(string $sql): string
    {
        return preg_replace('/SQL\s+SECURITY\s+DEFINER/i', 'SQL SECURITY INVOKER', $sql) ?? $sql;
    }

    private static function stripPrivilegeAndSystemStatements(string $sql): string
    {
        $patterns = [
            '/^\s*GRANT\b[\s\S]*?;\s*$/mi',
            '/^\s*REVOKE\b[\s\S]*?;\s*$/mi',
            '/^\s*SET\s+DEFAULT\s+ROLE\b[\s\S]*?;\s*$/mi',
            '/^\s*SET\s+ROLE\b[\s\S]*?;\s*$/mi',
            '/^\s*(?:INSERT|REPLACE)\s+INTO\s+`?mysql`?\s*\.[\s\S]*?;\s*$/mi',
            '/^\s*(?:UPDATE|DELETE)\s+(?:`?mysql`?\s*\.|FROM\s+`?mysql`?\s*\.)[\s\S]*?;\s*$/mi',
            '/^\s*CREATE\s+(?:USER|ROLE)\b[\s\S]*?;\s*$/mi',
            '/^\s*ALTER\s+USER\b[\s\S]*?;\s*$/mi',
            '/^\s*DROP\s+(?:USER|ROLE)\b[\s\S]*?;\s*$/mi',
            '/^\s*FLUSH\s+PRIVILEGES\s*;\s*$/mi',
        ];
        foreach ($patterns as $pattern) {
            $sql = preg_replace($pattern, '', $sql) ?? $sql;
        }

        return $sql;
    }

    /** Po odstranění DEFINER zbývají prázdné versioned komentáře (např. 50013). */
    private static function stripEmptyVersionedComments(string $sql): string
    {
        return preg_replace('#/\*!(?:M!)?\d+\s*\*/\s*#', '', $sql) ?? $sql;
    }

    /**
     * Mysqldump epilog `SET …=@OLD_…` bez úvodního uložení session → NULL/nežádoucí stav.
     * Baseline dumpy často mají jen patičku; FK checks řídíme explicitně v rebuildu.
     */
    private static function stripOrphanOldVarRestores(string $sql): string
    {
        $names = 'TIME_ZONE|SQL_MODE|FOREIGN_KEY_CHECKS|UNIQUE_CHECKS|CHARACTER_SET_CLIENT|CHARACTER_SET_RESULTS|COLLATION_CONNECTION|NOTE_VERBOSITY';

        return preg_replace(
            '#/\*!(?:M!)?\d+\s+SET\s+(?:@@session\.)?(?:' . $names . ')\s*=\s*@OLD_[A-Z_]+\s*\*/\s*;?\s*#i',
            '',
            $sql
        ) ?? $sql;
    }
}
