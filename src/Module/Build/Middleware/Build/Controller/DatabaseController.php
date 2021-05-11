<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Build\Middleware\Build\Controller;

use Build\Middleware\Build\Exception\HierarchyStepFailedException;
use Model\Dao\Hierarchy\HierarchyAggregateEditDao;

use Pes\Database\Manipulator\Manipulator;



/**
 * Description of DatabaseController
 *
 * @author pes2704
 */
class DatabaseController extends BuildControllerAbstract {

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
            return $this->executeFromTemplate("drop_database_template.sql", $this->container->get('build.config.drop'));
        };

        $this->manipulator = $this->container->get('manipulator_for_drop_database');
        $this->log[] = "Záznam o smazání databáze ".(new \DateTime("now"))->format("d.m.Y H:i:s");
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
            return $this->executeFromTemplate("create_database_template.sql", $this->container->get('build.config.createdb'));
        };

        $this->manipulator = $this->container->get('manipulator_for_create_database');
        $this->log[] = "Záznam o vytvoření databáze ".(new \DateTime("now"))->format("d.m.Y H:i:s");
        $response = $this->executeSteps($createSteps);
        if ($this->container instanceof ContainerSettingsAwareInterface) {
            $this->container->reset('manipulator_for_create_database');
        }
        return $this->createResponseFromReport();
    }

    public function dropUsers() {
        $dropSteps[] = function() {
            return $this->executeFromTemplate("drop_user_everyone_template.sql", $this->container->get('build.config.users.everyone'));
        };
        $dropSteps[] = function() {
            return $this->executeFromTemplate("drop_users_granted_template.sql", $this->container->get('build.config.users.granted'));
        };
        // users

        $this->manipulator = $this->container->get('manipulator_for_create_database');  // TODO: provizorium - potřebuji connection bez db - třeba přidat manipulátor s loggere, pro drop users
        $this->log[] = "Záznam o smazání uživatelů ".(new \DateTime("now"))->format("d.m.Y H:i:s");
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
        $this->log[] = "Záznam o smazání tabulek a pohledů ".(new \DateTime("now"))->format("d.m.Y H:i:s");
        // tables
        $selectStatement = $this->queryFromTemplate("drop_tables_0_select_tables_template.sql", $this->container->get('build.config.drop'));
        $tableNamesRows = $selectStatement->fetchAll(\PDO::FETCH_ASSOC);
        $dropQueriesString = '';
        foreach ($tableNamesRows as $tableNamesRow) {
            $dropQueriesString .= 'DROP TABLE IF EXISTS '.$tableNamesRow['table_name'].';'.PHP_EOL;
        }
        $this->executeFromTemplate('drop_tables_1_drop_tables_template.sql', ['dropTablesSql' => $dropQueriesString]);
        // views
        $selectStatement = $this->queryFromTemplate("drop_tables_2_select_views_template.sql", $this->container->get('build.config.drop'));
        $viewNamesRows = $selectStatement->fetchAll(\PDO::FETCH_ASSOC);
        $dropQueriesString = '';
        foreach ($viewNamesRows as $viewNamesRow) {
            $dropQueriesString .= 'DROP TABLE IF EXISTS '.$viewNamesRow['table_name'].';'.PHP_EOL;
        }
        $this->executeFromTemplate('drop_tables_3_drop_views_template.sql', ['dropViewsSql' => $dropQueriesString]);

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
            return $this->executeFromTemplate("create_user_everyone_template.sql", $this->container->get('build.config.users.everyone'));
        };
        $createSteps[] = function() {
            return $this->executeFromTemplate("create_granted_users_template.sql", $this->container->get('build.config.users.granted'));
        };

        $this->manipulator = $this->container->get('manipulator_for_create_database');
        $this->log[] = "Záznam o vytvoření uživatelů databáze ".(new \DateTime("now"))->format("d.m.Y H:i:s");
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

        $this->manipulator = $this->container->get(Manipulator::class);

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
                return ;
            };

            $conversionSteps[] = function() {   // convert
                return $this->executeFromFile("page0_createStrankyInnoDb&copy_stranky.sql");
            };
        }

        $conversionSteps[] = function() {
            return $this->executeFromFile("page1_createTables.sql");
        };
        $conversionSteps[] = function() {
            return $this->executeFromFile("page2_0_insertIntoLanguage&MenuItemType.sql");
        };

        if($convert) {
            $conversionSteps[] = function() {   // convert - pro případ, kdy kořen svislého menu je a0
                $oldRootsUpdateDefinitions = $this->container->get('build.config.convert')['updatestranky'];
                $executedSql = [];
                foreach ($oldRootsUpdateDefinitions as $oldDef) {
                    return $this->executeFromTemplate("page2_1_updateStrankyInnodb.sql", [ 'old_menu_list'=>$oldDef[0], 'new_menu_list'=>$oldDef[1], 'new_menu_poradi'=>$oldDef[2]]);
                }
            };
        }

        $conversionSteps[] = function() {
            // [type, list, title]
            $rootsDefinitions = $this->container->get('build.config.make')['items'];
            $executedSql = [];
            foreach ($rootsDefinitions as $rootDef) {
                $executedSql[] .= $this->executeFromTemplate("page2_2_insertIntoMenuItemNewMenuRoot.sql", ['menu_root_type' => $rootDef[0], 'menu_root_list'=>$rootDef[1], 'menu_root_title'=>$rootDef[2]]);
            }
            return implode(PHP_EOL, $executedSql);
        };

        if($convert) {
            $conversionSteps[] = function() {   // convert
                return $this->executeFromFile("page2_3_insertIntoMenuItemFromStranky.sql", );
            };
            $conversionSteps[] = function() {   // convert
                return $this->executeFromFile("page2_4_updateMenuItemTypes&Active.sql", );
            };
        }

        $conversionSteps[] = function() {   // convert
            $fileName = "page3_selectIntoAdjList.sql";
            return $this->executeFromFile($fileName);
        };
        $conversionSteps[] = function() {   // convert
                $adjList = $this->manipulator->findAllRows('menu_adjlist');
                if (is_array($adjList) AND count($adjList)) {
                    $this->log[] = "Načteno ".count($adjList)." položek z tabulky 'menu_adjlist'.";
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
                    $this->log[] = "Skriptem pomocí Hierarchy vygenerována tabulka 'menu_nested_set' z dat tabulky 'menu_adjlist'.";
                    $this->log[] = $this->timer->interval();
                    $this->log[] = "Vykonán krok.";
                }
            return TRUE;
        };

        $conversionSteps[] = function() {
            $fileName = "page4_alterMenuItem_fk.sql";
            return $this->executeFromFile($fileName);
        };
        $conversionSteps[] = function() {
            // [type, list, title]
            $rootsListNames = $this->container->get('build.config.make.roots');
            $executedSql = [];
            foreach ($rootsListNames as $rootName) {
                $executedSql[] .= $this->executeFromTemplate("page5_1_insertIntoMenuRootTable.sql", ['root' => $rootName]);
            }
            return implode(PHP_EOL, $executedSql);
        };

        if($convert) {
            $conversionSteps[] = function() {   // convert - pro případ, kdy konvertovaný "starý" kořen menu je home page
                $homeList = $this->container->get('build.config.convert')['home'];
                return $this->executeFromTemplate("page5_2_insertHomeIntoBlockTable.sql", [ 'home_name'=>$homeList[0], 'home_list'=>$homeList[1]]);
            };
            $conversionSteps[] = function() {  // convert
                $fileName = "page5_3_insertIntoBlockTable.sql";
                return $this->executeFromFile($fileName);
            };
        }

        $conversionSteps[] = function() {
            $fileName = "page6_createHierarchy_view.sql";
            return $this->executeFromFile($fileName);
        };

        if($convert) {
            $conversionSteps[] = function() {    // convert
                $fileName = "page7_insertIntoPaper.sql";
                return $this->executeFromFile($fileName);
            };
        }

        $this->manipulator = $this->container->get(Manipulator::class);
        $this->setTimeLimit();
        $this->log[] = "Záznam o vytvoření a konverzi databáze ".(new \DateTime("now"))->format("d.m.Y H:i:s");
        $this->executeSteps($conversionSteps);
        return $this->createResponseFromReport();
    }
}
