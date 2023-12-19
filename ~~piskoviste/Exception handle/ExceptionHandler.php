<?php

/*
 * Click https://stackoverflow.com/questions/7856173/throwing-exception-within-exception-handler/7939492#7939492
 */

// You can not re-throw from the exception handler, however, there are other places you can. For example you can de-couple the re-throw from the handler by encapsulating things into a class of it's own and then use the __destruct() function (PHP 5.3, Demo):
class ExceptionHandler
{
    private $rethrow;
    public function __construct()
    {
        set_exception_handler(array($this, 'handler'));
    }
    public function handler($exception)
    {
        echo  "cleaning up.\n";
        $this->rethrow = $exception;
    }
    public function __destruct()
    {
        if ($this->rethrow) throw $this->rethrow;
    }
}

$handler = new ExceptionHandler;

throw new Exception();

