<?php
namespace Events\Middleware\Events\Controler;

use FrontControler\FrontControlerAbstract;

use Access\Enum\RoleEnum;

use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusFlashRepo;
use Status\Model\Repository\StatusPresentationRepo;

use Events\Model\Repository\LoginRepoInterface;
use Events\Model\Entity\Login;

use Pes\Logger\FileLogger;

/**
 * Description of UpdateEventsControler
 *
 * @author pes2704
 */
class LoginSyncControler extends FrontControlerAbstract {

    private $loginRepo;
    private $fileLogger;

    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusFlashRepo $statusFlashRepo,
            StatusPresentationRepo $statusPresentationRepo,
            LoginRepoInterface $loginRepo,
            FileLogger $fileLogger
            ) {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo);
        $this->loginRepo = $loginRepo;
        $this->fileLogger = $fileLogger;
    }

    private function getValidLoginName(): ?string {

    }
    
    /**
     * Metoda přidá do tabulky login v db events jméno příhlášeného uživatele, pokud tam ještě není. Tím zajišťuje synchronizaci tabulky login v db events s tabulkou login v db auth.
     * 
     * - Přidává jen login name uživatele, který má jednu z vybraných rolí pro modul Event - viz metoda getValidLoginName
     * - Metoda jen přidává, neodstraňuje žádné záznamy
     * 
     * Pokud dojde k odstranění login name z db auth, v db events login name zůstane - mazání by musel zajišťovat "garbage collection" middleware s přístupen k db auth i db events
     */
    public function actualizeLogin() {
        $loginAgg = $this->statusSecurityRepo->get()->getLoginAggregate();
        if (isset($loginAgg)) {
            $role = $loginAgg->getCredentials()->getRoleFk();
        }
        if(isset($role) && ($role==RoleEnum::VISITOR || $role==RoleEnum::REPRESENTATIVE || $role==RoleEnum::EVENTS_ADMINISTRATOR)) {
            $loginName = $loginAgg->getLoginName();
        }
        if (isset($loginName)) {
            $login = $this->loginRepo->get($loginName);
            if (!isset($login)) {
                $login = new Login();
                $login->setLoginName($loginName);
                $this->loginRepo->add($login);  // není třeba flush - repo se předá v app kontejneru
                $this->fileLogger->info("{loginname} | Přidán: {loginname}, role {role}", ["loginname"=>$loginName, "role"=>$role]);
            }
        }
    }
}
