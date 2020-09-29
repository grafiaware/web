<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Middleware\Build\Controler;

use Middleware\Build\Exception\HierarchyStepFailedException;
use Model\Dao\Hierarchy\NodeEditDao;

use Pes\Database\Manipulator\Manipulator;



/**
 * Description of DatabaseControler
 *
 * @author pes2704
 */
class DatabaseControler extends BuildControlerAbstract {

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
            return $this->executeFromTemplate("drop_database_template.sql", $this->container->get('build.config.drop'));
        };
        $dropSteps[] = function() {
            return $this->executeFromTemplate("drop_users_template.sql", $this->container->get('build.config.users'));
        };

        $this->manipulator = $this->container->get('manipulator_for_drop_database');
        $this->log[] = "Záznam o smazání databáze ".(new \DateTime("now"))->format("d.m.Y H:i:s");
        $this->executeSteps($dropSteps);
        return $this->createResponseFromReport();
    }
    public function create() {

        ####
        #
        #   Před spuštením tohoto kroku:
        #   - nesmí existovat databáze
        #   - nesmí existovat uživatelé: prefix_everyone, prefix_authenticated, prefix_administrator
        #   - uživatel pod kterým je vytvořeno první spojení k databázovámu stroji (před vytvořením databáze)
        #     musí mít práva zakládat databáze a přidělovat všechna práva - nejlépe tedy role DBA
        #
        ####
        $createSteps[] = function() {
            return $this->executeFromTemplate("create_database_template.sql", $this->container->get('build.config.create'));
        };
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
        $conversionSteps[] = function() {
            return $this->executeFromFile("page2_1_insertIntoMenuItemNewSystemMenuRoots.sql");
        };
        $conversionSteps[] = function() {
            $menuDefinitions = [
//                ['s', 'Menu s'],
                ['l', 'Menu l'],
                ['p', 'Menu p'],
            ];
            $executedSql = [];
            foreach ($menuDefinitions as $menuDef) {
                $executedSql[] .= $this->executeFromTemplate("page2_2_insertIntoMenuItemNewMenuRoot.sql", ['menu_root_name'=>$menuDef[0], 'menu_root_title'=>$menuDef[1]]);
            }
            return implode(PHP_EOL, $executedSql);
        };
        // -- v novém menu je titulní stránka kořenem menu 's' - přejmenuji list a0 na s
        $conversionSteps[] = function() {
            $oldRootsUpdateDefinitions = [
                ['s', 'a0'],
            ];
            $executedSql = [];
            foreach ($oldRootsUpdateDefinitions as $oldDef) {
                return $this->executeFromTemplate("page2_3_updateStrankyOldMenuRoots.sql", ['svisle_menu_root_name'=>$oldDef[0], 'old_svisle_menu_root_name'=>$oldDef[1]]);
            }
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
                    $hierachy = $this->container->get(NodeEditDao::class);
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
                                        $this->manipulator->executeQuery("UPDATE menu_item SET menu_item.uid_fk='$childUid'
                                           WHERE menu_item.list='{$adjRow['child']}'");
                                    }
                                } else {  // pro rodiče neexistuje položka v menu_item -> je to jen prázdný uzel ve struktuře menu
                                    $childUid = $hierachy->addChildNodeAsLast($rootUid);   // ???
                                }
                            } else {  // rodič je root
                                // UPDATE menu_item položky pro všechny jazyky (nested set je jeden pro všechny jazyky)
                                $this->manipulator->executeQuery("UPDATE menu_item SET menu_item.uid_fk='$rootUid'
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
