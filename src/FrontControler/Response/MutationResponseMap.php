<?php

namespace FrontControler\Response;

/**
 * @deprecated Fáze 0: transient. Nové mutace nedávej sem — `responseMode` patří do
 *             {@see \Application\Api\RouteDefinition} v modulových katalozích
 *             (`Application\Api\Catalog\*`). Tento katalog se nepoužívá za běhu;
 *             hodnoty byly překlopeny do katalogů při generování. Odstranit po
 *             dokončení vynucení response z RouteDefinition.
 *
 * Katalog mutací (POST/PUT/DELETE): jaký klientský režim a jaký typ response.
 * Výchozí mapování mode → kind: {@see self::defaultKindFor()}.
 */
final class MutationResponseMap {

    /**
     * Default ResponseKind pro MutationResponseMode (bez výjimek typu DELETE → 204).
     */
    public static function defaultKindFor(string $mode): string {
        return match ($mode) {
            MutationResponseMode::BROWSER_FORM => ResponseKind::REDIRECT_SEE_OTHER,
            MutationResponseMode::EDITOR_FETCH => ResponseKind::JSON,
            MutationResponseMode::MACHINE_API => ResponseKind::JSON,
            MutationResponseMode::INLINE_SAVE => ResponseKind::NO_CONTENT,
            default => throw new \InvalidArgumentException("Unknown MutationResponseMode: {$mode}"),
        };
    }

    /**
     * @return list<MutationResponseEntry>
     */
    public static function entries(): array {
        return array_merge(
            self::redEditorFetch(),
            self::redInlineSave(),
            self::redBrowserForm(),
            self::redMachineApi(),
            self::eventsBrowserForm(),
            self::eventsMachineApi(),
            self::authBrowserForm(),
            self::authMachineApi(),
            self::consentMachineApi(),
        );
    }

    public static function find(string $method, string $pathPattern): ?MutationResponseEntry {
        $method = strtoupper($method);
        foreach (self::entries() as $entry) {
            if ($entry->method === $method && $entry->pathPattern === $pathPattern) {
                return $entry;
            }
        }
        return null;
    }

    /**
     * @return list<MutationResponseEntry>
     */
    public static function entriesForMode(string $mode): array {
        return array_values(array_filter(
            self::entries(),
            static fn(MutationResponseEntry $e) => $e->mode === $mode
        ));
    }

    /**
     * Red: hierarchy / menu / itemaction — menuSwap očekává JSON refresh.
     *
     * @return list<MutationResponseEntry>
     */
    private static function redEditorFetch(): array {
        $m = MutationResponseMode::EDITOR_FETCH;
        $k = ResponseKind::JSON;
        $hierarchy = [
            '/red/v1/hierarchy/:uid/add',
            '/red/v1/hierarchy/:uid/addchild',
            '/red/v1/hierarchy/:uid/cut',
            '/red/v1/hierarchy/:uid/copy',
            '/red/v1/hierarchy/:uid/cutcopyescape',
            '/red/v1/hierarchy/:uid/paste',
            '/red/v1/hierarchy/:uid/pastechild',
            '/red/v1/hierarchy/:uid/delete',
            '/red/v1/hierarchy/:uid/trash',
        ];
        $entries = [];
        foreach (['PUT', 'POST'] as $method) {
            foreach ($hierarchy as $path) {
                $entries[] = new MutationResponseEntry($method, $path, $m, $k, 'HierarchyControler → menuSwap refresh JSON');
            }
        }
        $entries[] = new MutationResponseEntry('PUT', '/red/v1/itemaction/:itemId/add', $m, $k, 'ItemActionControler');
        $entries[] = new MutationResponseEntry('PUT', '/red/v1/itemaction/:itemId/remove', $m, $k, 'ItemActionControler');
        $entries[] = new MutationResponseEntry('PUT', '/red/v1/menu/:menuItemUidFk/toggle', $m, $k, 'ItemEditControler');
        $entries[] = new MutationResponseEntry('POST', '/red/v1/menu/:menuItemUidFk/toggle', $m, $k, 'ItemEditControler');
        $entries[] = new MutationResponseEntry('POST', '/red/v1/menu/:menuItemUidFk/title', $m, $k, 'ItemEditControler');
        $entries[] = new MutationResponseEntry('POST', '/red/v1/upload/image', $m, $k, 'FilesUploadControler TinyMCE {location}');
        $entries[] = new MutationResponseEntry('POST', '/red/v1/upload/attachment', $m, $k, 'FilesUploadControler TinyMCE {location}');
        return $entries;
    }

