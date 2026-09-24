<?php

namespace Application\Api\Catalog;

use Application\Api\ModuleRouteCatalogInterface;
use Application\Api\RouteDefinition;
use FrontControler\Response\MutationResponseMode;
use Red\Middleware\Redactor\Controler\ArticleControler;
use Red\Middleware\Redactor\Controler\ComponentRedControler;
use Red\Middleware\Redactor\Controler\ComponentStaticControler;
use Red\Middleware\Redactor\Controler\FilesUploadControler;
use Red\Middleware\Redactor\Controler\HierarchyControler;
use Red\Middleware\Redactor\Controler\ItemActionControler;
use Red\Middleware\Redactor\Controler\ItemEditControler;
use Red\Middleware\Redactor\Controler\MenuControler;
use Red\Middleware\Redactor\Controler\MultipageControler;
use Red\Middleware\Redactor\Controler\PaperControler;
use Red\Middleware\Redactor\Controler\PresentationActionControler;
use Red\Middleware\Redactor\Controler\SectionsControler;
use Red\Middleware\Redactor\Controler\StaticControler;
use Red\Middleware\Redactor\Controler\StaticRegistryPushSyncControler;
use Red\Middleware\Redactor\Controler\TemplateControler;

/**
 * Modulový katalog API rout — jediné místo deklarace pro tento modul.
 */
final class RedRouteCatalog implements ModuleRouteCatalogInterface {

