<?php
namespace Middleware\Rs;

use Pes\Middleware\AppMiddlewareAbstract;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Pes\Http\Factory\ResponseFactory;
use \Pes\Container\Container;
use View\Includer;

use Container\HierarchyContainerConfigurator;
use Container\WebContainerConfigurator;


class Rs extends AppMiddlewareAbstract implements MiddlewareInterface {
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
        $response = (new ResponseFactory())->createResponse();

        // middleware kontejner:
        //      nový kontejner konfigurovaný ComponentContainerConfigurator a MenuContainerConfigurator
        //      -> delagát další nový kontejner konfigurovaný WebContainerConfigurator
        //      -> v něm jako delegát aplikační kontejner
        // komponenty používají databázi z menu kontejneru (upgrade), web používá starou databázi
        // do aplikačního kontejneru je přidán request (pro užití v kontroléru)
        $rsContainer =
            (new WebContainerConfigurator())->configure(
                        new Container(
                            (new WebContainerConfigurator())->configure(
                                    new Container($this->getApp()->getAppContainer())
                            )
                        )
            );

        $output = (new Includer())->protectedIncludeScope('Middleware/Rs/index.php', array('mwContainer'=> $rsContainer, 'request' => $request));
        $size = $response->getBody()->write($output);
        $response->getBody()->rewind();
        return $response;
    }
}

