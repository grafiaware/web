<?php

namespace Menu\Middleware\Menu\Controller;

use Menu\Psr\Http\Message\RequestInterface;

use Pes\Http\Response\RedirectResponse;
use Pes\Http\Response;

use Menu\Model\Dao\Hierarchy\HierarchyAggregateEditDao;
use Menu\Model\HierarchyHooks\HookedMenuItemActor;

use Menu\Model\HierarchyHooks\ArticleTitleUpdater;

/**
 * Description of Controller
 *
 * @author pes2704
 */
class EditMenuController extends FrontControllerAbstract {

/* non REST metody */
    public function add($id) {

        /* @var $hierarchy HierarchyAggregateEditDao */
        $hierarchy = $this->container->get(HierarchyAggregateEditDao::class);
        $hierarchy->getHookedActor()->
        $uid = $hierarchy->addNode($id);
        return new RedirectResponse('/', 303);
    }

    public function addchild($id) {
        /* @var $hierarchy HierarchyAggregateEditDao */
        $hierarchy = $this->container->get(HierarchyAggregateEditDao::class);
        $uid = $hierarchy->addChildNode($id);
         return new RedirectResponse('/', 303);
    }

    public function delete($id) {
        /* @var $hierarchy HierarchyAggregateEditDao */
        $hierarchy = $this->container->get(HierarchyAggregateEditDao::class);
        $hierarchy->registerHookedActor(new HookedMenuItemActor(['cs', 'en', 'de']));
        $hierarchy->deleteLeafNode($id);
        return new RedirectResponse('/', 307);
    }

/* POST */
    public function post($id) {
        // TODO: jazyk!!!
        // POZOR - musí být shoda jmen: zde, v html view (ItemView) a v javascriptu pro editaci itemu
        // data odeslaná js scriptem
        $request = $this->container->get(RequestInterface::class);
        $title = $request->getParam('title');
        $replacedTitle = $request->getParam('original-title');
        $uid = $request->getParam('uid');
        $lang = 'cs';
        /* @var $updater ArticleTitleUpdater */
        $updater = $this->container->get(ArticleTitleUpdater::class);
        $success = $updater->updateTitle($lang, $uid, $title);
// vracím 200 OK - použití 204 NoContent způsobí, že v jQuery kódu .done(function(data, textStatus, jqXHR) je proměnná data undefined a ani jqXhr objekt neobsahuje vrácený text - jQuery předpoklákládá, že NoContent znamená NoConetent
        $response = new Response();
        $response->getBody()->write($success ? "Uložen nový název $title." : "Nepodařilo se uložit název!");
        return $response;
    }
}