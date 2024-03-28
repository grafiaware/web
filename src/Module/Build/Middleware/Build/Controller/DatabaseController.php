<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Build\Middleware\Build\Controller;

use Psr\Http\Message\ServerRequestInterface;

use Build\Middleware\Build\Exception\HierarchyStepFailedException;
use Red\Model\Dao\Hierarchy\HierarchyAggregateEditDao;

use Pes\Database\Manipulator\Manipulator;

use Container\BuildContainerConfigurator;

/**
 * Description of DatabaseController
 *
 * @author pes2704
 */
class DatabaseController extends BuildControllerAbstract {
    
    const LIST_POSTFIX = '_child';

    public function listConfig(ServerRequestInterface $request) {
        $configurator = new BuildContainerConfigurator();
        $params = "<h3>Params</h3><pre>".print_r($configurator->getParams(), true)."</pre>";
        $factoriesNames = array_keys($configurator->getFactoriesDefinitions());
        $factoriesValuesList = [];
        foreach ($factoriesNames as $name) {
            $factoriesValuesList[$name] = $this->container->get($name);
        }
        $factories = "<h3>Factories values</h3><pre>".print_r($factoriesValuesList, true)."</pre>";
        $html = "<div>".$params.PHP_EOL.$factories."</div>" ;
        return $this->createResponseFromString($request, $html);
    }

    public function dropDb() {
        ####
        #
        #   Před spuštením tohoto kroku:
        #   - musí existovat databáze
        #   - uživatel pod kterým je vytvořeno spojení k databázovámu stroji
        #     musí mít práva mazat databáze a uživatele- nejlépe tedy role DBA
        #
        ####
        $dropSteps[] = function() {
            $this->executeFromTemplate("dropDb/drop_database_template.sql", $this->container->get('build.config.drop'));
        };

        $this->manipulator = $this->container->get('manipulator_for_drop_database');
        $this->reportMessage[] = "Záznam o smazání databáze ".(new \DateTime("now"))->format("d.m.Y H:i:s");
        $this->executeSteps($dropSteps);
        if ($this->container instanceof ContainerSettingsAwareInterface) {
            $this->container->reset('manipulator_for_drop_database');
        }
        return $this->createResponseFromReport();
    }

    public function createDb() {

        ####
        #
        #   Před spuštením tohoto kroku:
        #   - nesmí existovat databáze
        #   - uživatel pod kterým je vytvořeno první spojení k databázovámu stroji (před vytvořením databáze)
        #     musí mít práva zakládat databáze a přidělovat všechna práva - nejlépe tedy role DBA
        #
        ####
        $createSteps[] = function() {
            $this->executeFromTemplate("createDb/create_database_template.sql", $this->container->get('build.config.createdb'));
        };

        $this->manipulator = $this->container->get('manipulator_for_create_database');
        $this->reportMessage[] = "Záznam o vytvoření databáze ".(new \DateTime("now"))->format("d.m.Y H:i:s");
        $this->executeSteps($createSteps);
        if ($this->container instanceof ContainerSettingsAwareInterface) {
            $this->container->reset('manipulator_for_create_database');
        }
        return $this->createResponseFromReport();
    }

    public function dropUsers() {
        $dropSteps[] = function() {
            $this->executeFromTemplate("dropUsers/drop_user_everyone_template.sql", $this->container->get('build.config.createdropusers.everyone'));
        };
        $dropSteps[] = function() {
            $this->executeFromTemplate("dropUsers/drop_users_granted_template.sql", $this->container->get('build.config.createdropusers.granted'));
        };
        // users

        $this->manipulator = $this->container->get('manipulator_for_create_database');  // TODO: provizorium - potřebuji connection bez db - třeba přidat manipulátor s loggere, pro drop users
        $this->reportMessage[] = "Záznam o smazání uživatelů ".(new \DateTime("now"))->format("d.m.Y H:i:s");
        $this->executeSteps($dropSteps);
        return $this->createResponseFromReport();
    }