    public static function definitions(): array {
        return [
            new RouteDefinition('GET', '/red/v1/static/:menuItemId', ComponentStaticControler::class, 'static', null),
            new RouteDefinition('GET', '/red/v1/service/:name', ComponentRedControler::class, 'serviceComponent', null),
            new RouteDefinition('GET', '/red/v1/component/:name', ComponentRedControler::class, 'component', null),
            new RouteDefinition('GET', '/red/v1/root/:menuItemId', ComponentRedControler::class, 'root', null),
            new RouteDefinition('GET', '/red/v1/empty/:menuItemId', ComponentRedControler::class, 'empty', null),
            new RouteDefinition('GET', '/red/v1/select/:menuItemId', ComponentRedControler::class, 'select', null),
            new RouteDefinition('GET', '/red/v1/paper/:menuItemId', ComponentRedControler::class, 'paper', null),
            new RouteDefinition('GET', '/red/v1/article/:menuItemId', ComponentRedControler::class, 'article', null),
            new RouteDefinition('GET', '/red/v1/multipage/:menuItemId', ComponentRedControler::class, 'multipage', null),
            new RouteDefinition('GET', '/red/v1/presenteddriver/:uid', MenuControler::class, 'presentedDriver', null),
            new RouteDefinition('GET', '/red/v1/driver/:uid', MenuControler::class, 'driver', null),
            new RouteDefinition('GET', '/red/v1/templateslist/:type', TemplateControler::class, 'templatesList', null),
            new RouteDefinition('GET', '/red/v1/papertemplate/:folder', TemplateControler::class, 'papertemplate', null),
            new RouteDefinition('GET', '/red/v1/articletemplate/:folder', TemplateControler::class, 'articletemplate', null),
            new RouteDefinition('GET', '/red/v1/multipagetemplate/:folder', TemplateControler::class, 'multipagetemplate', null),
            new RouteDefinition('GET', '/red/v1/authortemplate/:name', TemplateControler::class, 'authorTemplate', null),
            new RouteDefinition('POST', '/red/v1/presentation/language', PresentationActionControler::class, 'setLangCode', MutationResponseMode::BROWSER_FORM),
            new RouteDefinition('POST', '/red/v1/presentation/editoraction', PresentationActionControler::class, 'setEditorAction', MutationResponseMode::BROWSER_FORM),
            new RouteDefinition('PUT', '/red/v1/itemaction/:itemId/add', ItemActionControler::class, 'addUserItemAction', MutationResponseMode::EDITOR_FETCH),
            new RouteDefinition('PUT', '/red/v1/itemaction/:itemId/remove', ItemActionControler::class, 'removeUserItemAction', MutationResponseMode::EDITOR_FETCH),
            new RouteDefinition('POST', '/red/v1/paper/:paperId/template', PaperControler::class, 'template', MutationResponseMode::BROWSER_FORM),
            new RouteDefinition('POST', '/red/v1/paper/:paperId/templateremove', PaperControler::class, 'templateRemove', MutationResponseMode::BROWSER_FORM),
            new RouteDefinition('POST', '/red/v1/paper/:paperId/headline', PaperControler::class, 'updateHeadline', MutationResponseMode::INLINE_SAVE),
            new RouteDefinition('POST', '/red/v1/paper/:paperId/perex', PaperControler::class, 'updatePerex', MutationResponseMode::INLINE_SAVE),
            new RouteDefinition('POST', '/red/v1/article/:articleId', ArticleControler::class, 'update', MutationResponseMode::INLINE_SAVE),
            new RouteDefinition('POST', '/red/v1/article/:articleId/template', ArticleControler::class, 'template', MutationResponseMode::BROWSER_FORM),
            new RouteDefinition('POST', '/red/v1/multipage/:multipageId/template', MultipageControler::class, 'template', MutationResponseMode::BROWSER_FORM),
            new RouteDefinition('POST', '/red/v1/multipage/:multipageId/templateremove', MultipageControler::class, 'templateRemove', MutationResponseMode::BROWSER_FORM),
            new RouteDefinition('PUT', '/red/v1/paper/:paperId/section', SectionsControler::class, 'add', null),
            new RouteDefinition('POST', '/red/v1/section/:sectionId', SectionsControler::class, 'update', MutationResponseMode::INLINE_SAVE),
            new RouteDefinition('POST', '/red/v1/section/:sectionId/toggle', SectionsControler::class, 'toggle', MutationResponseMode::BROWSER_FORM),
            new RouteDefinition('POST', '/red/v1/section/:sectionId/actual', SectionsControler::class, 'actual', null),
            new RouteDefinition('POST', '/red/v1/section/:sectionId/event', SectionsControler::class, 'event', null),
            new RouteDefinition('POST', '/red/v1/section/:sectionId/up', SectionsControler::class, 'up', null),
            new RouteDefinition('POST', '/red/v1/section/:sectionId/down', SectionsControler::class, 'down', null),
            new RouteDefinition('POST', '/red/v1/section/:sectionId/cut', SectionsControler::class, 'cut', null),
            new RouteDefinition('POST', '/red/v1/section/:sectionId/copy', SectionsControler::class, 'copy', null),
            new RouteDefinition('POST', '/red/v1/section/:sectionId/cutescape', SectionsControler::class, 'cutEscape', null),
            new RouteDefinition('POST', '/red/v1/section/:sectionId/pasteabove', SectionsControler::class, 'pasteAbove', null),
            new RouteDefinition('POST', '/red/v1/section/:sectionId/pastebelow', SectionsControler::class, 'pasteBelow', null),
            new RouteDefinition('POST', '/red/v1/section/:sectionId/addabove', SectionsControler::class, 'addAbove', null),
            new RouteDefinition('POST', '/red/v1/section/:sectionId/addbelow', SectionsControler::class, 'addBelow', null),
            new RouteDefinition('POST', '/red/v1/section/:sectionId/trash', SectionsControler::class, 'trash', MutationResponseMode::BROWSER_FORM),
            new RouteDefinition('POST', '/red/v1/section/:sectionId/restore', SectionsControler::class, 'restore', null),
            new RouteDefinition('POST', '/red/v1/section/:sectionId/delete', SectionsControler::class, 'delete', MutationResponseMode::BROWSER_FORM),
            new RouteDefinition('POST', '/red/v1/static/:staticId', StaticControler::class, 'update', MutationResponseMode::BROWSER_FORM),
            new RouteDefinition('POST', '/red/v1/static/registry/push-sync', StaticRegistryPushSyncControler::class, 'pushSync', MutationResponseMode::MACHINE_API),
            new RouteDefinition('POST', '/red/v1/static/registry/push-sync-ui', StaticRegistryPushSyncControler::class, 'pushSyncUi', MutationResponseMode::BROWSER_FORM),
            new RouteDefinition('PUT', '/red/v1/menu/:menuItemUidFk/toggle', ItemEditControler::class, 'toggle', MutationResponseMode::EDITOR_FETCH),
            new RouteDefinition('POST', '/red/v1/menu/:menuItemUidFk/toggle', ItemEditControler::class, 'toggle', MutationResponseMode::EDITOR_FETCH),
            new RouteDefinition('POST', '/red/v1/menu/:menuItemUidFk/title', ItemEditControler::class, 'title', MutationResponseMode::EDITOR_FETCH),
            new RouteDefinition('POST', '/red/v1/menu/:menuItemUidFk/type', ItemEditControler::class, 'type', null),
            new RouteDefinition('PUT', '/red/v1/hierarchy/:uid/add', HierarchyControler::class, 'add', MutationResponseMode::EDITOR_FETCH),
            new RouteDefinition('PUT', '/red/v1/hierarchy/:uid/addchild', HierarchyControler::class, 'addChild', MutationResponseMode::EDITOR_FETCH),
            new RouteDefinition('PUT', '/red/v1/hierarchy/:uid/cut', HierarchyControler::class, 'cut', MutationResponseMode::EDITOR_FETCH),
            new RouteDefinition('PUT', '/red/v1/hierarchy/:uid/copy', HierarchyControler::class, 'copy', MutationResponseMode::EDITOR_FETCH),
            new RouteDefinition('PUT', '/red/v1/hierarchy/:uid/cutcopyescape', HierarchyControler::class, 'cutEscape', MutationResponseMode::EDITOR_FETCH),
            new RouteDefinition('PUT', '/red/v1/hierarchy/:uid/paste', HierarchyControler::class, 'paste', MutationResponseMode::EDITOR_FETCH),
            new RouteDefinition('PUT', '/red/v1/hierarchy/:uid/pastechild', HierarchyControler::class, 'pasteChild', MutationResponseMode::EDITOR_FETCH),
            new RouteDefinition('PUT', '/red/v1/hierarchy/:uid/delete', HierarchyControler::class, 'delete', MutationResponseMode::EDITOR_FETCH),
            new RouteDefinition('PUT', '/red/v1/hierarchy/:uid/trash', HierarchyControler::class, 'trash', MutationResponseMode::EDITOR_FETCH),
            new RouteDefinition('POST', '/red/v1/hierarchy/:uid/add', HierarchyControler::class, 'add', MutationResponseMode::EDITOR_FETCH),
            new RouteDefinition('POST', '/red/v1/hierarchy/:uid/addchild', HierarchyControler::class, 'addChild', MutationResponseMode::EDITOR_FETCH),
            new RouteDefinition('POST', '/red/v1/hierarchy/:uid/cut', HierarchyControler::class, 'cut', MutationResponseMode::EDITOR_FETCH),
            new RouteDefinition('POST', '/red/v1/hierarchy/:uid/copy', HierarchyControler::class, 'copy', MutationResponseMode::EDITOR_FETCH),
            new RouteDefinition('POST', '/red/v1/hierarchy/:uid/cutcopyescape', HierarchyControler::class, 'cutEscape', MutationResponseMode::EDITOR_FETCH),
            new RouteDefinition('POST', '/red/v1/hierarchy/:uid/paste', HierarchyControler::class, 'paste', MutationResponseMode::EDITOR_FETCH),
            new RouteDefinition('POST', '/red/v1/hierarchy/:uid/pastechild', HierarchyControler::class, 'pasteChild', MutationResponseMode::EDITOR_FETCH),
            new RouteDefinition('POST', '/red/v1/hierarchy/:uid/delete', HierarchyControler::class, 'delete', MutationResponseMode::EDITOR_FETCH),
            new RouteDefinition('POST', '/red/v1/hierarchy/:uid/trash', HierarchyControler::class, 'trash', MutationResponseMode::EDITOR_FETCH),
            new RouteDefinition('POST', '/red/v1/upload/image', FilesUploadControler::class, 'imageUpload', MutationResponseMode::EDITOR_FETCH),
            new RouteDefinition('POST', '/red/v1/upload/attachment', FilesUploadControler::class, 'attachmentUpload', MutationResponseMode::EDITOR_FETCH),
        ];
    }
}
