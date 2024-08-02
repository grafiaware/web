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
    
    
    
    
    public function testMoveBelow() {
        
        $sectionToId = 459;
        //$paperToId = 1279;
        $sectionFromId = 463;
        //$paperFromId = 1279;
                
        /** @var PaperSectionInterface $sectionTo */
        $sectionTo = $this->paperSectionRepo->get($sectionToId);        
        $paperToId = $sectionTo->getPaperIdFk();        
        /** @var PaperSectionInterface $sectionFrom */
        $sectionFrom = $this->paperSectionRepo->get($sectionFromId);
        $paperFromId = $sectionFrom->getPaperIdFk();
        
   
        $sectionToPriority = $sectionTo->getPriority();
        $sectionFromPriority = $sectionFrom->getPriority();
        
        
        //-----------------------------------
        if ($paperToId == $paperFromId) { // je to jeden paper , priority jsou v jednom paperu
            $sections = $this->paperSectionRepo->findByPaperIdFk($sectionTo->getPaperIdFk()); //vsechny sekce v paperu
            $shifted = false;   
                          /** @var PaperSectionInterface $sectionItem */
          
//  foreach ($sections as $sectionItem) {
            
//tady jseou NESMYSLY            

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
                      
                        if  ( ($sectionItemPriorityCur > $sectionToPriority) AND
                            ($sectionItemPriorityCur < $sectionFromPriority) ) {
                            $sectionItem->setPriority($sectionItemPriorityCur+1);
                            $shifted = true;
                        }                                                                                                
                    }  

                    $sectionFrom->setPriority($sectionToPriority-1);

                }

           
        } else {   // jsou to 2 papery , priority nejsou  v jednom paperu
            
//tady jseou NESMYSLY
            $sectionsTo = $this->paperSectionRepo->findByPaperIdFk($sectionTo->getPaperIdFk()); //vsechny sekce v paperu  
            $sectionsFrom = $this->paperSectionRepo->findByPaperIdFk($sectionFrom->getPaperIdFk()); //vsechny sekce v paperu
            $shifted = false;   

            $sectionFrom->setPaperIdFk($paperToId) ;
            $sectionFrom->setPriority($sectionToPriority-1);
            
            foreach ($sectionsTo as $sectionItem) {
                $sectionItemPriorityCur = $sectionItem->getPriority();                   
                             
                if   ($sectionItemPriorityCur < $sectionToPriority)  {
                        $sectionItem->setPriority($sectionItemPriorityCur-1);
                        $shifted = true;
                }                                                                                    
            } //foreach     
                      
            foreach ($sectionsFrom as $sectionItem) {
                $sectionItemPriorityCur = $sectionItem->getPriority();     
                
                if ($sectionItemPriorityCur > $sectionFromPriority) {

                    $sectionFrom->setPriority($sectionItemPriorityCur-1);
                    $shifted = true;                                    
                }                                                
            } //foreach     
            
        
            
        }
                                                      
        
        $this->assertTrue(1);
    }
    
    
    
    public function testHavarie() {
        throw new Exception;        
    }
    
    

}
