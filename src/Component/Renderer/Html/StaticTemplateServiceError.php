<?php

namespace Component\Renderer\Html;

use Psr\Container\NotFoundExceptionInterface;
use Throwable;

/**
 * #191: srozumitelná hláška, když šablona static stránky žádá z kontejneru službu, kterou kontejner nemá.
 *
 * Typická příčina: položka menu (static) založená s jiným modulem (api_module_fk), než jehož služby šablona používá –
 * šablona se pak vykreslí kontejnerem jiného modulu. Šablony se mění, kontrola při založení položky není možná,
 * proto se chyba rozpozná až za běhu: v řetězci výjimek (PhpTemplateRenderer obaluje výjimky z každé vnořené
 * šablony do TemplateRenderingException) se hledá NotFoundExceptionInterface z kontejneru.
 *
 * Modul služby se odvodí z prvního segmentu jména služby, má-li jméno tvar FQCN (vzniklo z Trida::class),
 * např. Events\Middleware\Events\ViewModel\ProgramViewModel → events.
 */
class StaticTemplateServiceError {

    /** První segment namespace → api modul položky menu. */
    public const NAMESPACE_MODULES = [
        'Events' => 'events',
        'Auth' => 'auth',
        'Red' => 'red',
    ];

    /**
     * @param string|null $apiModule modul položky menu, jejímž kontejnerem se šablona vykresluje (null = výchozí red)
     * @return string|null text hlášky, null = výjimka nevznikla chybějící službou kontejneru
     */
    public static function message(Throwable $e, ?string $apiModule): ?string {
        $notFound = self::findNotFound($e);
        if (!isset($notFound)) {
            return null;
        }
        $service = self::serviceName($notFound->getMessage());
        $module = isset($service) ? self::moduleOfService($service) : null;
        $itemModule = $apiModule ?: 'red';

        $text = 'Static stránku nelze zobrazit: šablona požaduje službu '
            . (isset($service) ? "„{$service}“ " : '')
            . "a kontejner modulu „{$itemModule}“ (modul položky menu) ji nemá.";
        if (isset($module) && $module !== $itemModule) {
            $text .= " Služba patří modulu „{$module}“ – položku menu je třeba založit jako statickou stránku modulu „{$module}“.";
        }
        return $text;
    }

    private static function findNotFound(Throwable $e): ?NotFoundExceptionInterface {
        for ($current = $e; isset($current); $current = $current->getPrevious()) {
            if ($current instanceof NotFoundExceptionInterface) {
                return $current;
            }
        }
        return null;
    }

    /** Jméno služby ze zprávy Pes\Container: „Volání služby kontejneru get('Jmeno') selhalo…“ */
    public static function serviceName(string $message): ?string {
        return preg_match("/get\\('([^']+)'\\)/", $message, $m) ? $m[1] : null;
    }

    /** Modul podle prvního segmentu FQCN; null, pokud jméno není FQCN nebo segment není modul. */
    public static function moduleOfService(string $service): ?string {
        if (!str_contains($service, '\\')) {
            return null;
        }
        $first = strstr(ltrim($service, '\\'), '\\', true);
        return self::NAMESPACE_MODULES[$first] ?? null;
    }
}
