<?php
declare(strict_types=1);

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use Pes\Container\Container;

use Test\AppRunner\AppRunner;

use Red\Model\Repository\PaperSectionRepo;

use Test\Integration\Red\Container\TestDbUpgradeContainerConfigurator;
use Test\Integration\Red\Container\TestHierarchyContainerConfigurator;

use Red\Model\Repository\PaperSectionRepoInterface;
use Red\Model\Entity\PaperSectionInterface;

use Model\Context\ContextProviderInterface;


/**
 * Description of PaperContentDaoTest
 *
 * @author pes2704
 */
class NENI_TEST_MoveSectionTest  extends AppRunner {

    private $container;

    /**
     *  @var  PaperSectionRepoInterface
     */
    private $paperSectionRepo;         
    

    public static function setUpBeforeClass(): void {
        self::bootstrapBeforeClass();
    }

    protected function setUp(): void {
        $this->container =
                           
                (new TestHierarchyContainerConfigurator())->configure(
                           (new TestDbUpgradeContainerConfigurator())->configure(new Container())
                        );        
        $n=1;
        /** @var ContextProviderInterface $contextProvider */
        $contextProvider = $this->container->get(ContextProviderInterface::class);
        $contextProvider->forceShowOnlyPublished(false);
        
        $this->paperSectionRepo = $this->container->get(PaperSectionRepo::class);                      
        
    }
    
    
    
    public function testMoveAbove() {      
        $sectionFromId = 460;
        $sectionToId = 526;
        //$paperFromId = 1279;  //$paperToId = 1279;       
                
         /** @var PaperSectionInterface $sectionFrom */
        $sectionFrom = $this->paperSectionRepo->get($sectionFromId);
        $paperFromId = $sectionFrom->getPaperIdFk();
        /** @var PaperSectionInterface $sectionTo */
        $sectionTo = $this->paperSectionRepo->get($sectionToId);        
        $paperToId = $sectionTo->getPaperIdFk();        
          
        $sectionToPriority = $sectionTo->getPriority();
        $sectionFromPriority = $sectionFrom->getPriority();
        
        
        //-----------------------------------
        if ($paperToId == $paperFromId) { // je to jeden paper , priority jsou v jednom paperu
            $sections = $this->paperSectionRepo->findByPaperIdFk($paperFromId); //vsechny sekce v paperu odkud = vsechny sekce v paperu kam
            $shifted = false;   
                          /** @var PaperSectionInterface $sectionItem */                            
                if ($sectionToPriority > $sectionFromPriority) { //nahoru        
                    foreach ($sections as $sectionItem) {
                        $sectionItemPriorityCur = $sectionItem->getPriority();                                      
                        if  ( ($sectionItemPriorityCur <= $sectionToPriority ) AND
                              ($sectionItemPriorityCur > $sectionFromPriority) ) {
                            $sectionItem->setPriority($sectionItemPriorityCur-1);
                            $shifted = true;
                        }                           
                    }     
                    $sectionFrom->setPriority($sectionToPriority);  
                }
                else { //dolu                    
                    foreach ($sections as $sectionItem) {                      
                        $sectionItemPriorityCur = $sectionItem->getPriority();                                      
                       if  ( ($sectionItemPriorityCur > $sectionToPriority) AND
                              ($sectionItemPriorityCur < $sectionFromPriority) ) {
                            $sectionItem->setPriority($sectionItemPriorityCur+1);
                            $shifted = true;
                        }                                                                                                
                    }
                    $sectionFrom->setPriority($sectionToPriority+1);  
                }     
        } 
        
        
        
 //TOTO NENI ODLADENO ---- ASI ZATIM NESMYSLY   
        else {   // jsou to 2 papery , priority nejsou  v jednom paperu            
        }
      
        $this->assertTrue(1);
    
            throw new Exception();
    }
    
    
    
    //--------------------------------------------------------------------------
    public function MoveBelow() {      
        $sectionFromId = 457;
        $sectionToId = 458;
        //$paperFromId = 1279;  //$paperToId = 1279;       
                
         /** @var PaperSectionInterface $sectionFrom */
        $sectionFrom = $this->paperSectionRepo->get($sectionFromId);
        $paperFromId = $sectionFrom->getPaperIdFk();
        /** @var PaperSectionInterface $sectionTo */
        $sectionTo = $this->paperSectionRepo->get($sectionToId);        
        $paperToId = $sectionTo->getPaperIdFk();        
       
        $sectionToPriority = $sectionTo->getPriority();
        $sectionFromPriority = $sectionFrom->getPriority();
        
        
        //-----------------------------------
        if ($paperToId == $paperFromId) { // je to jeden paper , priority jsou v jednom paperu
            $sections = $this->paperSectionRepo->findByPaperIdFk($paperFromId); //vsechny sekce v paperu odkud = vsechny sekce v paperu kam
            $shifted = false;   
                          /** @var PaperSectionInterface $sectionItem */                            
                if ($sectionToPriority > $sectionFromPriority) { //nahoru     
                    foreach ($sections as $sectionItem) {
                        $sectionItemPriorityCur = $sectionItem->getPriority();                                      
                        if  ( ($sectionItemPriorityCur < $sectionToPriority ) AND
                              ($sectionItemPriorityCur > $sectionFromPriority) ) {
                            $sectionItem->setPriority($sectionItemPriorityCur-1);
                            $shifted = true;
                        }                           
                    }     
                    $sectionFrom->setPriority($sectionToPriority-1);  
                }
                else { //dolu                    
                    foreach ($sections as $sectionItem) {                      
                        $sectionItemPriorityCur = $sectionItem->getPriority();                                      
                       if  ( ($sectionItemPriorityCur >= $sectionToPriority) AND
                              ($sectionItemPriorityCur < $sectionFromPriority) ) {
                            $sectionItem->setPriority($sectionItemPriorityCur+1);
                            $shifted = true;
                        }                                                                                                
                    }
                    $sectionFrom->setPriority($sectionToPriority);  
                }
        }
        
            
        
//TOTO NENI ODLADENO ---- ASI ZATIM NESMYSLY       
        else {   // jsou to 2 papery , priority nejsou  v jednom paperu            
//tady jsou asi  NESMYSLY
            $sectionsTo = $this->paperSectionRepo->findByPaperIdFk($sectionTo->getPaperIdFk()); //vsechny sekce v paperu kam
            $sectionsFrom = $this->paperSectionRepo->findByPaperIdFk($sectionFrom->getPaperIdFk()); //vsechny sekce v paperu odkud
            $shifted = false;             
            
            foreach ($sectionsTo as $sectionItem) {
                $sectionItemPriorityCur = $sectionItem->getPriority();                                                
                if  ($sectionItemPriorityCur >= $sectionToPriority)  {
                    $sectionItem->setPriority($sectionItemPriorityCur+1);
                    $shifted = true;
                }                                                                                    
            }  
            
            foreach ($sectionsFrom as $sectionItem) {
                $sectionItemPriorityCur = $sectionItem->getPriority();                     
                if ($sectionItemPriorityCur > $sectionFromPriority) {
                    $sectionFrom->setPriority($sectionItemPriorityCur-1);
                    $shifted = true;                                    
                }                                                
            }            
            $sectionFrom->setPaperIdFk($paperToId) ;
            $sectionFrom->setPriority($sectionToPriority-1);            
        }
                                                      
        
        $this->assertTrue(1);
        
            throw new Exception();

    }
    
    
    
    public function testHavarie() {
            throw new Exception();
    }
    
    

}