    public function dropTables() {
        ####
        #
        #   Před spuštením tohoto kroku:
        #   - musí existovat databáze
        #   - musí existovat uživatelé: prefix_everyone, prefix_authenticated, prefix_administrator
        #   - uživatel pod kterým je vytvořeno spojení k databázovámu stroji
        #     musí mít práva mazat databáze a uživatele - nejlépe tedy role DBA
        #
        ####


        $this->manipulator = $this->container->get('manipulator_for_drop_database');
        $this->reportMessage[] = "Záznam o smazání tabulek a pohledů ".(new \DateTime("now"))->format("d.m.Y H:i:s");
        
                
        // views
        $selectViewsStatement = $this->queryFromTemplate("dropTables/drop_tables_2_select_views_template.sql", $this->container->get('build.config.droptables'));
        $viewNamesRows = $selectViewsStatement->fetchAll(\PDO::FETCH_ASSOC);
        $viewNames = [];
        foreach ($viewNamesRows as $viewNamesRow) {
            $viewNames[] = "`{$viewNamesRow['table_name']}`";
        }
        $this->executeFromTemplate('dropTables/drop_tables_3_drop_views_template.sql', ['tables' => implode(", ", $viewNames)]);

        // tables
        $selectTablesStatement = $this->queryFromTemplate("dropTables/drop_tables_0_select_tables_template.sql", $this->container->get('build.config.droptables'));
        $tableNamesRows = $selectTablesStatement->fetchAll(\PDO::FETCH_ASSOC);
        $tableNames = [];
        foreach ($tableNamesRows as $tableNamesRow) {
            $tableNames[] = "`{$tableNamesRow['table_name']}`";
        }
        $this->executeFromTemplate('dropTables/drop_tables_1_drop_tables_template.sql', ['tables' => implode(", ", $tableNames)]);

        
        
        if ($this->container instanceof ContainerSettingsAwareInterface) {
            $this->container->reset('manipulator_for_drop_database');
        }
        return $this->createResponseFromReport();
    }

    public function createUsers() {

        ####
        #
        #   Před spuštením tohoto kroku:
        #   - nesmí existovat uživatelé: prefix_everyone, prefix_authenticated, prefix_administrator
        #   - uživatel pod kterým je vytvořeno první spojení k databázovámu stroji (před vytvořením databáze)
        #     musí mít práva zakládat databáze a přidělovat všechna práva - nejlépe tedy role DBA
        #
        ####
        $createSteps[] = function() {
            $this->executeFromTemplate("createUsers/create_user_everyone_template.sql", $this->container->get('build.config.createdropusers.everyone'));
        };
        $createSteps[] = function() {
            $this->executeFromTemplate("createUsers/create_granted_users_template.sql", $this->container->get('build.config.createdropusers.granted'));
        };

        $this->manipulator = $this->container->get('manipulator_for_create_database');
        $this->reportMessage[] = "Záznam o vytvoření uživatelů databáze ".(new \DateTime("now"))->format("d.m.Y H:i:s");
        $response = $this->executeSteps($createSteps);
        if ($this->container instanceof ContainerSettingsAwareInterface) {
            $this->container->reset('manipulator_for_create_database');
        }
        return $this->createResponseFromReport();
    }

    public function make() {
        return $this->makeAndConvert('make');
    }

    public function convert() {
        return $this->makeAndConvert('convert');
    }
    
    public function import() {
        return $this->makeAndConvert('import');
    }
    

