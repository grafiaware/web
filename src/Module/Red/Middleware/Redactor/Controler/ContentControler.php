<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Middleware\Redactor\Controler;

use FrontControler\FrontControlerAbstract;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

use Pes\Application\AppFactory;
use Pes\Http\Request\RequestParams;
use Pes\Http\Response;
use Pes\Http\Response\RedirectResponse;

use Status\Model\Repository\{
    StatusSecurityRepo, StatusFlashRepo, StatusPresentationRepo
};
use Red\Model\Repository\PaperContentRepo;
use Red\Model\Entity\PaperContentInterface;
use Red\Model\Entity\PaperContent;

use Pes\Text\Message;

/**
 * Description of PostControler
 *
 * @author pes2704
 */
class ContentControler extends FrontControlerAbstract {

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
            $this->addFlashMessage('Content updated');

        }
        return $this->redirectSeeLastGet($request); // 303 See Other
    }

    public function toggleContent(ServerRequestInterface $request, $paperId, $contentId) {
        $content = $this->paperContentRepo->get($contentId);
        if ($this->isContent($content)) {
            $active = $content->getActive() ? 0 : 1;  //active je integer
            $content->setActive($active);
            $this->addFlashMessage("content toggle(".($active?'true':'false').")");
        }
        return $this->redirectSeeLastGet($request); // 303 See Other
    }

    public function actualContent(ServerRequestInterface $request, $paperId, $contentId) {
        $content = $this->paperContentRepo->get($contentId);
        if ($this->isContent($content)) {
            $button = (new RequestParams())->getParam($request, 'button');
            switch ($button) {
                case 'calendar':
                    $showTime = $this->timeFromParam($request, "show_$contentId");
                    $hideTime = $this->timeFromParam($request, "hide_$contentId");

                    $error = false;
                    if (isset($showTime) AND isset($hideTime) AND $showTime > $hideTime) {
                        $this->addFlashMessage("content: Chyba! Datum počátku zobrazování musí být menší nebo stejné jako datum konce.");
                        $error = true;
                    }
                    if (!$error) {
                        $content->setShowTime($showTime);
                        $content->setHideTime($hideTime);
                        $s = isset($showTime) ? 'from '.$showTime->format('d.m.Y') : '';
                        $h = isset($hideTime) ? 'to '.$hideTime->format('d.m.Y') : '';
                        $this->addFlashMessage("content: show $s $h");
                    }

                    break;
                case 'permanent':
                    $content->setShowTime(null);
                    $content->setHideTime(null);
                    $this->addFlashMessage("content: zobrazeno trvale");
                    break;
                default:
                    $this->addFlashMessage("actualContent: Error - unknown button name.");
                    break;
            }
        }
        return $this->redirectSeeLastGet($request); // 303 See Other
    }

    public function event(ServerRequestInterface $request, $paperId, $contentId) {
        $content = $this->paperContentRepo->get($contentId);
        if ($this->isContent($content)) {
            $button = (new RequestParams())->getParam($request, 'button');
            switch ($button) {
                case 'calendar':
                    $eventStartTime = $this->timeFromParam($request, "start_$contentId");
                    $eventEndTime = $this->timeFromParam($request, "end_$contentId");

                    $error = false;
                    if (isset($eventStartTime) AND isset($eventEndTime) AND $eventStartTime > $eventEndTime) {
                        $this->addFlashMessage("content: Chyba! Datum počátku akce musí být menší nebo stejné jako datum konce.");
                        $error = true;
                    }
                    if (!$error) {
                        $content->setEventStartTime($eventStartTime);
                        $content->setEventEndTime($eventEndTime);
                        $s = isset($eventStartTime) ? 'from '.$eventStartTime->format('d.m.Y') : '';
                        $h = isset($eventEndTime) ? 'to '.$eventEndTime->format('d.m.Y') : '';
                        $this->addFlashMessage("content: event $s $h");
                    }

                    break;
                case 'permanent':
                    $content->setShowTime(null);
                    $content->setHideTime(null);
                    $this->addFlashMessage("content: zobrazeno trvale");
                    break;
                default:
                    $this->addFlashMessage("actualContent: Error - unknown button name.");
                    break;
            }
        }
        return $this->redirectSeeLastGet($request); // 303 See Other
    }

    /**
     * Najde parametr a vytvoří DateTime. Pokud to selže, vrací null (\DateTime::createFromFormat() vrací při neúspěchu false)
     * @param type $name
     * @return \DateTime|null
     */
    private function timeFromParam(ServerRequestInterface $request, $name): ?\DateTime {
        $time = preg_replace('/\s+/', '', (new RequestParams())->getParam($request, $name));
        $dateTime = \DateTime::createFromFormat('d.m.Y', $time);
        return $dateTime ? $dateTime : null;

    }
    public function up(ServerRequestInterface $request, $paperId, $contentId) {
        $contents = $this->paperContentRepo->findByReference($paperId);
        $content = $this->paperContentRepo->get($contentId);
        $selectedContentPriority = $content->getPriority();
        foreach ($contents as $contentItem) {
            /** @var PaperContentInterface $contentItem */
            if ($contentItem->getPriority() == $selectedContentPriority+1) {  // obsahy s vyšší nebo stejnou prioritou - zvětším jim prioriru o 1 - vznikne díra pro $selectedContentPriority
                $contentItem->setPriority($selectedContentPriority);
                $content->setPriority($selectedContentPriority+1);
                $this->addFlashMessage("content up - priorita změněna $selectedContentPriority -> ".($selectedContentPriority+1));
            }
        }
        return $this->redirectSeeLastGet($request); // 303 See Other
    }

    public function down(ServerRequestInterface $request, $paperId, $contentId) {
        $contents = $this->paperContentRepo->findByReference($paperId);
        $content = $this->paperContentRepo->get($contentId);
        $selectedContentPriority = $content->getPriority();
        foreach ($contents as $contentItem) {
            /** @var PaperContentInterface $contentItem */
            if ($contentItem->getPriority() == $selectedContentPriority-1) {  // obsahy s vyšší nebo stejnou prioritou - zvětším jim prioriru o 1 - vznikne díra pro $selectedContentPriority
                $contentItem->setPriority($selectedContentPriority);
                $content->setPriority($selectedContentPriority-1);
                $this->addFlashMessage("content down - priorita změněna $selectedContentPriority -> ".($selectedContentPriority-1));
            }
        }
        return $this->redirectSeeLastGet($request); // 303 See Other
    }

    public function add(ServerRequestInterface $request, $paperId) {
        $priority = 1;
        // pro případ volání add i v situaci, kdy již existuje obsah
        $contents = $this->paperContentRepo->findByReference($paperId);
        foreach ($contents as $contentItem) {
            /** @var PaperContentInterface $contentItem */
            if ($contentItem->getPriority()>$priority) {
                $contentItem->setPriority($contentItem->getPriority()+1);
            }
        }
        $this->paperContentRepo->add($this->createNewContent($paperId, $priority));
        $this->addFlashMessage("add - Nový obsah, priorita $priority");
        return $this->redirectSeeLastGet($request); // 303 See Other
    }

    public function addAbove(ServerRequestInterface $request, $paperId, $contentId) {
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
        return $this->redirectSeeLastGet($request); // 303 See Other
    }

    public function addBelow(ServerRequestInterface $request, $paperId, $contentId) {
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
        return $this->redirectSeeLastGet($request); // 303 See Other
    }

    private function createNewContent($paperId, $priority) {
        $newContent = new PaperContent();
        $newContent->setPaperIdFk($paperId);
        $newContent->setPriority($priority);
        $newContent->setActive(0);
        return $newContent;
    }

    public function trash(ServerRequestInterface $request, $paperId, $contentId) {
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
        return $this->redirectSeeLastGet($request); // 303 See Other

    }

    public function restore(ServerRequestInterface $request, $paperId, $contentId) {
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
        return $this->redirectSeeLastGet($request); // 303 See Other
    }

    public function delete(ServerRequestInterface $request, $paperId, $contentId) {
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
        return $this->redirectSeeLastGet($request); // 303 See Other
    }

//    $paper = $this->paperRepo->get($menuItemId) ?? $this->createPaper($menuItemId);

//    private function createPaper($menuItemId) {
//        $paper = (new Paper())->setMenuItemIdFk($menuItemId)->setLangCode($this->statusPresentation->getLanguage()->getLangCode());
//        $this->paperRepo->add($paper);
//        return $paper;
//    }
}
