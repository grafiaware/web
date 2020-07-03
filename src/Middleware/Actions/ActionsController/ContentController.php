<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Middleware\Actions\ActionsController;

use Controller\PresentationFrontControllerAbstract;

use Psr\Http\Message\ServerRequestInterface;

use Pes\Application\AppFactory;
use Pes\Http\Request\RequestParams;
use Pes\Http\Response;
use Pes\Http\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;

use Model\Repository\{
    StatusSecurityRepo, StatusFlashRepo, StatusPresentationRepo, PaperContentRepo
};
use \Model\Entity\PaperContentInterface;
use Model\Entity\PaperContent;

use Pes\Text\Message;

/**
 * Description of PostController
 *
 * @author pes2704
 */
class ContentController extends PresentationFrontControllerAbstract {

    private $paperContentRepo;

    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusFlashRepo $statusFlashRepo,
            StatusPresentationRepo $statusPresentationRepo,
            PaperContentRepo $paperContentRepo) {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo);
        $this->paperContentRepo = $paperContentRepo;
    }

    private function isContent($content) {
        if (!isset($content)) {
            user_error('Neexistuje content se zadaným $contentId.');
            $this->addFlashMessage('Neexistuje content s $contentId v requestu.');
            return false;
        }
        return true;
    }

    /**
     *
     * @param ServerRequestInterface $request
     * @param string $paperId
     * @param string $contentId
     * @return type
     */
    public function updateContent(ServerRequestInterface $request, $paperId, $contentId): ResponseInterface {
        $content = $this->paperContentRepo->get($contentId);
        if ($this->isContent($content)) {
            $postContent = (new RequestParams())->getParam($request, 'content_'.$contentId);  // jméno POST proměnné je vytvořeno v paper rendereru složením 'content_' a $paper->getMenuItemId()
            $content->setContent($postContent);
            $this->addFlashMessage('updateContent');

        }
        return RedirectResponse::withPostRedirectGet(new Response(), $request->getAttribute(AppFactory::URI_INFO_ATTRIBUTE_NAME)->getSubdomainPath().'www/last/'); // 303 See Other
    }

    public function toggleContent($request, $paperId, $contentId) {
        $content = $this->paperContentRepo->get($contentId);
        if ($this->isContent($content)) {
            $active = $content->getActive() ? 0 : 1;  //active je integer
            $content->setActive($active);
            $this->addFlashMessage("content toggle(".($active?'true':'false').")");
        }
        return RedirectResponse::withPostRedirectGet(new Response(), $request->getAttribute(AppFactory::URI_INFO_ATTRIBUTE_NAME)->getSubdomainPath().'www/last/'); // 303 See Other

    }

    public function actualContent($request, $paperId, $contentId) {
        $content = $this->paperContentRepo->get($contentId);
        if ($this->isContent($content)) {
            $button = (new RequestParams())->getParam($request, 'button');
            switch ($button) {
                case 'calendar':
                    $showTime = (new RequestParams())->getParam($request, 'show');
                    $hideTime = (new RequestParams())->getParam($request, 'hide');
                    $content->setShowTime($showTime);
                    $content->setHideTime($hideTime);
                    $this->addFlashMessage("content setShowTime($showTime), setHideTime($hideTime)");
                    break;
                case 'permanent':
                    $content->setShowTime(null);
                    $content->setHideTime(null);
                    $this->addFlashMessage("content set permanent");
                    break;
                default:
                    $this->addFlashMessage("content actual: Error - unknown button name.");
                    break;
            }
        }
        return RedirectResponse::withPostRedirectGet(new Response(), $request->getAttribute(AppFactory::URI_INFO_ATTRIBUTE_NAME)->getSubdomainPath().'www/last/'); // 303 See Other
    }

    public function up($request, $paperId, $contentId) {
        $this->addFlashMessage("up - není implementováno.");
        return RedirectResponse::withPostRedirectGet(new Response(), $request->getAttribute(AppFactory::URI_INFO_ATTRIBUTE_NAME)->getSubdomainPath().'www/last/'); // 303 See Other
    }

    public function down($request, $paperId, $contentId) {
        $this->addFlashMessage("down - není implementováno.");
        return RedirectResponse::withPostRedirectGet(new Response(), $request->getAttribute(AppFactory::URI_INFO_ATTRIBUTE_NAME)->getSubdomainPath().'www/last/'); // 303 See Other
    }

    public function addAbove($request, $paperId, $contentId) {
        $contents = $this->paperContentRepo->findByReference($paperId);
        $content = $this->paperContentRepo->get($contentId);
        $selectedContentPriority = $content->getPriority();
        foreach ($contents as $contentItem) {
            /** @var PaperContentInterface $contentItem */
            if ($contentItem->getPriority()>=$selectedContentPriority) {  // obsahy s vyšší nebo stejnou prioritou - zvětším jim prioriru o 1 - vznikne díra pro $selectedContentPriority
                $contentItem->setPriority($contentItem->getPriority()+1);
            }
        }
        $newContent = new PaperContent();
        $newContent->setContent("Nový obsah");
        $newContent->setPaperIdFk($paperId);
        $newContent->setPriority($selectedContentPriority);
        $this->paperContentRepo->add($newContent);
        $this->addFlashMessage("addBelow - Nový obsah, priorita $selectedContentPriority");
        return RedirectResponse::withPostRedirectGet(new Response(), $request->getAttribute(AppFactory::URI_INFO_ATTRIBUTE_NAME)->getSubdomainPath().'www/last/'); // 303 See Other
    }

    public function addBelow($request, $paperId, $contentId) {
        $contents = $this->paperContentRepo->findByReference($paperId);
        $content = $this->paperContentRepo->get($contentId);
        $selectedContentPriority = $content->getPriority();
        foreach ($contents as $contentItem) {
            /** @var PaperContentInterface $contentItem */
            if ($contentItem->getPriority()>$selectedContentPriority) {  // obsahy s vyšší prioritou - zvětším jim prioriru o 1 - vznikne díra pro $selectedContentPriority+1
                $contentItem->setPriority($contentItem->getPriority()+1);
            }
        }
        $newContent = new PaperContent();
        $newContent->setContent("Nový obsah");
        $newContent->setPaperIdFk($paperId);
        $newContent->setPriority($selectedContentPriority+1);
        $this->paperContentRepo->add($newContent);
        $this->addFlashMessage("addBelow - Nový obsah, priorita $selectedContentPriority");
        return RedirectResponse::withPostRedirectGet(new Response(), $request->getAttribute(AppFactory::URI_INFO_ATTRIBUTE_NAME)->getSubdomainPath().'www/last/'); // 303 See Other
    }

    public function delete($request, $paperId, $contentId) {
        $this->addFlashMessage("delete - není implementováno.");
        return RedirectResponse::withPostRedirectGet(new Response(), $request->getAttribute(AppFactory::URI_INFO_ATTRIBUTE_NAME)->getSubdomainPath().'www/last/'); // 303 See Other
    }

//    $paper = $this->paperRepo->get($menuItemId) ?? $this->createPaper($menuItemId);

//    private function createPaper($menuItemId) {
//        $paper = (new Paper())->setMenuItemIdFk($menuItemId)->setLangCode($this->statusPresentation->getLanguage()->getLangCode());
//        $this->paperRepo->add($paper);
//        return $paper;
//    }
}
