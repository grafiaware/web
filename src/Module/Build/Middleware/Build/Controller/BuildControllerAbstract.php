<?php
namespace Build\Middleware\Build\Controller;

use FrontControler\FrontControlerAbstract;

use Build\Middleware\Build\Exception\{
    BuildExceptionInterface, SqlExecutionStepFailedException, MaxExecutionTimeException
};

use Psr\Container\ContainerInterface;

use Pes\View\Template\InterpolateTemplate;
use Pes\View\Renderer\InterpolateRenderer;

use Pes\Database\Manipulator\Manipulator;
use Pes\Database\Statement\StatementInterface;
use Pes\Debug\Timer;
use Pes\Http\Headers;
use Pes\Http\Body;
use Pes\Http\Response;

use Exception;

/**
 * Description of InstallationControllerAbstract
 *
 * @author pes2704
 */
class BuildControllerAbstract  extends FrontControlerAbstract  implements BuildControllerInterface {

    const RELATIVE_SQLFILE_PATH = "src/Module/Build/Middleware/Build/Sql/";

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
    protected $reportMessage;

    /** @var InterpolateRenderer */
    private $interpolateRenderer;

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
                $this->reportMessage[] = "Vykonán krok. ".$this->timer->interval();
            }
        } catch (BuildExceptionInterface $ke) {
            throw $ke;
        }

        $this->reportMessage[] = '<pre>Celkový čas: '.$this->timer->runtime().'</pre>';

    }
    protected function createResponseFromReport() {
        $body = new Body(fopen('php://temp', 'r+'));
        $body->write($this->createReport());
        return new Response(200, new Headers(), $body);
    }

    protected function setTimeLimit() {
        $limit = self::TIME_LIMIT;
        set_time_limit($limit);
        $this->reportMessage[] = "Nastaven časový limit běhu php skriptu na $limit sekund.";
    }

    public static function timeout() {
        $errorArray = error_get_last();  // chyba již vznikla, tímto se neztratí - jen se připojí výjimka
        if (strpos($errorArray['message'], "Maximum execution time of ")===0) {
            throw new MaxExecutionTimeException("Překročen časový linit běhu skriptu ".self::TIME_LIMIT." sekund. Hodnota je dána konstantou třídy ".__CLASS__." TIME_LIMIT.");
        }
    }

    /**
     * Interpoluje s použitím obsahu souboru jako šablony, ve které placeholdery jodnotami z pole dat. 
     * Hodnoty v datech jse upraveny takto:
     * - null je nahrazena řetězcem NULL (SQL klíčové slovo NULL)
     * - řetězec, který začíná a končí znaky "backtick" "`" je považován za SQL identifikátor a je vložen tak, jak je (včetně backtick) (SQL identifikátor)
     * - ostatní hodnoty jsou převedeny na string a ten je vložen s přidáním apostrofů před a za řetězec (SQL string)
     * 
     * @param type $templateFilename
     * @param array $data
     * @return void
     * @throws SqlExecutionStepFailedException
     */
    protected function executeFromTemplate($templateFilename, array $data=[]): void {
        $this->reportMessage[] = "## Execute from template '$templateFilename'.";
        if (!isset($this->interpolateRenderer)) {
            $this->interpolateRenderer = new InterpolateRenderer();
        }
        $backtick = "`";
        foreach ($data as $key => $value) {
            if (!isset($value)) {
                $sqlData[$key] = 'NULL';
            } elseif ($this->startsWith($value, $backtick) AND $this->endsWith($value, $backtick)) {
                $sqlData[$key] = $value;
            } else {
                $sqlData[$key] = "'".strval($value)."'"; 
            }
        }
        $this->interpolateRenderer->setTemplate(new InterpolateTemplate(self::RELATIVE_SQLFILE_PATH.$templateFilename));
        $sql = $this->interpolateRenderer->render($sqlData);
        try {
            $this->executeFromString($sql);
        } catch (SqlExecutionStepFailedException $e) {
            $message = "Chybný krok. Nedokončeny všechny akce v kroku. Chyba nastala při vykonávání SQL příkazů v template $templateFilename.";
            $this->reportMessage[] = $message;
            $this->reportMessage[] = print_r($data, true);
            throw $e;
        }
    }
    
    private function startsWith($haystack, $needle) {
        return substr_compare($haystack, $needle, 0, strlen($needle)) === 0;
    }
    
    private function endsWith($haystack, $needle) {
        return substr_compare($haystack, $needle, -strlen($needle)) === 0;
    }
    
    protected function executeFromFile($fileName): void {
        $this->reportMessage[] = "## Execute from file '$fileName'.";
        $filePath = self::RELATIVE_SQLFILE_PATH.$fileName;
        try {
            $this->executeFromString(file_get_contents($filePath));
        } catch (SqlExecutionStepFailedException $e) {
            $message = "Chybný krok. Nedokončeny všechny akce v kroku. Chyba nastala při vykonávání SQL příkazů v souboru $filePath.";
            $this->reportMessage[] = $message;
            throw $e;
        }
    }

    protected function executeFromString($sqlString): void {
        $this->reportMessage[] = "## Execute from string '$sqlString'.";
        $this->execute($sqlString);
    }
    
    private function execute($sqlString): void {
        $this->reportMessage[] = $sqlString;
        try {
            $this->manipulator->exec($sqlString);
        } catch (\Exception $e) {
            $message = "Chybný krok. Nedokončeny všechny akce v kroku. Chyba nastala při vykonávání SQL příkazů v řetězci: ". substr($sqlString, 0, 100);
            $this->reportMessage[] = $message;
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
            $this->reportMessage[] = $message;
            $this->reportMessage[] = print_r($data, true);
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
            $this->reportMessage[] = $message;
            throw new SqlExecutionStepFailedException($message, 0, $e);
        }
        return $statement;
    }

    protected function queryFromString($sqlString): StatementInterface {
        $this->reportMessage[] = $sqlString;
        try {
            $statement = $this->manipulator->query($sqlString);
        } catch (\Exception $e) {
            $message = "Chybný krok. Nedokončeny všechny akce v kroku. Chyba nastala při vykonávání SQL příkazů v řetězci: ". substr($sqlString, 0, 100);
            $this->reportMessage[] = $message;
            throw new SqlExecutionStepFailedException($message, 0, $e);
        }
        return $statement;
    }

    private function createReport() {
        $report = [];
        foreach ($this->reportMessage as $value) {
            $report[] = "<pre>$value</pre>";
        }
        return implode(PHP_EOL, $report);
    }
}
