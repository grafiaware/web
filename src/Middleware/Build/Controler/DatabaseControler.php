<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Middleware\Build\Controler;

use Middleware\Build\Exception\HierarchyStepFailedException;
use Model\Dao\Hierarchy\HierarchyAggregateEditDao;

use Pes\Database\Manipulator\Manipulator;



/**
 * Description of DatabaseControler
 *
 * @author pes2704
 */
class DatabaseControler extends BuildControlerAbstract {

    public function dropDb() {
        ####
        #
        #   Před spuštením tohoto kroku:
        #   - musí existovat databáze
        #   - musí existovat uživatelé: prefix_everyone, prefix_authenticated, prefix_administrator
        #   - uživatel pod kterým je vytvořeno spojení k databázovámu stroji
        #     musí mít práva mazat databáze a uživatele- nejlépe tedy role DBA
        #
        ####
        $dropSteps[] = function() {
            return $this->executeFromTemplate("drop_database_template.sql", $this->container->get('build.config.drop'));
        };
        $dropSteps[] = function() {
            return $this->executeFromTemplate("drop_users_template.sql", $this->container->get('build.config.users'));
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
            return $this->executeFromTemplate("create_database_template.sql", $this->container->get('build.config.create'));
        };

        $this->manipulator = $this->container->get('manipulator_for_create_database');
        $this->log[] = "Záznam o vytvoření databáze ".(new \DateTime("now"))->format("d.m.Y H:i:s");
        $response = $this->executeSteps($createSteps);
        if ($this->container instanceof ContainerSettingsAwareInterface) {
            $this->container->reset('manipulator_for_create_database');
        }
        return $this->createResponseFromReport();
    }

    public function drop() {
        ####
        #
        #   Před spuštením tohoto kroku:
        #   - musí existovat databáze
        #   - musí existovat uživatelé: prefix_everyone, prefix_authenticated, prefix_administrator
        #   - uživatel pod kterým je vytvořeno spojení k databázovámu stroji
        #     musí mít práva mazat databáze a uživatele- nejlépe tedy role DBA
        #
        ####

        $dropSteps[] = function() {
            return $this->executeFromTemplate("drop_users_template.sql", $this->container->get('build.config.users'));
        };

        $this->manipulator = $this->container->get('manipulator_for_drop_database');
        $this->log[] = "Záznam o smazání tabulek, pohledů a uživatelů ".(new \DateTime("now"))->format("d.m.Y H:i:s");
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
        // users
        $this->executeSteps($dropSteps);
        if ($this->container instanceof ContainerSettingsAwareInterface) {
            $this->container->reset('manipulator_for_drop_database');
        }
        return $this->createResponseFromReport();
    }

    public function create() {

        ####
        #
        #   Před spuštením tohoto kroku:
        #   - nesmí existovat uživatelé: prefix_everyone, prefix_authenticated, prefix_administrator
        #   - uživatel pod kterým je vytvořeno první spojení k databázovámu stroji (před vytvořením databáze)
        #     musí mít práva zakládat databáze a přidělovat všechna práva - nejlépe tedy role DBA
        #
        ####
        $createSteps[] = function() {
            return $this->executeFromTemplate("create_users_template.sql", $this->container->get('build.config.users'));
        };

        $this->manipulator = $this->container->get('manipulator_for_create_database');
        $this->log[] = "Záznam o vytvoření databáze ".(new \DateTime("now"))->format("d.m.Y H:i:s");
        $response = $this->executeSteps($createSteps);
        if ($this->container instanceof ContainerSettingsAwareInterface) {
            $this->container->reset('manipulator_for_create_database');
        }
        return $this->createResponseFromReport();
    }

    public function convert() {
        $this->manipulator = $this->container->get(Manipulator::class);

        ### copy old table stranky ###
        $conversionSteps[] = function() {
            $configCopy = $this->container->get('build.config.copy');
            return $this->manipulator->copyTable($configCopy['source_table_name'], $configCopy['target_table_name']);
        };

        // smazání chybné stránky v grafia databázích s list='s_01' - chybná syntax list způdobí chyby při vyztváření adjlist - původní stránka nemá žádný obsah
        $conversionSteps[] = function() {
            $configCopy = $this->container->get('build.config.copy');
            return $this->executeFromString("DELETE FROM {$configCopy['target_table_name']} WHERE list = 's_01'");
        };

        ##### convert db ####

        $conversionSteps[] = function() {
            return $this->executeFromFile("page0_createStranky.sql");
        };
        $conversionSteps[] = function() {
            return $this->executeFromFile("page1_createTables.sql");
        };
        $conversionSteps[] = function() {
            return $this->executeFromFile("page2_0_insertIntoLanguage&MenuItemType.sql");
        };
//        $conversionSteps[] = function() {
//            return $this->executeFromFile("page2_1_insertIntoMenuItemNewSystemMenuRoots.sql");
//        };
        // -- v novém menu je titulní stránka kořenem menu 's' - přejmenuji list a0 na s
        $conversionSteps[] = function() {
            $oldRootsUpdateDefinitions = [
                ['menu_vertical', 'a0'],        // !! menu menu_vertical je s titulní stranou - kořen menu vznikne z existující stránky -> ve staré db změním stránku list=a0 na list=menu_vertical
            ];
            $executedSql = [];
            foreach ($oldRootsUpdateDefinitions as $oldDef) {
                return $this->executeFromTemplate("page2_3_updateStrankyOldMenuRoots.sql", ['svisle_menu_root_name'=>$oldDef[0], 'old_svisle_menu_root_name'=>$oldDef[1]]);
            }
        };
        $conversionSteps[] = function() {
            // [type, list, title]
            $menuDefinitions = [
                ['root', 'root', 'ROOT'],
                ['trash', 'trash', 'Trash'],
                ['paper', 'blocks', 'Blocks'],
//                ['paper', 'menu_vertical', 'Menu'],      // !! menu menu_vertical je s titulní stranou  -> ve staré db je stránka list=menu_vertical a má titulek
                ['paper', 'menu_horizontal', 'Menu'],
                ['paper', 'menu_redirect', 'Menu'],
            ];
            $executedSql = [];
            foreach ($menuDefinitions as $menuDef) {
                $executedSql[] .= $this->executeFromTemplate("page2_2_insertIntoMenuItemNewMenuRoot.sql", ['menu_root_type' => $menuDef[0], 'menu_root_list'=>$menuDef[1], 'menu_root_title'=>$menuDef[2]]);
            }
            return implode(PHP_EOL, $executedSql);
        };

        $conversionSteps[] = function() {
            return $this->executeFromTemplate("page2_4_insertIntoMenuItemFromStranky.sql", );
        };
        $conversionSteps[] = function() {
            $fileName = "page3_selectIntoAdjList.sql";
            return $this->executeFromFile($fileName);
        };
        $conversionSteps[] = function() {
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
            $fileName = "page5_insertIntoMenuRoot&ComponentTable.sql";
            return $this->executeFromFile($fileName);
        };
        $conversionSteps[] = function() {
            $fileName = "page6_createHierarchy_view.sql";
            return $this->executeFromFile($fileName);
        };

        $conversionSteps[] = function() {
            $fileName = "page7_insertIntoPaper.sql";
            return $this->executeFromFile($fileName);
        };
        $this->manipulator = $this->container->get(Manipulator::class);
        $this->setTimeLimit();
        $this->log[] = "Záznam o konverzi databáze ".(new \DateTime("now"))->format("d.m.Y H:i:s");
        $this->executeSteps($conversionSteps);
        return $this->createResponseFromReport();
    }
}
