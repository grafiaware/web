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
        return $this->redirectSeeOther($request,'www/last/'); // 303 See Other
    }

    public function toggleContent($request, $paperId, $contentId) {
        $content = $this->paperContentRepo->get($contentId);
        if ($this->isContent($content)) {
            $active = $content->getActive() ? 0 : 1;  //active je integer
            $content->setActive($active);
            $this->addFlashMessage("content toggle(".($active?'true':'false').")");
        }
        return $this->redirectSeeOther($request,'www/last/'); // 303 See Other

    }

    public function actualContent($request, $paperId, $contentId) {
        $content = $this->paperContentRepo->get($contentId);
        if ($this->isContent($content)) {
            $button = (new RequestParams())->getParam($request, 'button');
            switch ($button) {
                case 'calendar':
                    $showTime = preg_replace('/\s+/', '', (new RequestParams())->getParam($request, 'show'));
                    $hideTime = preg_replace('/\s+/', '', (new RequestParams())->getParam($request, 'hide'));
                    $content->setShowTime(\DateTime::createFromFormat('d.m.Y', $showTime));
                    $content->setHideTime(\DateTime::createFromFormat('d.m.Y', $hideTime));
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
        return $this->redirectSeeOther($request,'www/last/'); // 303 See Other
    }

    public function up($request, $paperId, $contentId) {
        $contents = $this->paperContentRepo->findByReference($paperId);
        $content = $this->paperContentRepo->get($contentId);
        $selectedContentPriority = $content->getPriority();
        foreach ($contents as $contentItem) {
            /** @var PaperContentInterface $contentItem */
            if ($contentItem->getPriority() == $selectedContentPriority+1) {  // obsahy s vyšší nebo stejnou prioritou - zvětším jim prioriru o 1 - vznikne díra pro $selectedContentPriority
                $contentItem->setPriority($selectedContentPriority);
                $content->setPriority($selectedContentPriority+1);
                $this->addFlashMessage("content down - priority $selectedContentPriority -> ".($selectedContentPriority+1));
            }
        }
        return $this->redirectSeeOther($request,'www/last/'); // 303 See Other
    }

    public function down($request, $paperId, $contentId) {
        $contents = $this->paperContentRepo->findByReference($paperId);
        $content = $this->paperContentRepo->get($contentId);
        $selectedContentPriority = $content->getPriority();
        foreach ($contents as $contentItem) {
            /** @var PaperContentInterface $contentItem */
            if ($contentItem->getPriority() == $selectedContentPriority-1) {  // obsahy s vyšší nebo stejnou prioritou - zvětším jim prioriru o 1 - vznikne díra pro $selectedContentPriority
                $contentItem->setPriority($selectedContentPriority);
                $content->setPriority($selectedContentPriority-1);
                $this->addFlashMessage("content down - priority $selectedContentPriority -> ".($selectedContentPriority-1));
            }
        }
        return $this->redirectSeeOther($request,'www/last/'); // 303 See Other
    }

    public function add($request, $paperId) {
        $priority = 1;
        // pro případ volání add i v situaci, kdy již existuje obsah
        $contents = $this->paperContentRepo->findByReference($paperId);
        foreach ($contents as $contentItem) {
            /** @var PaperContentInterface $contentItem */
            if ($contentItem->getPriority()>$priority) {
                $contentItem->setPriority($contentItem->getPriority());
            }
        }
        $this->paperContentRepo->add($this->createNewContent($paperId, $priority));
        $this->addFlashMessage("add - Nový obsah, priorita $priority");
        return $this->redirectSeeOther($request,'www/last/'); // 303 See Other
    }

    public function addAbove($request, $paperId, $contentId) {
        $contents = $this->paperContentRepo->findByReference($paperId);
        $content = $this->paperContentRepo->get($contentId);
        $priority = $content->getPriority();
        foreach ($contents as $contentItem) {
            /** @var PaperContentInterface $contentItem */
            $itemPriority = $contentItem->getPriority();
            if ($itemPriority>$priority) {  // obsahy s vyšší prioritou - zvětším jim prioritu o 1 - vznikne díra pro nový content
                $contentItem->setPriority($itemPriority+1);
            }
        }
        $this->paperContentRepo->add($this->createNewContent($paperId, $priority+1));
        $this->addFlashMessage("addBelow - Nový obsah, priorita $priority");
        return $this->redirectSeeOther($request,'www/last/'); // 303 See Other
    }

    public function addBelow($request, $paperId, $contentId) {
        $contents = $this->paperContentRepo->findByReference($paperId);
        $content = $this->paperContentRepo->get($contentId);
        $priority = $content->getPriority();
        foreach ($contents as $contentItem) {
            /** @var PaperContentInterface $contentItem */
            $itemPriority = $contentItem->getPriority();
            if ($itemPriority >= $priority) {  // obsahy s vyšší nebo rovnou prioritou - zvětším jim prioritu o 1 - vznikne díra pro nový content
                $contentItem->setPriority($itemPriority+1);
            }
        }
        $this->paperContentRepo->add($this->createNewContent($paperId, $priority));
        $this->addFlashMessage("addBelow - Nový obsah, priorita $priority");
        return $this->redirectSeeOther($request,'www/last/'); // 303 See Other
    }

    private function createNewContent($paperId, $priority) {
        $newContent = new PaperContent();
        $newContent->setContent("Nový obsah");
        $newContent->setPaperIdFk($paperId);
        $newContent->setPriority($priority);
        return $newContent;
    }

    public function trash($request, $paperId, $contentId) {
        $contents = $this->paperContentRepo->findByReference($paperId);
        $content = $this->paperContentRepo->get($contentId);
        $priority = $content->getPriority();
        $content->setPriority(0);   // "koš" - s prioritou 0 může být více contentů
        $content->setActive(0);
        foreach ($contents as $contentItem) {
            /** @var PaperContentInterface $contentItem */
            $itemPriority = $contentItem->getPriority();
            if ($itemPriority>$priority) {  // obsahy s vyšší prioritou - zmenším jim prioritu o 1 - zavřu díru po odloženém do "koše"
                $contentItem->setPriority($itemPriority-1);
            }
        }
        $this->addFlashMessage("trash - content zahozen do koše.");
        return $this->redirectSeeOther($request,'www/last/'); // 303 See Other

    }

    public function restore($request, $paperId, $contentId) {
        $contents = $this->paperContentRepo->findByReference($paperId);
        $content = $this->paperContentRepo->get($contentId);
        $priority = $content->getPriority();
        foreach ($contents as $contentItem) {
            /** @var PaperContentInterface $contentItem */
            $itemPriority = $contentItem->getPriority();
            if ($itemPriority != 0) {  // mimo obsahů v "koši"
                $contentItem->setPriority($itemPriority+1); // uvolním pozici s prioritou 1
            }
        }
        $content->setPriority(1);   // z "koše" - obnoveno vždy s prioritou 1, zůstává neaktivní
        $this->addFlashMessage("restore - obnoven content z koše.");
        return $this->redirectSeeOther($request,'www/last/'); // 303 See Other
    }

    public function delete($request, $paperId, $contentId) {
        $contents = $this->paperContentRepo->findByReference($paperId);
        $content = $this->paperContentRepo->get($contentId);
        $priority = $content->getPriority();
        $this->paperContentRepo->remove($content);
        foreach ($contents as $contentItem) {
            /** @var PaperContentInterface $contentItem */
            $itemPriority = $contentItem->getPriority();
            if ($itemPriority>$priority) {  // obsahy s vyšší prioritou - zmenším jim prioritu o 1 - zavřu díru po smazaném
                $contentItem->setPriority($itemPriority-1);
            }
        }
        $this->addFlashMessage("delete - smazán content.");
        return $this->redirectSeeOther($request,'www/last/'); // 303 See Other
    }

//    $paper = $this->paperRepo->get($menuItemId) ?? $this->createPaper($menuItemId);

//    private function createPaper($menuItemId) {
//        $paper = (new Paper())->setMenuItemIdFk($menuItemId)->setLangCode($this->statusPresentation->getLanguage()->getLangCode());
//        $this->paperRepo->add($paper);
//        return $paper;
//    }
}
