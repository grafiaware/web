<?php
namespace Container;


use Site\ConfigurationCache;

// kontejner
use Pes\Container\ContainerConfiguratorAbstract;
use Psr\Container\ContainerInterface;   // pro parametr closure function(ContainerInterface $c) {}

// logger
use Pes\Logger\FileLogger;

//user
use Auth\Model\Entity\LoginAggregate;
use Auth\Model\Entity\LoginAggregateCredentialsInterface;

// database
use Pes\Database\Handler\Account;
use Pes\Database\Handler\AccountInterface;
use Pes\Database\Handler\ConnectionInfo;
use Pes\Database\Handler\DsnProvider\DsnProviderMysql;
use Pes\Database\Handler\OptionsProvider\OptionsProviderMysql;
use Pes\Database\Handler\AttributesProvider\AttributesProvider;
use Pes\Database\Handler\Handler;
use Pes\Database\Handler\HandlerInterface;

// controller
use \Red\Middleware\Redactor\Controler\UserActionControler;
use \Red\Middleware\Redactor\Controler\HierarchyControler;
use \Red\Middleware\Redactor\Controler\EditItemControler;
use Red\Middleware\Redactor\Controler\ItemActionControler;
use \Red\Middleware\Redactor\Controler\PaperControler;
use \Red\Middleware\Redactor\Controler\ArticleControler;
use Red\Middleware\Redactor\Controler\MultipageControler;
use \Red\Middleware\Redactor\Controler\SectionsControler;
use Red\Middleware\Redactor\Controler\FilesUploadControler;

use Events\Middleware\Events\Controller\{EventController, VisitorDataController};

// services
// generator service
use Red\Service\ContentGenerator\ContentGeneratorRegistry;
use Red\Service\ContentGenerator\Paper\PaperService;
use Red\Service\ContentGenerator\Article\ArticleService;
use Red\Service\ContentGenerator\StaticTemplate\StaticService;
use Red\Service\ContentGenerator\Multipage\MultipageService;
// menu itemmanipulator
use Red\Service\MenuItemxManipulator\MenuItemManipulator;

// array model
use Events\Model\Arraymodel\Event;

// events
use Events\Model\Repository\EnrollRepo;
use Events\Model\Repository\VisitorProfileRepo;
use Events\Model\Repository\VisitorJobRequestRepo;

// dao
use Red\Model\Dao\Hierarchy\HierarchyAggregateEditDao;
use Red\Model\Dao\Hierarchy\HierarchyAggregateReadonlyDao;

// repo
use Status\Model\Repository\{StatusSecurityRepo, StatusPresentationRepo, StatusFlashRepo};
use Red\Model\Repository\LanguageRepo;
use Red\Model\Repository\HierarchyJoinMenuItemRepo;
use Red\Model\Repository\MenuItemRepo;
use Red\Model\Repository\MenuItemTypeRepo;
use Red\Model\Repository\BlockRepo;
use Red\Model\Repository\MenuRootRepo;
use Red\Model\Repository\MenuItemAggregatePaperRepo;
use Red\Model\Repository\PaperAggregateContentsRepo;
use Red\Model\Repository\PaperRepo;
use Red\Model\Repository\PaperSectionRepo;
use Red\Model\Repository\ArticleRepo;
use Red\Model\Repository\MultipageRepo;
use Red\Model\Repository\ItemActionRepo;

/**
 *
 *
 * @author pes2704
 */
class EventsContainerConfigurator extends ContainerConfiguratorAbstract {

    public function getParams(): iterable {
        return ConfigurationCache::api();
    }

    public function getFactoriesDefinitions(): iterable {
        return [];
    }

    public function getAliases(): iterable {
        return [
            RouterInterface::class => Router::class,
            LoginAggregateCredentialsInterface::class => LoginAggregate::class,
            AccountInterface::class => Account::class,
        ];
    }

    public function getServicesDefinitions(): iterable {
        return [

            EventController::class => function(ContainerInterface $c) {
                return (new EventController(
                        $c->get(StatusSecurityRepo::class),
                        $c->get(StatusFlashRepo::class),
                        $c->get(StatusPresentationRepo::class),
                        $c->get(EnrollRepo::class),
                        $c->get(Event::class))
                        )->injectContainer($c);
            },
            Event::class => function(ContainerInterface $c) {
                /** @var StatusSecurityRepo $statusSecurityRepo */
                $statusSecurityRepo = $c->get(StatusSecurityRepo::class);
                return new Event($statusSecurityRepo->get());
            },

            VisitorDataController::class => function(ContainerInterface $c) {
                return (new VisitorDataController(
                        $c->get(StatusSecurityRepo::class),
                        $c->get(StatusFlashRepo::class),
                        $c->get(StatusPresentationRepo::class),
                        $c->get(VisitorProfileRepo::class),
                        $c->get(VisitorJobRequestRepo::class))
                        )->injectContainer($c);
            },

        ];
    }
}