    /**
     * Red: inline content save bez refresh payloadu.
     *
     * @return list<MutationResponseEntry>
     */
    private static function redInlineSave(): array {
        $m = MutationResponseMode::INLINE_SAVE;
        $k = ResponseKind::NO_CONTENT;
        return [
            new MutationResponseEntry('POST', '/red/v1/paper/:paperId/headline', $m, $k, 'PaperControler::updateHeadline'),
            new MutationResponseEntry('POST', '/red/v1/paper/:paperId/perex', $m, $k, 'PaperControler::updatePerex'),
            new MutationResponseEntry('POST', '/red/v1/article/:articleId', $m, $k, 'ArticleControler content save'),
            new MutationResponseEntry('POST', '/red/v1/section/:sectionId', $m, $k, 'SectionsControler::update'),
        ];
    }

    /**
     * Red: HTML admin formuláře → PRG.
     *
     * @return list<MutationResponseEntry>
     */
    private static function redBrowserForm(): array {
        $m = MutationResponseMode::BROWSER_FORM;
        $k = ResponseKind::REDIRECT_SEE_OTHER;
        return [
            new MutationResponseEntry('POST', '/red/v1/presentation/language', $m, $k, 'PresentationActionControler'),
            new MutationResponseEntry('POST', '/red/v1/presentation/editoraction', $m, $k, 'PresentationActionControler'),
            new MutationResponseEntry('POST', '/red/v1/paper/:paperId/template', $m, $k, 'PaperControler template'),
            new MutationResponseEntry('POST', '/red/v1/paper/:paperId/templateremove', $m, $k, 'PaperControler'),
            new MutationResponseEntry('POST', '/red/v1/article/:articleId/template', $m, $k, 'ArticleControler template'),
            new MutationResponseEntry('POST', '/red/v1/multipage/:multipageId/template', $m, $k, 'MultipageControler'),
            new MutationResponseEntry('POST', '/red/v1/multipage/:multipageId/templateremove', $m, $k, 'MultipageControler'),
            new MutationResponseEntry('POST', '/red/v1/static/:staticId', $m, $k, 'StaticControler'),
            new MutationResponseEntry('POST', '/red/v1/static/registry/push-sync-ui', $m, $k, 'StaticRegistryPushSyncControler::pushSyncUi flash+PRG'),
            new MutationResponseEntry('POST', '/red/v1/section/:sectionId/toggle', $m, $k, 'SectionsControler (form paths; část může migrovat na JSON)'),
            new MutationResponseEntry('POST', '/red/v1/section/:sectionId/trash', $m, $k, 'SectionsControler'),
            new MutationResponseEntry('POST', '/red/v1/section/:sectionId/delete', $m, $k, 'SectionsControler'),
        ];
    }

    /**
     * Red: machine / ops JSON.
     *
     * @return list<MutationResponseEntry>
     */
    private static function redMachineApi(): array {
        return [
            new MutationResponseEntry(
                'POST',
                '/red/v1/static/registry/push-sync',
                MutationResponseMode::MACHINE_API,
                ResponseKind::JSON,
                'StaticRegistryPushSyncControler::pushSync'
            ),
        ];
    }

    /**
     * Events: klasické admin/visitor formuláře → PRG.
     *
     * @return list<MutationResponseEntry>
     */
    private static function eventsBrowserForm(): array {
        $m = MutationResponseMode::BROWSER_FORM;
        $k = ResponseKind::REDIRECT_SEE_OTHER;
        $paths = [
            '/events/v1/filterjob',
            '/events/v1/cleanfilterjob',
            '/events/v1/representation',
            '/events/v1/enroll',
            '/events/v1/company',
            '/events/v1/company/:companyId',
            '/events/v1/company/:companyId/remove',
            '/events/v1/company/:companyId/companycontact',
            '/events/v1/company/:companyId/companycontact/:companyContactId',
            '/events/v1/company/:companyId/companycontact/:companyContactId/remove',
            '/events/v1/company/:companyId/companyaddress',
            '/events/v1/company/:companyId/companyaddress/:companyIdA',
            '/events/v1/company/:companyId/companyaddress/:companyIdA/remove',
            '/events/v1/company/:companyId/companyinfo',
            '/events/v1/company/:companyId/companyinfo/:companyIdA',
            '/events/v1/company/:companyId/companyinfo/:companyIdA/remove',
            '/events/v1/representative',
            '/events/v1/representative/:loginLoginName/:companyId/remove',
            '/events/v1/visitorprofile',
            '/events/v1/visitorprofile/:loginname',
            '/events/v1/visitorprofile/:parentId/doctype/:type',
            '/events/v1/visitorprofile/:parentId/doctype/:type/remove',
            '/events/v1/uploadvisitorfile',
            '/events/v1/job/:jobId/jobrequest',
            '/events/v1/job/:jobId/jobrequest/:loginloginname',
            '/events/v1/job/:jobId/jobrequest/:loginloginname/send',
            '/events/v1/company/:companyId/job',
            '/events/v1/company/:companyId/job/:jobId',
            '/events/v1/company/:companyId/job/:jobId/remove',
            '/events/v1/jobtag',
            '/events/v1/jobtag/:jobTagId',
            '/events/v1/jobtag/:jobTagId/remove',
            '/events/v1/job/:jobId/jobtotag',
            '/events/v1/institution',
            '/events/v1/institution/:institutionId',
            '/events/v1/institution/:institutionId/remove',
            '/events/v1/eventcontent',
            '/events/v1/eventcontent/:idContent',
            '/events/v1/eventcontent/:idContent/remove',
            '/events/v1/eventlink',
            '/events/v1/eventlink/:eventLinkId',
            '/events/v1/eventlink/:eventLinkId/remove',
        ];
        $entries = [];
        foreach ($paths as $path) {
            $entries[] = new MutationResponseEntry('POST', $path, $m, $k, 'Events HTML form → redirectSeeLastGet');
        }
        return $entries;
    }

