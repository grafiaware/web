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
    
    const MANY = 30000;

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
     * Updatuje obsah sekce.
     * 
     * @param ServerRequestInterface $request
     * @param string $sectionId
     * @return type
     */
    public function update(ServerRequestInterface $request, $sectionId): ResponseInterface {
        /** @var PaperSectionInterface $section */
        $section = $this->paperSectionRepo->get($sectionId);
        if (!isset($section)) {
            user_error("Neexistuje sekce se zadaným id: $sectionId");
        } else {
            $namePrefix = implode("_", [self::SECTION_CONTENT, $sectionId]);
            $sectionPost = $this->paramValue($request, $namePrefix);
            $section->setContent($sectionPost);
            $section->setEditor($this->getLoginUserName());
            $this->addFlashMessage('Section updated', FlashSeverityEnum::SUCCESS);
        }
        return $this->createPutNoContentResponse(); // 204 No Content
    }

    /**
     * Přepne zveřejnění (active) sekce.
     * 
     * @param ServerRequestInterface $request
     * @param type $sectionId
     * @return type
     */
    public function toggle(ServerRequestInterface $request, $sectionId) {
        $section = $this->paperSectionRepo->get($sectionId);
        $active = $section->getActive() ? 0 : 1;  //active je integer
        $section->setActive($active);
        $this->addFlashMessage("Section toggle(".($active?'true':'false').")", FlashSeverityEnum::SUCCESS);
        return $this->redirectSeeLastGet($request); // 303 See Other
    }

    /**
     * Nastaví datumy odkdy a dokdy je sekce aktuální, tedy považovaná za zveřejněnou. 
     * Sekce musí být zveřejněná (active). Pokud sekce vůbec není zvěřejněna, nemá nastavení actual žádny vliv.
     * 
     * Pokud má sekce nastaveny jeden nebo oba datumy actual, je reálně považována za zveřejněnou pouze v rozmezí datumů od-do (včetně). 
     * Pokud má jen datum do je považována za zveřejněnou kdykoli před datem do (včetnš), pokud má sekce nastaven pouze datum od, 
     * je považována za zveřejněnou od daného data (včetně) dále.
     * 
     * @param ServerRequestInterface $request
     * @param type $sectionId
     * @return type
     */
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
    
    /**
     * Zvětší prioritu sekce o jednotku. Pokud jsou sekce řazeny podle priority (výchozí stav), posune sekci na stránce o pozici nahoru.
     * 
     * @param ServerRequestInterface $request
     * @param type $sectionId
     * @return type
     */
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

    /**
     * Zmenší prioritu sekce o jednotku. Pokud jsou sekce řazeny podle priority (výchozí stav), posune sekci na stránce o pozici dolů.
     * 
     * @param ServerRequestInterface $request
     * @param type $sectionId
     * @return type
     */
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
    
    /**
     * Vybere sekci k přesunutí. Přesunutí reálně proběhne až příkazem paste.
     * 
     * @param ServerRequestInterface $request
     * @param type $sectionId
     * @return type
     */
    public function cut(ServerRequestInterface $request, $sectionId) {
        $statusFlash = $this->statusFlashRepo->get();
        $statusFlash->setPostCommand([self::POST_COMMAND_CUT=>$sectionId]);  // command s životností do dalšího POST requestu
        $langCode = $this->statusPresentationRepo->get()->getLanguageCode();
        $statusFlash->setMessage("Section cut - item: $langCode/$sectionId selected for cut&paste operation", FlashSeverityEnum::INFO);        
        return $this->redirectSeeLastGet($request); // 303 See Other
    }
    
    /**
     * Vybere sekci ke zkopírování. Zkopírování reálně proběhne až příkazem paste.
     * 
     * @param ServerRequestInterface $request
     * @param type $sectionId
     * @return type
     */
    public function copy(ServerRequestInterface $request, $sectionId) {
        $statusFlash = $this->statusFlashRepo->get();
        $statusFlash->setPostCommand([self::POST_COMMAND_COPY=>$sectionId]);  // command s životností do dalšího POST requestu
        $langCode = $this->statusPresentationRepo->get()->getLanguageCode();
        $statusFlash->setMessage("Section copy - item: $langCode/$sectionId selected for copy&paste operation", FlashSeverityEnum::INFO);  
        return $this->redirectSeeLastGet($request); // 303 See Other
    }
    
    /**
     * 
     * @param ServerRequestInterface $request
     * @param type $sectionId
     * @return type
     */
    public function pasteAbove(ServerRequestInterface $request, $sectionId) {
        $this->pasteSection($sectionId, true);
        $this->addFlashMessage("Section pasteAbove $sectionId", FlashSeverityEnum::SUCCESS);
        return $this->redirectSeeLastGet($request); // 303 See Other
    }
            
    public function pasteBelow(ServerRequestInterface $request, $sectionId) {
        $this->pasteSection($sectionId, false);
        $this->addFlashMessage("Section pasteAbove $sectionId", FlashSeverityEnum::SUCCESS);
        return $this->redirectSeeLastGet($request); // 303 See Other
    }
    
    /**
     * Vloží tj.přesune přetím vybranou sekci príkazem cut nad nebo pod cílovou sekci.
     * 
     * @param type $targetSectionId id cílové sekce
     * @param bool $above true - přesune vybranou sekci nad cílovou sekci, false, přesune předtím vybranou sekci pod cílovou sekci
     */
    private function pasteSection($targetSectionId, bool $above) {
        $statusFlash = $this->statusFlashRepo->get();        
        $flashCommand = $statusFlash->getPostCommand();
        if (is_array($flashCommand) ) {  // command existuje
            $command = array_key_first($flashCommand);
            $sourceSectionId = $flashCommand[$command];
            /** @var PaperSectionInterface $sourceSection */
            $sourceSection = $this->paperSectionRepo->get($sourceSectionId);
            /** @var PaperSectionInterface $targetSection */
            $targetSection = $this->paperSectionRepo->get($targetSectionId);
            $sourcePaperId = $sourceSection->getPaperIdFk();
            $targetPaperId = $targetSection->getPaperIdFk();
            if ($sourcePaperId===$targetPaperId) {
                switch ($command) {
                    case self::POST_COMMAND_CUT:
                        if ($above) {
                            $this->moveSectionAboveTarget($sourceSection, $targetSection, $targetPaperId);
                        } else {
                            $this->moveSectionBelowTarget($sourceSection, $targetSection, $targetPaperId);
                        }
                        break;
                    case self::POST_COMMAND_COPY:
                        $this->addFlashMessage("Operaci nelze provést - copy není implementováno", FlashSeverityEnum::WARNING);
                        break;
                    default:
                        $this->addFlashMessage("Unknown post command $command.", FlashSeverityEnum::WARNING);
                        break;
                }
            } else {
                switch ($command) {
                    case self::POST_COMMAND_CUT:
                        if ($above) {
                            $this->flipSectionAboveTarget($sourceSection, $targetSection, $sourcePaperId, $targetPaperId);
                        } else {
                            $this->flipSectionBelowTarget($sourceSection, $targetSection, $sourcePaperId, $targetPaperId);
                        }
                        break;
                    case self::POST_COMMAND_COPY:
                        $this->addFlashMessage("Operaci nelze provést - copy není implementováno.", FlashSeverityEnum::WARNING);
                        break;
                    default:
                        $this->addFlashMessage("Unknown post command $command.", FlashSeverityEnum::WARNING);
                        break;
                }                
            }
        } else {
            $this->addFlashMessage("No post command.", FlashSeverityEnum::WARNING);
        }        
    }
    
    /**
     * Přesune zdrojovou sekci nad cílovou
     * Pozn. předpokládá priority od 1, nefunguje pro přesun z koše
     * 
     * @param PaperSectionInterface $sourceSection
     * @param PaperSectionInterface $targetSection
     * @param type $targetPaperId
     */
    private function moveSectionAboveTarget(PaperSectionInterface $sourceSection, PaperSectionInterface $targetSection, $targetPaperId) {
        $paperSections = $this->paperSectionRepo->findByPaperIdFk($targetPaperId);
        $sourceSectionPriority = $sourceSection->getPriority();
        $targetSectionPriority = $targetSection->getPriority();
        if ($targetSectionPriority > $sourceSectionPriority) { //nahoru            
            $this->shiftPriority($paperSections, $sourceSectionPriority+1, $targetSectionPriority, -1);
            $sourceSection->setPriority($targetSectionPriority);  
        } else { //dolu                    
            $this->shiftPriority($paperSections, $targetSectionPriority+1, $sourceSectionPriority-1, 1);            
            $sourceSection->setPriority($targetSectionPriority+1);  
        }             
        
    }
    
    /**
     * Přesune zdrojovou sekci pod cílovou
     * Pozn. předpokládá priority od 1, nefunguje pro přesun z koše
     * 
     * @param PaperSectionInterface $sourceSection
     * @param PaperSectionInterface $targetSection
     * @param type $targetPaperId
     */
    private function moveSectionBelowTarget(PaperSectionInterface $sourceSection, PaperSectionInterface $targetSection, $targetPaperId) {
        $paperSections = $this->paperSectionRepo->findByPaperIdFk($targetPaperId);
        $sourceSectionPriority = $sourceSection->getPriority();
        $targetSectionPriority = $targetSection->getPriority();
        if ($targetSectionPriority > $sourceSectionPriority) { //nahoru     
            $this->shiftPriority($paperSections, $sourceSectionPriority+1, $targetSectionPriority-1, -1);            
            $sourceSection->setPriority($targetSectionPriority-1);  
        } else { //dolu                    
            $this->shiftPriority($paperSections, $targetSectionPriority, $sourceSectionPriority-1, 1);
            $sourceSection->setPriority($targetSectionPriority);  
        }        
    }
    
    private function flipSectionAboveTarget(PaperSectionInterface $sourceSection, PaperSectionInterface $targetSection, $sourcePaperId, $targetPaperId) {
        $sourcePaperSections = $this->paperSectionRepo->findByPaperIdFk($sourcePaperId);
        $targetPaperSections = $this->paperSectionRepo->findByPaperIdFk($targetPaperId);
        $sourceSectionPriority = $sourceSection->getPriority();
        $targetSectionPriority = $targetSection->getPriority();
        $this->shiftPriority($sourcePaperSections, $sourceSectionPriority+1, self::MANY, -1);
        $this->shiftPriority($targetPaperSections, $targetSectionPriority+1, self::MANY, 1);             
        $sourceSection->setPaperIdFk($targetPaperId);
        $sourceSection->setPriority($targetSectionPriority+1);          
    }

    private function flipSectionBelowTarget(PaperSectionInterface $sourceSection, PaperSectionInterface $targetSection, $sourcePaperId, $targetPaperId) {
        $sourcePaperSections = $this->paperSectionRepo->findByPaperIdFk($sourcePaperId);
        $targetPaperSections = $this->paperSectionRepo->findByPaperIdFk($targetPaperId);
        $sourceSectionPriority = $sourceSection->getPriority();
        $targetSectionPriority = $targetSection->getPriority();
        $this->shiftPriority($sourcePaperSections, $sourceSectionPriority+1, self::MANY, -1);
        $this->shiftPriority($targetPaperSections, $targetSectionPriority, self::MANY, 1);             
        $sourceSection->setPaperIdFk($targetPaperId);
        $sourceSection->setPriority($targetSectionPriority);          
    }
    
    /**
     * Změní priority sekcí s prioritou větší nebo rovnou minimu a mensí nebo rovnou maximu (tj. min a max 
     * udává uzavřený interval). Priority změní o zadanou hodnotu $shiftValue.
     * 
     * @param array $paperSections
     * @param int $minPriority
     * @param int $maxPriority
     * @param int $shiftValue Změna priority
     */
    private function shiftPriority( array &$paperSections, int $minPriority, int $maxPriority, int $shiftValue = 0) {
        /** @var PaperSectionInterface $section */
        // prochází všechny sekce - sekce nejsou seřazeny podle priority
        foreach ($paperSections as $section) {                      
            $currentPriority = $section->getPriority();                                      
            if  ( ($currentPriority >= $minPriority) AND ($currentPriority <= $maxPriority) ) {
                $section->setPriority($currentPriority+$shiftValue);
            }                                                                                                
        }        
    }
    
    public function cutEscape(ServerRequestInterface $request, $sectionId) {
        $statusFlash = $this->statusFlashRepo->get();
        $statusFlash->getPostCommand();  // zrušení výběru položky "cut" nebo "copy"
        $statusFlash->setMessage("cut escape - operation cut&paste aborted", FlashSeverityEnum::WARNING);
        return $this->redirectSeeLastGet($request); // 303 See Other
    }
    
    /**
     * Metoda přidí novou sekci. POZOR! Jako parametr má id paper.
     * 
     * @param ServerRequestInterface $request
     * @param type $paperId
     * @return type
     */
    public function add(ServerRequestInterface $request, $paperId) {
        $max = 0;
        // pro případ volání add i v situaci, kdy již existuje obsah
        $sections = $this->paperSectionRepo->findByPaperIdFk($paperId);
        foreach ($sections as $sectionsItem) {
            /** @var PaperSectionInterface $sectionsItem */
            $sectionPriority = $sectionsItem->getPriority();
            if ($sectionPriority>$max) {
                $max = $sectionPriority;
            }
        }
        $this->paperSectionRepo->add($this->createNewContent($paperId, $max+1));
        $this->addFlashMessage("Section add - Nová sekce, priorita $max+1", FlashSeverityEnum::SUCCESS);
        return $this->redirectSeeLastGet($request); // 303 See Other
    }

    /**
     * Metoda přidá prázdnou novou sekci nad sekci, ve které uživatelel klikl na tlačítko.
     * 
     * @param ServerRequestInterface $request
     * @param type $sectionId
     * @return type
     */
    public function addAbove(ServerRequestInterface $request, $sectionId) {
        /** @var PaperSectionInterface $section */
        $section = $this->paperSectionRepo->get($sectionId);
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

    /**
     * Metoda přidá prázdnou novou sekci pod sekci, ve které uživatel klikl na tlačítko.
     * 
     * @param ServerRequestInterface $request
     * @param type $sectionId
     * @return type
     */
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

    /**
     * Metoda přesune sekci do koše. Sekce v koši se liší pouze tím, že mají prioritu rovnu nula. Mtoda tedy nastaví prioritu = 0.
     * 
     * @param ServerRequestInterface $request
     * @param type $sectionId
     * @return type
     */
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
