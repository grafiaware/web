<?php

namespace Site\Common;

/**
 * Slučování common defaults se site overlay.
 * - asociativní pole → rekurze
 * - list (0..n) → overlay nahradí celé pole
 * - klíče v $replaceListKeys → overlay nahradí celé pole
 */
final class ConfigMerge {

    /**
     * @param array<string, mixed> $base
     * @param array<string, mixed> $overlay
     * @param list<string> $replaceListKeys
     * @return array<string, mixed>
     */
    public static function merge(array $base, array $overlay, array $replaceListKeys = []): array {
        foreach ($overlay as $key => $value) {
            if (!array_key_exists($key, $base)) {
                $base[$key] = $value;
                continue;
            }
            $baseVal = $base[$key];

            if (in_array($key, $replaceListKeys, true)
                || self::isList($value)
                || self::isList($baseVal)
            ) {
                $base[$key] = $value;
                continue;
            }

            if (is_array($value) && is_array($baseVal) && self::isAssoc($value) && self::isAssoc($baseVal)) {
                $base[$key] = self::merge($baseVal, $value, $replaceListKeys);
                continue;
            }

            $base[$key] = $value;
        }
        return $base;
    }

    /** @param mixed $value */
    public static function isList($value): bool {
        if (!is_array($value)) {
            return false;
        }
        if ($value === []) {
            return true;
        }
        return array_keys($value) === range(0, count($value) - 1);
    }

    /** @param mixed $value */
    public static function isAssoc($value): bool {
        return is_array($value) && !self::isList($value);
    }
}