    /**
     * Events: registry API + synchro JSON.
     *
     * @return list<MutationResponseEntry>
     */
    private static function eventsMachineApi(): array {
        $m = MutationResponseMode::MACHINE_API;
        return [
            new MutationResponseEntry('PUT', '/events/v1/static/registry/:menuItemId', $m, ResponseKind::JSON, 'StaticRegistryControler upsert'),
            new MutationResponseEntry('DELETE', '/events/v1/static/registry/:menuItemId', $m, ResponseKind::NO_CONTENT, 'StaticRegistryControler delete 204'),
            new MutationResponseEntry('POST', '/events/v1/synchro', $m, ResponseKind::JSON, 'Events SynchroControler'),
            new MutationResponseEntry('POST', '/events/v1/validateuser', $m, ResponseKind::JSON, 'Events SynchroControler'),
            new MutationResponseEntry('POST', '/events/v1/maintenance/archivecompanies/:sourceVersion/:targetVersion', $m, ResponseKind::JSON, 'MaintenanceControler'),
        ];
    }

    /**
     * Auth: login / register / password formuláře → PRG.
     *
     * @return list<MutationResponseEntry>
     */
    private static function authBrowserForm(): array {
        $m = MutationResponseMode::BROWSER_FORM;
        $k = ResponseKind::REDIRECT_SEE_OTHER;
        $paths = [
            '/auth/v1/logout',
            '/auth/v1/login',
            '/auth/v1/register',
            '/auth/v1/register1',
            '/auth/v1/forgottenpassword',
            '/auth/v1/changepassword',
            '/auth/v1/credentials/:loginnamefk',
            '/auth/v1/role',
            '/auth/v1/role/:role',
            '/auth/v1/role/:role/remove',
        ];
        $entries = [];
        foreach ($paths as $path) {
            $entries[] = new MutationResponseEntry('POST', $path, $m, $k, 'Auth HTML form → redirectSeeLastGet');
        }
        return $entries;
    }

    /**
     * Auth: registry + synchro JSON.
     *
     * @return list<MutationResponseEntry>
     */
    private static function authMachineApi(): array {
        $m = MutationResponseMode::MACHINE_API;
        return [
            new MutationResponseEntry('PUT', '/auth/v1/static/registry/:menuItemId', $m, ResponseKind::JSON, 'StaticRegistryControler upsert'),
            new MutationResponseEntry('DELETE', '/auth/v1/static/registry/:menuItemId', $m, ResponseKind::NO_CONTENT, 'StaticRegistryControler delete 204'),
            new MutationResponseEntry('POST', '/auth/v1/synchro', $m, ResponseKind::JSON, 'Auth SynchroControler'),
            new MutationResponseEntry('POST', '/auth/v1/validuser', $m, ResponseKind::JSON, 'Auth SynchroControler'),
        ];
    }

    /**
     * Consent beacon.
     *
     * @return list<MutationResponseEntry>
     */
    private static function consentMachineApi(): array {
        return [
            new MutationResponseEntry(
                'POST',
                '/consent/v1/log',
                MutationResponseMode::MACHINE_API,
                ResponseKind::JSON,
                'LogControler createJsonPostCreatedResponse 201'
            ),
        ];
    }
}
