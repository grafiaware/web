<?php
namespace Middleware\Konverze;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;

use Pes\Container\Container;
use Pes\Middleware\AppMiddlewareAbstract;
use Pes\Debug\Timer;

use Container\KonverzeContainerConfigurator;

use Pes\Database\Manipulator\Manipulator;
use Database\Hierarchy\EditHierarchy;

use Pes\Http\Headers;
use Pes\Http\Body;
use Pes\Http\Response;
/**
 * Description of MenuApplication
 *
 * @author pes2704
 */
class Konverze extends AppMiddlewareAbstract implements MiddlewareInterface {

    /**
     * @var Timer
     */
    private $timer;

    /**
     * @var Container
     */
    private $container;

    /** @var Pes\Database\Manipulator\Manipulator  */
    private $manipulator;
    private $log;


   //-------------------------------------------------------------------------------
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {

        $this->container = (new KonverzeContainerConfigurator())->configure(new Container($this->getApp()->getAppContainer()));
        $this->manipulator = $this->container->get(Manipulator::class);

        $steps[] = function() {
            $fileName = "page0_createStranky_copy.sql";
            return $this->krok($fileName);
        };
        $steps[] = function() {
            $fileName = "page1_createTables.sql";
            return $this->krok($fileName);
        };
        $steps[] = function() {
            $fileName = "page2_insertIntoMenuItem&Paper.sql";
            return $this->krok($fileName);
        };
        $steps[] = function() {
            $fileName = "page3_selectIntoAdjList.sql";
            return $this->krok($fileName);
        };
        $steps[] = function() {
                $adjList = $this->manipulator->findAllRows('menu_adjlist');
                if (is_array($adjList) AND count($adjList)) {
                    $this->log[] = "Načteno ".count($adjList)." položek z tabulky 'menu_adjlist'.";
                    $hierachy = $this->container->get(EditHierarchy::class);
                    // $hierachy->newNestedSet() založí kořenovou položku nested setu a vrací její uid
                    $rootUid = $hierachy->newNestedSet();
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
                    $this->log[] = "Skriptem pomocí Hierarchy vygenerována tabulka 'menu_nested_set' z dat tabulky 'menu_adjlist'.";
                    $this->log[] = $this->timer->interval();
                    $this->log[] = "Vykonán krok.";
                }
            return TRUE;
        };
        $steps[] = function() {
            $fileName = "page4_alterMenuItem_fk.sql";
            return $this->krok($fileName);
        };
        $steps[] = function() {
            $fileName = "page5_insertIntoMenuRoot&ComponentTable.sql";
            return $this->krok($fileName);
        };
        $steps[] = function() {
            $fileName = "page6_createNestedSetView.sql";
            return $this->krok($fileName);
        };

###### run ########
        $this->timer = new Timer();
        $this->log[] = "Záznam o konverzi ".(new \DateTime("now"))->format("d.m.Y H:i:s");
        $limit = 60;
        set_time_limit($limit);
        $this->log[] = "Nastaven časový limit běhu php skriptu na $limit sekund.";
        #### speed test ####
        $this->timer->start();

        foreach ($steps as $step) {
            if (!$step()) {
                throw new \Exception('Chybný krok. Nedokončeny všechny akce v kroku.');
            }
        }

        $report = [];
        foreach ($this->log as $value) {
            $report[] = "<pre>$value</pre>";
        }

        #### speed test výsledky jsou viditelné ve firebugu ####
//        $report[] = '<div style="display: none;">';
        $report[] = '<pre>Celkový čas: '.$this->timer->runtime().'</pre>';
//        $report[] = '</div>';
        $headers = new Headers();
        $body = new Body(fopen('php://temp', 'r+'));
        $body->write(implode(PHP_EOL, $report));
        return new Response(200, $headers, $body);
    }


    private function krok($fileName) {
        $relativePath = "src/Middleware/Konverze/Sql/";
        $fileName = $relativePath.$fileName;
        $this->manipulator->executeQuery(file_get_contents($fileName));
        $this->log[] = file_get_contents($fileName);
        $this->log[] = $this->timer->interval();
        $this->log[] = "Vykonán krok.";
        return TRUE;
    }
}
