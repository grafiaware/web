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

class Katalog {
    
    private $container;

    private $langCode;
    private $katalogUid;
    
    private $lastKatalogUid;


    public function __construct($container) {
        $this->container = $container;
        $statusPresentationRepo = $container->get(StatusPresentationRepo::class);
        /** @var StatusPresentationRepo $statusPresentationRepo */
        $statusPresentation = $statusPresentationRepo->get();
        $this->langCode = $statusPresentation->getLanguage()->getLangCode();
        $this->katalogUid = $statusPresentation->getMenuItem()->getUidFk();
    }
    
    private function setUp(): void {
        /** @var HierarchyAggregateReadonlyDao $hierarchyDao */
        $hierarchyDao = $this->container->get(HierarchyAggregateReadonlyDao::class);
        $node = $hierarchyDao->get(['lang_code_fk'=>$this->langCode, 'uid_fk'=>$this->katalogUid]);
        if (!isset($node)) {
            throw new LogicException("V databázi '{$hierarchyDao->getSchemaName()}' není ACTIVE položka menu v jazyce '$this->langCode' s názvem '$this->title'");
        }
        if (!$node['api_generator_fk']=='static') {
            throw new LogicException("Položka {$this->title} nemá hodnotu 'api_generator_fk'=='static', není typu static.");            
        }        
        if (!$node['api_module_fk']=='red') {
            throw new LogicException("Položka {$this->title} nemá hodnotu 'api_module_fk'=='red', není určena pro modul red.");            
        }      
        $subTreeNodes = $hierarchyDao->getSubTree($this->langCode, $this->katalogUid);
        array_shift($subTreeNodes);
        if (!$subTreeNodes) {  // prázdné pole
            throw new LogicException("Položka s katalogem nemá publikované (aktivní) potomky.");            
        }
        $menuItemAggRepo = $this->container->get(MenuItemAggregatePaperRepo::class);                

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
    }
    
    public function getLastKatalogUid() {
        if (!isset($this->lastKatalogUid)) {  // prázdné pole
            throw new LogicException("Last katalog uid je generováno při generování katalogu. Je třeba nejprve volat metodu getKatalog().");            
        }
        return $this->lastKatalogUid;
    }
    
    public function getKatalog() {
        $this->setUp();
        
        /** @var HierarchyAggregateReadonlyDao $hierarchyDao */
        $hierarchyDao = $this->container->get(HierarchyAggregateReadonlyDao::class);        
        $subTreeNodes = $hierarchyDao->getSubTree($this->langCode, $this->katalogUid);
        $this->lastKatalogUid = array_shift($subTreeNodes);
        /** @var MenuItemAggregatePaperRepo $menuItemAggRepo */
        $menuItemAggRepo = $this->container->get(MenuItemAggregatePaperRepo::class);                
        $list = [];
        foreach ($subTreeNodes as $node) {
            $menuItemAgg = $menuItemAggRepo->get($this->langCode, $node['uid']);            
            $paper = $menuItemAgg->getPaper();
            $sections = $paper->getPaperSectionsArray();
            foreach ($sections as $section) {
                $content = $section->getContent();
//                $pattern = "/<aid=\"*\"/";
                $anchorPattern = "/id=\"([^']*?)\"/";
                $anchorMatches = [];
                preg_match($anchorPattern, preg_replace('/\s+/', '', $content), $anchorMatches);
                $textPattern = "$<\/a>([^<]+)<\/$";
                $textMatches = [];
                preg_match($textPattern, $content, $textMatches);
                if (isset($anchorMatches[1]) && isset($textMatches[1])) {
                    $list[] = ['uid'=>$menuItemAgg->getUidFk(), 'firstLetter'=> strtoupper($anchorMatches[1][0]), 'anchor'=>$anchorMatches[1], 'nazev'=>$textMatches[1], 'nazevCs'=>html_entity_decode($textMatches[1], ENT_HTML5), 'active'=>$section->getActive()];
                } else {
                    $log[] = substr($content, 0, 200);
                }
            }
        }
        return $list;
    }    
}