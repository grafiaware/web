<?php
namespace Middleware\Edun;
use Pes\Middleware\AppMiddlewareAbstract;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use View\Includer;

use Pes\Http\Request\RequestParams;
use Pes\Http\Factory\ResponseFactory;

class Edun extends AppMiddlewareAbstract implements MiddlewareInterface {
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
        $requestParams = new RequestParams();
        $list = $requestParams->getQueryParam($request, 'list', 's');
        $order = $requestParams->getQueryParam($request, 'order', '');
        $lang = $requestParams->getQueryParam($request, 'lang', $requestParams->getQueryParam($request, 'language', 'lan1'));

        /* @var $sessionHandler \Pes\Session\SessionStatusHandlerInterface */
        $sessionHandler = $this->getMwContainer()->get(\Pes\Session\SessionStatusHandlerInterface::class);
        $loggedUser = $sessionHandler->get('auth.user');

        $response = (new ResponseFactory())->createResponse();
        $output = (new Includer())->protectedIncludeScope('Middleware/Edun/index.php',
                [
                    'mwContainer'=> $this->getMwContainer(), 'request' => $request,
                    'loggedUser'=>$loggedUser, 'list'=>$list, 'order'=>$order, 'lang'=>$lang,
                ]);
        $size = $response->getBody()->write($output);
        $response->getBody()->rewind();
        return $response;
    }
}

