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
        $response = $this->executeSteps($createSteps);
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
        #     musí mít práva mazat databáze a uživatele- nejlépe tedy role DBA
        #
        ####


        $this->manipulator = $this->container->get('manipulator_for_drop_database');
        $this->reportMessage[] = "Záznam o smazání tabulek a pohledů ".(new \DateTime("now"))->format("d.m.Y H:i:s");
        
                
        // views
        $selectStatement = $this->queryFromTemplate("dropTables/drop_tables_2_select_views_template.sql", $this->container->get('build.config.drop'));
        $viewNamesRows = $selectStatement->fetchAll(\PDO::FETCH_ASSOC);
        $dropQueriesString = '';
        foreach ($viewNamesRows as $viewNamesRow) {
            $dropQueriesString .= 'DROP TABLE IF EXISTS '.$viewNamesRow['table_name'].';'.PHP_EOL;
        }
        $this->executeFromTemplate('dropTables/drop_tables_3_drop_views_template.sql', ['dropViewsSql' => $dropQueriesString]);

        // tables
        $selectStatement = $this->queryFromTemplate("dropTables/drop_tables_0_select_tables_template.sql", $this->container->get('build.config.drop'));
        $tableNamesRows = $selectStatement->fetchAll(\PDO::FETCH_ASSOC);
        $dropQueriesString = '';
        foreach ($tableNamesRows as $tableNamesRow) {
            $dropQueriesString .= 'DROP TABLE IF EXISTS '.$tableNamesRow['table_name'].';'.PHP_EOL;
        }
        $this->executeFromTemplate('dropTables/drop_tables_1_drop_tables_template.sql', ['dropTablesSql' => $dropQueriesString]);

        
        
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
        return $this->makeAndConvert(false);
    }

    public function convert() {
        return $this->makeAndConvert(true);
    }

    private function makeAndConvert($convert) {

        $this->manipulator = $this->container->get('manipulator_for_convert');

        ##### convert db ####
        if($convert) {
            ### copy old table stranky ###
            $conversionSteps[] = function() {   // convert
                $convertConfig = $this->container->get('build.config.convert');
                return $this->manipulator->copyTable($convertConfig['source_table_name'], $convertConfig['target_table_name']);
            };

            $conversionSteps[] = function() {   // jen pro convert grafia
                $convertRepairs = $this->container->get('build.config.convert')['repairs'];
                foreach ($convertRepairs as $repair) {
                    $this->executeFromString($repair);
                }
            };

            $conversionSteps[] = function() {   // convert
                $this->executeFromFile("makeAndConvert/page0_createStrankyInnoDb&copy_stranky.sql");
            };
        }

        $conversionSteps[] = function() {
//            $this->executeFromFile("makeAndConvert/page1_createTables.sql");
            $this->executeFromFile("makeAndConvert/page1_v2_createTables.sql");
        };
        $conversionSteps[] = function() {
            $this->executeFromFile("makeAndConvert/page2_0_insertIntoLanguage&MenuItemType.sql");
        };

        if($convert) {
            $conversionSteps[] = function() {   // convert - pro případ, kdy kořen svislého menu je a0
                $oldRootsUpdateDefinitions = $this->container->get('build.config.convert')['updatestranky'];
                $executedSql = [];
                foreach ($oldRootsUpdateDefinitions as $oldDef) {
                    $this->executeFromTemplate("makeAndConvert/page2_1_updateStrankyInnodb.sql", [ 'old_menu_list'=>$oldDef[0], 'new_menu_list'=>$oldDef[1], 'new_menu_poradi'=>$oldDef[2]]);
                }
            };
        }

        $conversionSteps[] = function() {
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

        if($convert) {
            $conversionSteps[] = function() {   // convert
                $this->executeFromFile("makeAndConvert/page2_3_insertIntoMenuItemFromStranky.sql", );
            };
            $conversionSteps[] = function() {   // convert
                $this->executeFromFile("makeAndConvert/page2_4_updateMenuItemTypes&Active.sql", );
            };
        }

        $conversionSteps[] = function() {   // convert
            $fileName = "makeAndConvert/page3_selectIntoAdjList.sql";
            $this->executeFromFile($fileName);
        };
        $conversionSteps[] = function() {   // convert
                $adjList = $this->manipulator->findAllRows('menu_adjlist');
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

        $conversionSteps[] = function() {
            $fileName = "makeAndConvert/page4_alterMenuItem_fk.sql";
            $this->executeFromFile($fileName);
        };
        $conversionSteps[] = function() {
            // [type, list, title]
            $rootsListNames = $this->container->get('build.config.make.roots');
            foreach ($rootsListNames as $rootName) {
                $this->executeFromTemplate("makeAndConvert/page5_1_insertIntoMenuRootTable.sql", ['root' => $rootName]);
            }
        };

        if($convert) {
            $conversionSteps[] = function() {   // convert - pro případ, kdy konvertovaný "starý" kořen menu je home page
                $homeList = $this->container->get('build.config.convert')['home'];
                $this->executeFromTemplate("makeAndConvert/page5_2_insertHomeIntoBlockTable.sql", [ 'home_name'=>$homeList[0], 'home_list'=>$homeList[1]]);
            };
            $conversionSteps[] = function() {  // convert
                $fileName = "makeAndConvert/page5_3_insertIntoBlockTable.sql";
                $this->executeFromFile($fileName);
            };
        }

        $conversionSteps[] = function() {
            $fileName = "makeAndConvert/page6_createHierarchy_view.sql";
            $this->executeFromFile($fileName);
        };

        if($convert) {
            $conversionSteps[] = function() {    // convert
                $fileName = "makeAndConvert/page7_insertIntoPaper.sql";
                $this->executeFromFile($fileName);
            };
            $conversionSteps[] = function() {   // jen pro convert grafia
                $convertFinal = $this->container->get('build.config.convert')['final'];
                foreach ($convertFinal as $final) {
                    $this->executeFromString($final);
                }
            };
        }
            
        $this->setTimeLimit();
        $this->reportMessage[] = "Záznam o vytvoření a konverzi databáze ".(new \DateTime("now"))->format("d.m.Y H:i:s");
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