    private function makeAndConvert($convert) {

        $this->manipulator = $this->container->get('manipulator_for_convert');

        
        $p_1_v2_createTables = function() {
//            $this->executeFromFile("makeAndConvert/page1_createTables.sql");
            $this->executeFromFile("makeAndConvert/page1_v2_createTables.sql");
        };       
        $p_2_0_insertIntoLanguageMenuItemApi = function() {
            $this->executeFromFile("makeAndConvert/page2_0_insertIntoLanguage&MenuItemApi.sql");
        };
        $p_2_2_insertIntoMenuItemNewMenuRoot_convert = function() { // convert
                // [type, list, title]
                $rootsDefinitions = $this->container->get('build.config.convert.items');
                foreach ($rootsDefinitions as $rootDef) {
                    $this->executeFromTemplate("makeAndConvert/page2_2_insertIntoMenuItemNewMenuRoot.sql", 
                        [
                            'menu_root_api_module' => $rootDef[0], 
                            'menu_root_api_generator' => $rootDef[1],
                            'menu_root_list'=>$rootDef[2],
                            'menu_root_title'=>$rootDef[3],
                        ]);
                }
        };
        $p_2_2_insertIntoMenuItemNewMenuRoot_make =  function() {
                // [type, list, title]
                $rootsDefinitions = $this->container->get('build.config.make.items');
                foreach ($rootsDefinitions as $rootDef) {
                    $this->executeFromTemplate("makeAndConvert/page2_2_insertIntoMenuItemNewMenuRoot.sql", 
                        [
                            'menu_root_api_module' => $rootDef[0], 
                            'menu_root_api_generator' => $rootDef[1],
                            'menu_root_list'=>$rootDef[2],
                            'menu_root_title'=>$rootDef[3],
                        ]);
                }
        };            
        $p_2_2_insertIntoMenuItemNewMenuRoot_make_zacatekMenu = function() {
                // [type, list, title]
                $rootsListNames1 = $this->container->get('build.config.make.menuroots');
                foreach ($rootsListNames1 as $rootName1) {
                    if ( ($rootName1 != 'root' ) and ($rootName1 != 'trash' ) ) {
                        $this->executeFromTemplate("makeAndConvert/page2_2_insertIntoMenuItemNewMenuRoot.sql",                                                    //                                                   
                                                    [
                                                    'menu_root_api_module' => 'red', 
                                                    'menu_root_api_generator' => 'select',
                                                    'menu_root_list'=> $rootName1 . self::LIST_POSTFIX ,
                                                    'menu_root_title'=> 'Tady začni...',
                                                    ]);
                    }    
                }                       
        };
        $build_config_convert_copy = function()  {  // convert // kopie tabulky stranky ze staré do nové db
                $convertConfig = $this->container->get('build.config.convert.copy');
                return $this->manipulator->copyTable($convertConfig['source'], $convertConfig['target']);
        };
        $build_config_convert_repairs = function() { // convert  // prostor pro úpravy obsahu tabulky stranky v nove db
                $convertRepairs = $this->container->get('build.config.convert.repairs');
                foreach ($convertRepairs as $repair) {
                    $this->executeFromString($repair);
                }
        };
        $p_2_1_updateStranky  = function() {   // convert - pro případ, kdy kořen menu je některá ze stránek označených aXX ve staré db
                $oldRootsUpdateDefinitions = $this->container->get('build.config.convert.updatestranky');
                foreach ($oldRootsUpdateDefinitions as $oldDef) {
                    $this->executeFromTemplate("makeAndConvert/page2_1_updateStranky.sql", [ 'old_menu_list'=>$oldDef[0], 'new_menu_list'=>$oldDef[1], 'new_menu_poradi'=>$oldDef[2]]);
                }
        };
        $p_0_createStrankyInnoDbcopy_stranky = function() { // convert  // kopie tabulky stranky do stranky typu InnoDb
                $this->executeFromFile("makeAndConvert/page0_createStrankyInnoDb&copy_stranky.sql");
        };
        $p_2_3_insertIntoMenuItemFromStranky = function() { // convert  // stranky `lang_code_fk`, `list`, `order`,  `title`, `active`, `auto_generated` do isert into menu_item
                $this->executeFromFile("makeAndConvert/page2_3_insertIntoMenuItemFromStranky.sql", );
        };
        $p_2_4_updateMenuItemTypesActive = function() {   // convert// úprava api_module, api_geherator a active v menu_item
                $this->executeFromFile("makeAndConvert/page2_4_updateMenuItemTypes&Active.sql", );
        };
        $p_3_1_p_3_2_selectIntoAdjList = function() {  
            $rootName = $this->container->get('build.config.make.root')[0];
            $menuRoots = $this->container->get('build.config.make.menuroots');
            $inMenuRoots = implode("', '", $menuRoots);
            $this->executeFromTemplate("makeAndConvert/page3_1_selectIntoAdjList.sql", ['root'=>$rootName]);
            $this->executeFromTemplate("makeAndConvert/page3_2_selectIntoAdjList.sql", ['in_menu_roots'=>$inMenuRoots, 'root'=>$rootName]);               
        };
        $p_3_3_p_3_4_selectIntoAdjList = function() { // convert
                $rootName = $this->container->get('build.config.make.root')[0];
                $menuRoots = $this->container->get('build.config.make.menuroots');
                $inMenuRoots = implode("', '", $menuRoots);                
                $map = $this->container->get('build.config.convert.prefixmap');
                foreach ($map as $prefix=>$menuRoot) {
                    $this->executeFromTemplate("makeAndConvert/page3_3_selectIntoAdjList.sql", ['in_menu_roots'=>$inMenuRoots, 'root'=>$rootName, 'menu_root'=>$menuRoot, 'prefix'=>$prefix]);                
                }
                $this->executeFromTemplate("makeAndConvert/page3_4_selectIntoAdjList.sql", ['in_menu_roots'=>$inMenuRoots, 'root'=>$rootName]);                
        };            
        $p_3_2a_selectIntoAdjList =  function() {
                $rootName = $this->container->get('build.config.make.root')[0];
                $menuRoots = $this->container->get('build.config.make.menuroots');
                $inMenuRoots = implode("', '", $menuRoots);                
                $this->executeFromTemplate("makeAndConvert/page3_2a_selectIntoAdjList.sql", ['in_menu_roots'=>$inMenuRoots, 'root'=>$rootName, 'child'=>self::LIST_POSTFIX]);               
        };
        $p_3_5_selectNodesFromAjdlist_Hierarchy = function() {  
//                $adjList = $this->manipulator->findAllRows('menu_adjlist');
            $stmt = $this->queryFromFile("makeAndConvert/page3_5_selectNodesFromAjdlist.sql");
            $adjList = $stmt->fetchAll(\PDO::FETCH_ASSOC);                  
                if (is_array($adjList) AND count($adjList)) {
                    $this->reportMessage[] = "Načteno ".count($adjList)." položek z tabulky 'menu_adjlist'.";
                    $hierachy = $this->container->get(HierarchyAggregateEditDao::class);
                    // $hierachy->newNestedSet() založí kořenovou položku nested setu a vrací její uid
                    $rootUid = $hierachy->newNestedSet();
                    try {
                        foreach ($adjList as $adjRow) {
                            if (isset($adjRow['parent'])) {  // rodič není root
                                // najde menu_item pro všechny jazyky - použiji jen jeden (mají stejné nested_set uid_fk, liší se jen lang_code_fk)
                                $parentItems = $this->manipulator->find("menu_item", ["list"=>$adjRow['parent']]);
                                if (count($parentItems) > 0) { // pro rodiče existuje položka v menu_item -> není to jen prázdný uzel ve struktuře menu
                                    $childItems = $this->manipulator->find("menu_item", ["list"=>$adjRow['child']]);
                                    if ($childItems) {
                                        $childUid = $hierachy->addChildNodeAsLast($parentItems[0]['uid_fk']);  //jen jeden parent
                                        // UPDATE menu_item položky pro všechny jazyky (nested set je jeden pro všechny jazyky)
                                        $this->manipulator->exec("UPDATE menu_item SET menu_item.uid_fk='$childUid'
                                           WHERE menu_item.list='{$adjRow['child']}'");
                                    }
                                } else {  // pro rodiče neexistuje položka v menu_item -> je to jen prázdný uzel ve struktuře menu
                                    $childUid = $hierachy->addChildNodeAsLast($rootUid);   // ???
                                }
                            } else {  // rodič je root
                                // UPDATE menu_item položky pro všechny jazyky (nested set je jeden pro všechny jazyky)
                                $this->manipulator->exec("UPDATE menu_item SET menu_item.uid_fk='$rootUid'
                                   WHERE menu_item.list='{$adjRow['child']}'");
                            }
                        }
                    } catch (\Exception $e) {
                        throw new HierarchyStepFailedException("Chybný krok. Nedokončeny všechny akce v kroku. Chyba nastala při transformaci adjacency list na nested tree.", 0, $e);
                    }
                    $this->reportMessage[] = "Skriptem pomocí Hierarchy vygenerována tabulka 'menu_nested_set' z dat tabulky 'menu_adjlist'.";
                    $this->reportMessage[] = $this->timer->interval();
                    $this->reportMessage[] = "Vykonán krok.";
                }
            return TRUE;
        };
        $p_4_alterMenuItem_fk = function() {
            $fileName = "makeAndConvert/page4_alterMenuItem_fk.sql";
            $this->executeFromFile($fileName);
        };
        $p_5_1_insertIntoMenuRootTable = function() {
            // [type, list, title]
            $rootsListNames = $this->container->get('build.config.make.menuroots');
            foreach ($rootsListNames as $rootName) {
                $this->executeFromTemplate("makeAndConvert/page5_1_insertIntoMenuRootTable.sql", ['root' => $rootName]);
            }
        };
        $p_5_2_insertHomeIntoBlockTable_convert = function() {  // convert - pro případ, kdy konvertovaný "starý" kořen menu je home page
                $homeList = $this->container->get('build.config.convert.home');
                $this->executeFromTemplate("makeAndConvert/page5_2_insertHomeIntoBlockTable.sql", [ 'home_name'=>$homeList[0], 'home_list'=>$homeList[1]]);
        };
        $p_5_3_insertIntoBlockTable =  function() {  // convert
                $fileName = "makeAndConvert/page5_3_insertIntoBlockTable.sql";
                $this->executeFromFile($fileName);
        };
        $p_5_2_insertHomeIntoBlockTable_make =  function() { 
                $homeList = $this->container->get('build.config.make.home');
                $this->executeFromTemplate("makeAndConvert/page5_2_insertHomeIntoBlockTable.sql", [ 'home_name'=>$homeList[0], 'home_list'=>$homeList[1] . self::LIST_POSTFIX]);
        }; 
        $p_6_createHierarchy_view = function() {
            $fileName = "makeAndConvert/page6_createHierarchy_view.sql";
            $this->executeFromFile($fileName);
        };
        $p_6__insertIntoPaperAndSection = function() {    //convert  // naplní paper a section z stranky_innodb
                $fileName = "makeAndConvert/page7_insertIntoPaperAndSection.sql";
                $this->executeFromFile($fileName);
        };
        $build_config_convert_final = function() {   //convert 
                $convertFinal = $this->container->get('build.config.convert.final');
                foreach ($convertFinal as $final) {
                    $this->executeFromString($final);
                }
        };    
        
        
        //-------------------------------------------------
        //if ($convert) {$mark ='convert';} else { $mark ='make';}
        
        switch ($convert) {                
            case 'convert':     
                $conversionSteps[] = $p_1_v2_createTables ;      
                $conversionSteps[] = $p_2_0_insertIntoLanguageMenuItemApi;     
                $conversionSteps[] = $p_2_2_insertIntoMenuItemNewMenuRoot_convert;
                
                ### copy old table stranky ###
                $conversionSteps[] = $build_config_convert_copy ; //kopie tabulky stranky ze staré do nové db
                $conversionSteps[] = $build_config_convert_repairs ;
                $conversionSteps[] = $p_2_1_updateStranky;
                $conversionSteps[] = $p_0_createStrankyInnoDbcopy_stranky;            
                $conversionSteps[] = $p_2_3_insertIntoMenuItemFromStranky;
                $conversionSteps[] = $p_2_4_updateMenuItemTypesActive;
                
                $conversionSteps[] = $p_3_1_p_3_2_selectIntoAdjList;   
                $conversionSteps[] = $p_3_3_p_3_4_selectIntoAdjList;
                
                $conversionSteps[] = $p_3_5_selectNodesFromAjdlist_Hierarchy;              
                $conversionSteps[] = $p_4_alterMenuItem_fk;        
                $conversionSteps[] = $p_5_1_insertIntoMenuRootTable;
                
                $conversionSteps[] = $p_5_2_insertHomeIntoBlockTable_convert;
                $conversionSteps[] = $p_5_3_insertIntoBlockTable;
                
                $conversionSteps[] = $p_6_createHierarchy_view;   
                
                $conversionSteps[] = $p_6__insertIntoPaperAndSection;            
                $conversionSteps[] = $build_config_convert_final;       
                break;
            
            case 'make':
                $conversionSteps[] = $p_1_v2_createTables ;      
                $conversionSteps[] = $p_2_0_insertIntoLanguageMenuItemApi;  
                $conversionSteps[] = $p_2_2_insertIntoMenuItemNewMenuRoot_make;                                                    
                    // zacatky menu, kdyz nedela convert
                $conversionSteps[] = $p_2_2_insertIntoMenuItemNewMenuRoot_make_zacatekMenu; 
                
                $conversionSteps[] = $p_3_1_p_3_2_selectIntoAdjList;    
                $conversionSteps[] = $p_3_2a_selectIntoAdjList;
                
                $conversionSteps[] = $p_3_5_selectNodesFromAjdlist_Hierarchy;              
                $conversionSteps[] = $p_4_alterMenuItem_fk;        
                $conversionSteps[] = $p_5_1_insertIntoMenuRootTable;
               
                $conversionSteps[] = $p_5_2_insertHomeIntoBlockTable_make;   
                
                $conversionSteps[] = $p_6_createHierarchy_view;                       
                break;
             
             case 'import':
                $conversionSteps[]= ''; 
               /*    ### copy old table stranky ###
                $conversionSteps[] = $build_config_convert_copy ; //kopie tabulky stranky ze staré do nové db
                $conversionSteps[] = $build_config_convert_repairs ;
                $conversionSteps[] = $p_2_1_updateStranky;
                $conversionSteps[] = $p_0_createStrankyInnoDbcopy_stranky;            
                $conversionSteps[] = $p_2_3_insertIntoMenuItemFromStranky;
                $conversionSteps[] = $p_2_4_updateMenuItemTypesActive;
                
                $conversionSteps[] = $p_3_1_p_3_2_selectIntoAdjList;   
                $conversionSteps[] = $p_3_3_p_3_4_selectIntoAdjList;
                
                $conversionSteps[] = $p_3_5_selectNodesFromAjdlist_Hierarchy;              
                $conversionSteps[] = $p_4_alterMenuItem_fk;        
                $conversionSteps[] = $p_5_1_insertIntoMenuRootTable;*/
                                 
                break; 
             
            default:     
        }            
        
        
        
//        //------------------------------------------------------------------------
//        $conversionSteps[] = $p_1_v2_createTables ;      
//        $conversionSteps[] = $p_2_0_insertIntoLanguageMenuItemApi;        
//
//        if ($convert) {
//            $conversionSteps[] = $p_2_2_insertIntoMenuItemNewMenuRoot_convert;
//        } else {
//            $conversionSteps[] = $p_2_2_insertIntoMenuItemNewMenuRoot_make;                                                    
//            // zacatky menu, kdyz nedela convert
//            $conversionSteps[] = $p_2_2_insertIntoMenuItemNewMenuRoot_make_zacatekMenu;                                                       
//        }
//               
//        if($convert) {
//            ### copy old table stranky ###
//            $conversionSteps[] = $build_config_convert_copy ;
//            $conversionSteps[] = $build_config_convert_repairs ;
//            $conversionSteps[] = $p_2_1_updateStranky;
//            $conversionSteps[] = $p_0_createStrankyInnoDbcopy_stranky;            
//            $conversionSteps[] = $p_2_3_insertIntoMenuItemFromStranky;
//            $conversionSteps[] = $p_2_4_updateMenuItemTypesActive;
//        }
//        
//        $conversionSteps[] = $p_3_1_p_3_2_selectIntoAdjList;                     
//        
//        if ($convert) {
//            $conversionSteps[] = $p_3_3_p_3_4_selectIntoAdjList;
//        } else  {
//            $conversionSteps[] = $p_3_2a_selectIntoAdjList;
//        }
//                       
//        $conversionSteps[] = $p_3_5_selectNodesFromAjdlist_Hierarchy;        
//      
//        $conversionSteps[] = $p_4_alterMenuItem_fk;
//        
//        $conversionSteps[] = $p_5_1_insertIntoMenuRootTable;
//
//        if($convert) {
//            $conversionSteps[] = $p_5_2_insertHomeIntoBlockTable_convert;
//            $conversionSteps[] = $p_5_3_insertIntoBlockTable;
//        }else{        
//            $conversionSteps[] = $p_5_2_insertHomeIntoBlockTable_make;           
//        }
//               
//        $conversionSteps[] = $p_6_createHierarchy_view;        
//
//        if($convert) {
//            $conversionSteps[] = $p_6__insertIntoPaperAndSection;            
//            $conversionSteps[] = $build_config_convert_final;
//        }                   
//        //-------------------------------------------------------------------------
        
        
        $this->setTimeLimit();
        $this->reportMessage[] = "Záznam o vytvoření a konverzi databáze ".(new \DateTime("now"))->format("d.m.Y H:i:s"). ' '. $convert;
        
        
        $this->executeSteps($conversionSteps);
        return $this->createResponseFromReport();
    }
    
    
    
    
    
    
    private function createAuthUsers() {
        
        ####
        #
        #   Před spuštením tohoto kroku:
        #   - nesmí existovat uživatelé: prefix_everyone, prefix_authenticated, prefix_administrator
        #   - uživatel pod kterým je vytvořeno první spojení k databázovámu stroji (před vytvořením databáze)
        #     musí mít práva zakládat databáze a přidělovat všechna práva - nejlépe tedy role DBA
        #
        ####
        $createSteps[] = function() {
            $this->executeFromTemplate("createUsers/create_user_everyone_template.sql", $this->container->get('build.config.createdropusers.everyone'));
        };
        $createSteps[] = function() {
            $this->executeFromTemplate("createUsers/create_granted_users_template.sql", $this->container->get('build.config.createdropusers.granted'));
        };

        $this->manipulator = $this->container->get('manipulator_for_create_database');
        $this->reportMessage[] = "Záznam o vytvoření uživatelů databáze ".(new \DateTime("now"))->format("d.m.Y H:i:s");
        $response = $this->executeSteps($createSteps);
        if ($this->container instanceof ContainerSettingsAwareInterface) {
            $this->container->reset('manipulator_for_create_database');
        }
        return $this->createResponseFromReport();
    }
    
    protected function dropAuthUsers() {
        
    }
}
