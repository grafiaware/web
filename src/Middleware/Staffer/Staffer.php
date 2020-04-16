<?php
namespace Middleware\Staffer;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Pes\Http\Factory\ResponseFactory;

class Staffer implements MiddlewareInterface {
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
        $response = (new ResponseFactory())->createResponse();
        $output = $this->protectedIncludeScope('Middleware/Staffer/index.php', array('request' => $request));
        $size = $response->getBody()->write($output);
        $response->getBody()->rewind();
        return $response;
    }

    private function protectedIncludeScope($includeFilePath, array $data) {

        extract($data);

        try {
            $level = ob_get_level();
            ob_start();
            include $includeFilePath;
            $result = ob_get_clean();
        } catch (Throwable $e) {
            while (ob_get_level() > $level) {
                ob_end_clean();
            }
            throw $e;
        } catch (Exception $e) {
            while (ob_get_level() > $level) {
                ob_end_clean();
            }
            throw $e;
        }
        return $result;
    }

}

