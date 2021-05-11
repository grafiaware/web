<?php
namespace Build\Middleware\Build\Controller;

use FrontController\PresentationFrontControllerAbstract;

use Build\Middleware\Build\Exception\{
    BuildExceptionInterface, SqlExecutionStepFailedException, MaxExecutionTimeException
};

use Psr\Container\ContainerInterface;

use Pes\View\Template\InterpolateTemplate;
use Pes\View\Renderer\InterpolateRenderer;

use Pes\Database\Manipulator\Manipulator;
use \Pes\Database\Statement\StatementInterface;
use Pes\Debug\Timer;
use Pes\Http\Headers;
use Pes\Http\Body;
use Pes\Http\Response;


/**
 * Description of InstallationControllerAbstract
 *
 * @author pes2704
 */
class BuildControllerAbstract  extends PresentationFrontControllerAbstract  implements BuildControllerInterface {

    const RELATIVE_SQLFILE_PATH = "src/Middleware/Build/Sql/";

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

    public function injectContainer(ContainerInterface $buildContainer): BuildControllerInterface {
        $this->container = $buildContainer;
        return $this;
    }

    protected function executeSteps($steps) {

        $this->timer = new Timer();
        #### speed test ####
        $this->timer->start();
        register_shutdown_function(function(){
            BuildControllerAbstract::timeout();
        });

        ### create ###
        try {
            foreach ($steps as $step) {
                $step();
                $this->log[] = "Vykonán krok. ".$this->timer->interval();
            }
        } catch (BuildExceptionInterface $ke) {
            throw $ke;
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

    protected function executeFromTemplate($templateFilename, array $data=[]): void {
        if (!isset($this->interpolateRenderer)) {
            $this->interpolateRenderer = new InterpolateRenderer();
        }
        $this->interpolateRenderer->setTemplate(new InterpolateTemplate(self::RELATIVE_SQLFILE_PATH.$templateFilename));
        $sql = $this->interpolateRenderer->render($data);
        try {
            $this->executeFromString($sql);
        } catch (SqlExecutionStepFailedException $e) {
            $message = "Chybný krok. Nedokončeny všechny akce v kroku. Chyba nastala při vykonávání SQL příkazů v template $templateFilename.";
            $this->log[] = $message;
            $this->log[] = print_r($data, true);
            throw new SqlExecutionStepFailedException($message, 0, $e);
        }
    }

    protected function executeFromFile($fileName): void {
        $fileName = self::RELATIVE_SQLFILE_PATH.$fileName;
        try {
            $this->executeFromString(file_get_contents($fileName));
        } catch (SqlExecutionStepFailedException $e) {
            $message = "Chybný krok. Nedokončeny všechny akce v kroku. Chyba nastala při vykonávání SQL příkazů v souboru $fileName.";
            $this->log[] = $message;
            throw new SqlExecutionStepFailedException($message, 0, $e);
        }
    }

    protected function executeFromString($sqlString): void {
        $this->log[] = $sqlString;
        try {
            $this->manipulator->exec($sqlString);
        } catch (\Exception $e) {
            $message = "Chybný krok. Nedokončeny všechny akce v kroku. Chyba nastala při vykonávání SQL příkazů v řetězci: ". substr($sqlString, 0, 100);
            $this->log[] = $message;
            throw new SqlExecutionStepFailedException($message, 0, $e);
        }
    }

    protected function queryFromTemplate($templateFilename, array $data=[]): StatementInterface {
        if (!isset($this->interpolateRenderer)) {
            $this->interpolateRenderer = new InterpolateRenderer();
        }
        $this->interpolateRenderer->setTemplate(new InterpolateTemplate(self::RELATIVE_SQLFILE_PATH.$templateFilename));
        $sql = $this->interpolateRenderer->render($data);
        try {
            $statement = $this->queryFromString($sql);
        } catch (SqlExecutionStepFailedException $e) {
            $message = "Chybný krok. Nedokončeny všechny akce v kroku. Chyba nastala při vykonávání SQL příkazů v template $templateFilename.";
            $this->log[] = $message;
            $this->log[] = print_r($data, true);
            throw new SqlExecutionStepFailedException($message, 0, $e);
        }
        return $statement;
    }

    protected function queryFromFile($fileName): StatementInterface {
        $fileName = self::RELATIVE_SQLFILE_PATH.$fileName;
        try {
            $statement = $this->queryFromString(file_get_contents($fileName));
        } catch (SqlExecutionStepFailedException $e) {
            $message = "Chybný krok. Nedokončeny všechny akce v kroku. Chyba nastala při vykonávání SQL příkazů v souboru $fileName.";
            $this->log[] = $message;
            throw new SqlExecutionStepFailedException($message, 0, $e);
        }
        return $statement;
    }

    protected function queryFromString($sqlString): StatementInterface {
        $this->log[] = $sqlString;
        try {
            $statement = $this->manipulator->query($sqlString);
        } catch (\Exception $e) {
            $message = "Chybný krok. Nedokončeny všechny akce v kroku. Chyba nastala při vykonávání SQL příkazů v řetězci: ". substr($sqlString, 0, 100);
            $this->log[] = $message;
            throw new SqlExecutionStepFailedException($message, 0, $e);
        }
        return $statement;
    }

    private function createReport() {
        $report = [];
        foreach ($this->log as $value) {
            $report[] = "<pre>$value</pre>";
        }
        return implode(PHP_EOL, $report);
    }
}
