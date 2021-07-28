<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Deployer;

use FrontControler\PresentationFrontControlerAbstract;

use Deployer\Exception\{
    DeployExceptionInterface, DeployStepFailedException, MaxExecutionTimeException
};

use Psr\Container\ContainerInterface;

use Pes\View\Template\InterpolateTemplate;
use Pes\View\Renderer\InterpolateRenderer;

use Pes\Database\Manipulator\Manipulator;
use Pes\Debug\Timer;
use Pes\Http\Headers;
use Pes\Http\Body;
use Pes\Http\Response;



/**
 * Description of Deployer
 *
 * @author pes2704
 */
abstract class DeployerAbstract implements DeployerInterface {

    const TIME_LIMIT = 100;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /** @var Manipulator  */
    protected $manipulator;

    /**
     * @var Timer
     */
    protected $timer;
    protected $log;

    /** @var InterpolateRenderer */
    private $interpolateRenderer;

    public function injectContainer(ContainerInterface $buildContainer): DeployerInterface {
        $this->container = $buildContainer;
        return $this;
    }

    protected function executeSteps($steps) {

        $this->timer = new Timer();
        #### speed test ####
        $this->timer->start();
        register_shutdown_function(function(){
            DeployerAbstract::timeout();
        });

        ### create ###
        try {
            foreach ($steps as $step) {
                $step();
                $this->log[] = "Vykonán krok. ".$this->timer->interval();
            }
        } catch (DeployExceptionInterface $de) {
            throw $de;
        }

        $this->log[] = '<pre>Celkový čas: '.$this->timer->runtime().'</pre>';

    }
    protected function createResponseFromReport() {
        $body = new Body(fopen('php://temp', 'r+'));
        $body->write($this->createReport());
        return new Response(200, new Headers(), $body);
    }

    protected function setTimeLimit() {
        $limit = self::TIME_LIMIT;
        set_time_limit($limit);
        $this->log[] = "Nastaven časový limit běhu php skriptu na $limit sekund.";
    }

    public static function timeout() {
        $errorArray = error_get_last();  // chyba již vznikla, tímto se neztratí - jen se připojí výjimka
        if (strpos($errorArray['message'], "Maximum execution time of ")===0) {
            throw new MaxExecutionTimeException("Překročen časový linit běhu skriptu ".self::TIME_LIMIT." sekund. Hodnota je dána konstantou třídy ".__CLASS__." TIME_LIMIT.");
        }
    }

    private function createReport() {
        $report = [];
        foreach ($this->log as $value) {
            $report[] = "<pre>$value</pre>";
        }
        return implode(PHP_EOL, $report);
    }
}
