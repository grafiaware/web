<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace StatusModel;

use Model\Repository\StatusSecurityRepo;
use Model\Entity\StatusSecurity;
use Model\Entity\StatusSecurityInterface;

use Model\Entity\UserInterface;
use Pes\Database\Handler\AccountInterface;
use Pes\Database\Handler\Handler;
/**
 * Description of SecurityStatusModel
 *
 * @author pes2704
 */
class StatusSecurityModel implements StatusSecurityModelInterface {

    /**
     *
     * @var StatusSecurityRepo
     */
    protected $securityStatusRepo;

    /**
     * @var StatusSecurityInterface
     */
    protected $securityStatus;

    private $SecurityContextObjects = [];

    /**
     * Základní funkcí modelu je regenerece security statusu. K tomu potřebuje objekty vytvářené v kontejneru v závislosti na bezpečnostním kontextu.
     *
     * @param StatusSecurityRepo $securityStatusRepo
     * @param \StatusModel\UserInterface $user
     * @param \StatusModel\AccountInterface $account
     * @param \StatusModel\Handler $handler
     */
    public function __construct(
            StatusSecurityRepo $securityStatusRepo, UserInterface $user=null, AccountInterface $account=null, Handler $handler=null
            ) {
        $this->securityStatusRepo = $securityStatusRepo;
        $this->securityStatus = $this->securityStatusRepo->get();
        $this->SecurityContextObjects[] = $user;
        $this->SecurityContextObjects[] = $account;
        $this->SecurityContextObjects[] = $handler;
    }

    /**
     * {@inheritdoc}
     *
     * Metoda smaže objekty UserInterface, AccountInterface a Handler předané jako instanční proměnné při volání konstruktoru.
     * Předpokládá, že tyto proměnné byly do konstruktoru injektovány v kontejneru, pak tím dojde i kesmazání objektů v instancích kontejneru
     * a kontejner při dalším volání služeb automatocky vytvoří tyto objekty nové.
     *
     * @return void
     */
    public function regenerateSecurityStatus(): void {
        // odstraň z kontejneru objekty vzniklé s použitím bezpečnostního kontextu - kontejner vytvoří nové z nového kontextu
        // nový hadler s novým User, Account
        foreach ($this->SecurityContextObjects as $contextObject) {
            unset($contextObject);
        }
        $this->securityStatus = new StatusSecurity();
        $this->securityStatusRepo->add($this->securityStatus);
    }

    /**
     *
     * @return StatusSecurityInterface
     */
    public function getStatusSecurity(): StatusSecurityInterface {
        if (!isset($this->securityStatus)) {
            $this->regenerateSecurityStatus();
        }
        return $this->securityStatus;
    }

}
