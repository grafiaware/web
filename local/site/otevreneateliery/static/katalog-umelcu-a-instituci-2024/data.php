<?php

/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */

use Pes\Container\Container;


use Status\Model\Repository\StatusPresentationRepo;

use Red\Model\Dao\Hierarchy\HierarchyAggregateReadonlyDao;
use Red\Model\Repository\MenuItemAggregatePaperRepo;

use Red\Model\Entity\PaperAggregatePaperSectionInterface;

use LogicException, UnexpectedValueException;
use TypeError;


// context
use Model\Context\ContextProviderInterface;

class Katalog {
    
    private $container;

    private $langCode;
    private $katalogUid;
    
    private $lastKatalogUid;

    private $hierarchyDao;

    public function __construct($container) {
        $this->container = $container;
        $statusPresentationRepo = $container->get(StatusPresentationRepo::class);
        /** @var StatusPresentationRepo $statusPresentationRepo */
        $statusPresentation = $statusPresentationRepo->get();
        $this->langCode = $statusPresentation->getLanguage()->getLangCode();
        $this->katalogUid = $statusPresentation->getMenuItem()->getUidFk();
    }
    
    public function getLastKatalogUid() {
        if (!isset($this->lastKatalogUid)) {  // prázdné pole
            throw new LogicException("Last katalog uid je generováno při generování katalogu. Je třeba nejprve volat metodu getKatalog().");            
        }
        return $this->lastKatalogUid;
    }
    
    public function getKatalog() {
        /** @var HierarchyAggregateReadonlyDao $this->hierarchyDao */
        $this->hierarchyDao = $this->container->get(HierarchyAggregateReadonlyDao::class);
        ####
        # zde nastaveno čtení bez ohledu na kontext - čtou se i nepublikované položky ve všech repository (používají stejný context objekt
        #
        # - musí se nastavit zde, po zavolání $menuItemAggRepo->get() pro všechny $subTreeNodes se už v dalších metodách entity nečtou!
        #
        /** @var ContextProviderInterface $contextProvider */
        $contextProvider = $this->container->get(ContextProviderInterface::class);
        
        // kontext pro čtení pomocí HierarchyAggregateReadonlyDao - i v editovatelném modu načte jen aktivní node
        $contextProvider->forceShowOnlyPublished(true);
        
        $node = $this->hierarchyDao->get(['lang_code_fk'=>$this->langCode, 'uid_fk'=>$this->katalogUid]);
        if (!isset($node)) {
            throw new LogicException("V databázi '{$this->hierarchyDao->getSchemaName()}' není PUBLIKOVANÁ (active) položka menu v jazyce '$this->langCode' s názvem '$this->title'");
        }     
        if (!$node['api_module_fk']=='red') {
            throw new LogicException("Položka {$this->title} nemá hodnotu 'api_module_fk'=='red', není určena pro modul red.");            
        }      
        $subTreeNodes = $this->hierarchyDao->getSubTree($this->langCode, $this->katalogUid);
        array_shift($subTreeNodes);
        if (!$subTreeNodes) {  // prázdné pole
            throw new LogicException("Položka menu s katalogem nemá publikované (aktivní) potomky.");            
        }
        $menuItemAggRepo = $this->container->get(MenuItemAggregatePaperRepo::class);                
        
        // kontext pro čtení pomocí MenuItemAggregatePaperRepo - vždy načte i neaktivní menu item aggregate
        $contextProvider->forceShowOnlyPublished(false);
        
        foreach ($subTreeNodes as $node) {
            if (!$node['api_generator_fk']=='paper') {
                throw new LogicException("Položka {$this->title} nemá hodnotu 'api_generator_fk'=='paper',  není typu paper.");            
            }        
            if (!$node['api_module_fk']=='red') {
                throw new LogicException("Položka {$this->title} nemá hodnotu 'api_module_fk'=='red',  není určena pro modul red.");            
            }
            try {
                $menuItemAgg = $menuItemAggRepo->get($this->langCode, $node['uid']);
            } catch (TypeError $exc) {
                throw new UnexpectedValueException("Položka menu item je typu paper, ale nepodařilo se načíst odpovídající paper z databáze.");
            }

            $paper = $menuItemAgg->getPaper();
            // flash (notifikace)
            if (!$paper instanceof PaperAggregatePaperSectionInterface) {
                throw new LogicException("Paper '{$paper->getHeadline()}' není publikovaný (active) paper.");
            }
            $sections = $paper->getPaperSectionsArray();
            if (!$sections) {  // prázdné pole
                throw new LogicException("Paper '{$paper->getHeadline()}' nemá publikované (aktivní) sekce.");            
            }
        }        
        
        $list = [];
        foreach ($subTreeNodes as $node) {
            $menuItemAgg = $menuItemAggRepo->get($this->langCode, $node['uid']);            
            $paper = $menuItemAgg->getPaper();
            $sections = $paper->getPaperSectionsArray();
            foreach ($sections as $section) {
                if ($section->getPriority()>0) {  // mimo sekcí v koši
                    $content = $section->getContent();
                    $anchorPattern = "/id=\"([^']*?)\"/";
                    $anchorMatches = [];
                    preg_match_all($anchorPattern, preg_replace('/\s+/', '', $content), $anchorMatches);
                    $textPattern = "$<\/a>([^<]+)<\/$";
                    $textMatches = [];
                    preg_match_all($textPattern, $content, $textMatches);
                    if ($anchorMatches[1] && $textMatches[1]) {
                        foreach ($anchorMatches[1] as $key => $anchorMatch) {
                            $list[] = ['uid'=>$menuItemAgg->getUidFk(), 'firstLetter'=> strtoupper($anchorMatch[0]), 'anchor'=>$anchorMatch, 'nazev'=>$textMatches[1][$key], 'nazevCs'=>html_entity_decode($textMatches[1][$key], ENT_HTML5), 'active'=>$section->getActive()];                        
                        }
                    } else {
                        $log[] = substr($content, 0, 200);
                    }
                }
            }
        }
        return $list;
    }
}