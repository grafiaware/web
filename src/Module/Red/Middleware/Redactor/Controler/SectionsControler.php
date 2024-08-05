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

use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusFlashRepo;
use Status\Model\Repository\StatusPresentationRepo;

use Status\Model\Enum\FlashSeverityEnum;

use Red\Model\Repository\PaperSectionRepo;
use Red\Model\Entity\PaperSectionInterface;
use Red\Model\Entity\PaperSection;

use Pes\Text\Message;

use DateTime;

/**
 * Description of PostControler
 *
 * @author pes2704
 */
class SectionsControler extends FrontControlerAbstract {

    const SECTION_CONTENT = 'section-content';
    
    const POST_COMMAND_CUT = 'post_command_section_cut';
    const POST_COMMAND_COPY = 'post_command_section_copy';
    
    private $paperSectionRepo;

    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusFlashRepo $statusFlashRepo,
            StatusPresentationRepo $statusPresentationRepo,
            PaperSectionRepo $paperSectionRepo) {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo);
        $this->paperSectionRepo = $paperSectionRepo;
    }

    /**
     *
     * @param ServerRequestInterface $request
     * @param string $sectionId
     * @return type
     */
    public function update(ServerRequestInterface $request, $sectionId): ResponseInterface {
        /** @var PaperSectionInterface $section */
        $section = $this->paperSectionRepo->get($sectionId);
        if (!isset($section)) {
            user_error("Neexistuje sekce se zadaným id.$sectionId");
        } else {
            $namePrefix = implode("_", [self::SECTION_CONTENT, $sectionId]);
            $sectionPost = $this->paramValue($request, $namePrefix);
            $section->setContent($sectionPost);
            $section->setEditor($this->getLoginUserName());
            $this->addFlashMessage('Section updated', FlashSeverityEnum::SUCCESS);
        }
        return $this->redirectSeeLastGet($request); // 303 See Other
    }

    public function toggle(ServerRequestInterface $request, $sectionId) {
        $section = $this->paperSectionRepo->get($sectionId);
        $active = $section->getActive() ? 0 : 1;  //active je integer
        $section->setActive($active);
        $this->addFlashMessage("Section toggle(".($active?'true':'false').")", FlashSeverityEnum::SUCCESS);
        return $this->redirectSeeLastGet($request); // 303 See Other
    }

    public function actual(ServerRequestInterface $request, $sectionId) {
        $content = $this->paperSectionRepo->get($sectionId);
        $button = (new RequestParams())->getParam($request, 'button');
        switch ($button) {
            case 'calendar':
                $showTime = $this->timeFromParam($request, "show_$sectionId");
                $hideTime = $this->timeFromParam($request, "hide_$sectionId");

                $error = false;
                if (isset($showTime) AND isset($hideTime) AND $showTime > $hideTime) {
                    $this->addFlashMessage("Section: Chyba! Datum počátku zobrazování musí být menší nebo stejné jako datum konce.", FlashSeverityEnum::WARNING);
                    $error = true;
                }
                if (!$error) {
                    $content->setShowTime($showTime);
                    $content->setHideTime($hideTime);
                    $s = isset($showTime) ? 'from '.$showTime->format('d.m.Y') : '';
                    $h = isset($hideTime) ? 'to '.$hideTime->format('d.m.Y') : '';
                    $this->addFlashMessage("Section: show $s $h", FlashSeverityEnum::SUCCESS);
                }

                break;
            case 'permanent':
                $content->setShowTime(null);
                $content->setHideTime(null);
                $this->addFlashMessage("Section: zobrazeno trvale", FlashSeverityEnum::SUCCESS);
                break;
            default:
                $this->addFlashMessage("actualContent: Error - unknown button name.", FlashSeverityEnum::WARNING);
                break;
        }
        return $this->redirectSeeLastGet($request); // 303 See Other
    }

    public function event(ServerRequestInterface $request, $sectionId) {
        $content = $this->paperSectionRepo->get($sectionId);
        $button = (new RequestParams())->getParam($request, 'button');
        switch ($button) {
            case 'calendar':
                $eventStartTime = $this->timeFromParam($request, "start_$sectionId");
                $eventEndTime = $this->timeFromParam($request, "end_$sectionId");

                $error = false;
                if (isset($eventStartTime) AND isset($eventEndTime) AND $eventStartTime > $eventEndTime) {
                    $this->addFlashMessage("Section: Chyba! Datum počátku akce musí být menší nebo stejné jako datum konce.", FlashSeverityEnum::WARNING);
                    $error = true;
                }
                if (!$error) {
                    $content->setEventStartTime($eventStartTime);
                    $content->setEventEndTime($eventEndTime);
                    $s = isset($eventStartTime) ? 'from '.$eventStartTime->format('d.m.Y') : '';
                    $h = isset($eventEndTime) ? 'to '.$eventEndTime->format('d.m.Y') : '';
                    $this->addFlashMessage("Section: event $s $h", FlashSeverityEnum::SUCCESS);
                }

                break;
            case 'permanent':
                $content->setEventStartTime(null);
                $content->setEventEndTime(null);
                $this->addFlashMessage("Section: koná se trvale", FlashSeverityEnum::SUCCESS);
                break;
            default:
                $this->addFlashMessage("actualContent: Error - unknown button name.", FlashSeverityEnum::WARNING);
                break;
        }
        return $this->redirectSeeLastGet($request); // 303 See Other
    }

    /**
     * Najde parametr a vytvoří DateTime. Pokud to selže, vrací null (DateTime::createFromFormat() vrací při neúspěchu false)
     * @param type $name
     * @return DateTime|null
     */
    private function timeFromParam(ServerRequestInterface $request, $name): ?DateTime {
        $time = preg_replace('/\s+/', '', (new RequestParams())->getParam($request, $name));
        $dateTime = DateTime::createFromFormat('d.m.Y', $time);
        return $dateTime ? $dateTime : null;

    }
    public function up(ServerRequestInterface $request, $sectionId) {
        /** @var PaperSectionInterface $section */
        $section = $this->paperSectionRepo->get($sectionId);
        $sections = $this->paperSectionRepo->findByPaperIdFk($section->getPaperIdFk());
        $selectedSectionPriority = $section->getPriority();
        $shifted = false;
        foreach ($sections as $sectionsItem) {
            /** @var PaperSectionInterface $sectionsItem */
            if ($sectionsItem->getPriority() == $selectedSectionPriority+1) {  // obsahy s vyšší nebo stejnou prioritou - zvětším jim prioriru o 1 - vznikne díra pro $selectedContentPriority
                $sectionsItem->setPriority($selectedSectionPriority);
                $section->setPriority($selectedSectionPriority+1);
                $shifted = true;
            }
        }
        if ($shifted) {
                $this->addFlashMessage("Section up - priorita změněna $selectedSectionPriority -> ".($selectedSectionPriority+1), FlashSeverityEnum::SUCCESS);
        }
        return $this->redirectSeeLastGet($request); // 303 See Other
    }

    public function down(ServerRequestInterface $request, $sectionId) {
        /** @var PaperSectionInterface $section */
        $section = $this->paperSectionRepo->get($sectionId);
        $sections = $this->paperSectionRepo->findByPaperIdFk($section->getPaperIdFk());
        $selectedSectionPriority = $section->getPriority();
        $shifted = false;
        foreach ($sections as $sectionsItem) {
            /** @var PaperSectionInterface $sectionsItem */
            if ($sectionsItem->getPriority() == $selectedSectionPriority-1) {  // obsahy s vyšší nebo stejnou prioritou - zvětším jim prioriru o 1 - vznikne díra pro $selectedContentPriority
                $sectionsItem->setPriority($selectedSectionPriority);
                $section->setPriority($selectedSectionPriority-1);
                $shifted = true;
            }
        }
        if ($shifted) {
            $this->addFlashMessage("Section down - priorita změněna $selectedSectionPriority -> ".($selectedSectionPriority-1), FlashSeverityEnum::SUCCESS);
        }
        return $this->redirectSeeLastGet($request); // 303 See Other
    }
    
    public function cut(ServerRequestInterface $request, $sectionId) {
        $statusFlash = $this->statusFlashRepo->get();
        $statusFlash->setPostCommand([self::POST_COMMAND_CUT=>$sectionId]);  // command s životností do dalšího POST requestu
        $langCode = $this->statusPresentationRepo->get()->getLanguage()->getLangCode();
        $statusFlash->setMessage("Section cut - item: $langCode/$sectionId selected for cut&paste operation", FlashSeverityEnum::INFO);        
        return $this->redirectSeeLastGet($request); // 303 See Other
    }
    
    public function copy(ServerRequestInterface $request, $sectionId) {
        $statusFlash = $this->statusFlashRepo->get();
        $statusFlash->setPostCommand([self::POST_COMMAND_COPY=>$sectionId]);  // command s životností do dalšího POST requestu
        $langCode = $this->statusPresentationRepo->get()->getLanguage()->getLangCode();
        $statusFlash->setMessage("Section copy - item: $langCode/$sectionId selected for copy&paste operation", FlashSeverityEnum::INFO);  
        return $this->redirectSeeLastGet($request); // 303 See Other
    }
    
    public function pasteAbove(ServerRequestInterface $request, $sectionId) {
        /** @var PaperSectionInterface $sourceSection */
        $sourceSection = $this->paperSectionRepo->get($sectionId);
        $sourcePaperSections = $this->paperSectionRepo->findByPaperIdFk($sourceSection->getPaperIdFk());
        $statusFlash = $this->statusFlashRepo->get();        
        $postCommand = $statusFlash->getPostCommand();
        if (is_array($postCommand) ) {  // command existuje
            $command = array_key_first($postCommand);
            $sourceSectionId = $postCommand[$command];
            switch ($command) {
                case self::POST_COMMAND_CUT:
                    /** @var PaperSectionInterface $sourceSection */
                    $sourceSection = $this->paperSectionRepo->get($sourceSectionId);
                    $sourcePaperSections = $this->paperSectionRepo->findByPaperIdFk($sourceSection->getPaperIdFk());
                    // přesun sekce a přerovnání sekcí
                    $success = true;
                    break;
                case self::POST_COMMAND_COPY:

                    $success = true;
                    break;
                default:
                    $this->addFlashMessage("Paste - unknown post command.", FlashSeverityEnum::WARNING);
                    break;
            }
        }else {
            $this->addFlashMessage("No post command.", FlashSeverityEnum::WARNING);
        }
  
        
        
        
        
        
        $this->addFlashMessage("Section pasteAbove $sectionId", FlashSeverityEnum::SUCCESS);
        return $this->redirectSeeLastGet($request); // 303 See Other
    }
            
    public function pasteBelow(ServerRequestInterface $request, $sectionId) {
        $this->addFlashMessage("Section pasteBelow $sectionId", FlashSeverityEnum::SUCCESS);
        return $this->redirectSeeLastGet($request); // 303 See Other
    }
    
    public function cutEscape(ServerRequestInterface $request, $sectionId) {
        $statusFlash = $this->statusFlashRepo->get();
        $statusFlash->setPostCommand(null);  // zrušení výběru položky "cut" nebo "copy"
        $statusFlash->setMessage("cut escape - operation cut&paste aborted", FlashSeverityEnum::WARNING);
        return $this->redirectSeeLastGet($request); // 303 See Other
    }    
    
    /**
     * Metoda přidí novou, první sekci. POZOR! Jako parametr má id paper.
     * @param ServerRequestInterface $request
     * @param type $paperId
     * @return type
     */
    public function add(ServerRequestInterface $request, $paperId) {
        $priority = 1;
        // pro případ volání add i v situaci, kdy již existuje obsah
        $sections = $this->paperSectionRepo->findByPaperIdFk($paperId);
        foreach ($sections as $sectionsItem) {
            /** @var PaperSectionInterface $sectionsItem */
            $sectionsItem->setPriority($sectionsItem->getPriority()+1);
        }
        $this->paperSectionRepo->add($this->createNewContent($paperId, $priority));
        $this->addFlashMessage("Section add - Nová sekce, priorita $priority", FlashSeverityEnum::SUCCESS);
        return $this->redirectSeeLastGet($request); // 303 See Other
    }

    public function addAbove(ServerRequestInterface $request, $contentId) {
        /** @var PaperSectionInterface $section */
        $section = $this->paperSectionRepo->get($contentId);
        $paperId = $section->getPaperIdFk();
        $sections = $this->paperSectionRepo->findByPaperIdFk($paperId);
        $priority = $section->getPriority();
        foreach ($sections as $sectionsItem) {
            /** @var PaperSectionInterface $sectionsItem */
            $itemPriority = $sectionsItem->getPriority();
            if ($itemPriority>$priority) {  // obsahy s vyšší prioritou - zvětším jim prioritu o 1 - vznikne díra pro nový content
                $sectionsItem->setPriority($itemPriority+1);
            }
        }
        $this->paperSectionRepo->add($this->createNewContent($paperId, $priority+1));
        $this->addFlashMessage("Section addAbove - priorita $priority+1", FlashSeverityEnum::SUCCESS);
        return $this->redirectSeeLastGet($request); // 303 See Other
    }

    public function addBelow(ServerRequestInterface $request, $sectionId) {
        /** @var PaperSectionInterface $section */
        $section = $this->paperSectionRepo->get($sectionId);
        $paperId = $section->getPaperIdFk();
        $sections = $this->paperSectionRepo->findByPaperIdFk($paperId);
        $priority = $section->getPriority();
        foreach ($sections as $sectionsItem) {
            /** @var PaperSectionInterface $sectionsItem */
            $itemPriority = $sectionsItem->getPriority();
            if ($itemPriority >= $priority) {  // obsahy s vyšší nebo rovnou prioritou - zvětším jim prioritu o 1 - vznikne díra pro nový content
                $sectionsItem->setPriority($itemPriority+1);
            }
        }
        $this->paperSectionRepo->add($this->createNewContent($paperId, $priority));
        $this->addFlashMessage("Section addBelow - priorita $priority-1", FlashSeverityEnum::SUCCESS);
        return $this->redirectSeeLastGet($request); // 303 See Other
    }

    private function createNewContent($paperId, $priority) {
        $newContent = new PaperSection();
        $newContent->setPaperIdFk($paperId);
        $newContent->setPriority($priority);
        $newContent->setActive(0);   //active je integer
        return $newContent;
    }

    public function trash(ServerRequestInterface $request, $sectionId) {
        /** @var PaperSectionInterface $section */
        $section = $this->paperSectionRepo->get($sectionId);
        $paperId = $section->getPaperIdFk();
        $sections = $this->paperSectionRepo->findByPaperIdFk($paperId);
        $priority = $section->getPriority();
        $section->setPriority(0);   // "koš" - s prioritou 0 může být více contentů
        $section->setActive(0);
        foreach ($sections as $sectionsItem) {
            /** @var PaperSectionInterface $sectionsItem */
            $itemPriority = $sectionsItem->getPriority();
            if ($itemPriority>$priority) {  // obsahy s vyšší prioritou - zmenším jim prioritu o 1 - zavřu díru po odloženém do "koše"
                $sectionsItem->setPriority($itemPriority-1);
            }
        }
        $this->addFlashMessage("Section trash - sekce odložena do koše pro Paper.", FlashSeverityEnum::SUCCESS);
        return $this->redirectSeeLastGet($request); // 303 See Other

    }

    public function restore(ServerRequestInterface $request, $sectionId) {
        /** @var PaperSectionInterface $section */
        $section = $this->paperSectionRepo->get($sectionId);
        $paperId = $section->getPaperIdFk();
        $sections = $this->paperSectionRepo->findByPaperIdFk($paperId);
        foreach ($sections as $contentItem) {
            /** @var PaperSectionInterface $contentItem */
            $itemPriority = $contentItem->getPriority();
            if ($itemPriority != 0) {  // mimo obsahů v "koši"
                $contentItem->setPriority($itemPriority+1); // uvolním pozici s prioritou 1
            }
        }
        $section->setPriority(1);   // z "koše" - obnoveno s prioritou 1, zůstává neaktivní
        $this->addFlashMessage("Section restore - obnovena sekce z koše.", FlashSeverityEnum::SUCCESS);
        return $this->redirectSeeLastGet($request); // 303 See Other
    }

    public function delete(ServerRequestInterface $request, $sectiontId) {
        $section = $this->paperSectionRepo->get($sectiontId);
        $this->paperSectionRepo->remove($section);
        $this->addFlashMessage("Section delete - smazána sekce.", FlashSeverityEnum::SUCCESS);
        return $this->redirectSeeLastGet($request); // 303 See Other
    }
}
