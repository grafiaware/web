<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Events\Model\Context;

use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusPresentationRepo;
use Pes\Model\Context\ContextProviderInterface;
use Access\Enum\RoleEnum;

/**
 * Description of ContextFactory
 *
 * @author pes2704
 */
class ContextProvider implements ContextProviderInterface {

    private ?bool $forceShowOnlyPublished = null;

    /**
     * @param StatusSecurityRepo|null $statusSecurityRepo bez něj vždy jen zveřejněné (dosavadní chování)
     */
    public function __construct(
            private ?StatusSecurityRepo $statusSecurityRepo = null
            ) {
    }

    /**
     * Jen pokud víte co děláte!
     * Metoda slouží pro změnu nastavení, které contect provider provádí pomocí parametrů konstruktoru.
     *
     * Touto metodou lze vynutit pevné nastavení, je jen třeba dbát na to, aby nastavení proběhlo před prvním čtením.
     *
     * POZOR! Použití této metody může vést ke komplikovaným důsledkům. Například pokud se ContextProvider používá v kontejneru, pak je s největší pravděpodobností použit
     * v různých Dao a Repository a změna jeho nastavé ovlivní všechny. Navíc až od okamžiku nastavení.
     * Lze to snad eliminovat voláním metody s bool hodnotou, provést operaci s jedním Dao (Repo - pozor na agregátní repo) a ihned zavolat metodu s hodnotou parametru null.
     * Zavolánim metody s hodnotou null se vrátí chování objektu k defaultnímu.
     *
     * @param bool $forceShowOnlyPublished
     */
    public function forceShowOnlyPublished($forceShowOnlyPublished=null): void {
        $this->forceShowOnlyPublished = isset($forceShowOnlyPublished) ? (bool) $forceShowOnlyPublished : null;
    }

    /**
     * G0 (test 27. 11.): administrátor programu (role events_administrator) pracuje i s nezveřejněnými eventy –
     * ve správě vp/event je vidí, upraví, zveřejní a smaže. Ostatní (i nepřihlášení) jen zveřejněné.
     * Veřejný program (ProgramViewModel) a přihlášení na event (EventControler::enroll) kontrolují zveřejnění samy,
     * administrátorovi se tedy v programu nezveřejněné eventy nezobrazí.
     */
    public function showOnlyPublished(): bool {
        if (isset($this->forceShowOnlyPublished)) {
            return $this->forceShowOnlyPublished;
        }
        $role = $this->statusSecurityRepo?->get()?->getLoginAggregate()?->getCredentials()?->getRoleFk();
        return $role !== RoleEnum::EVENTS_ADMINISTRATOR;
    }
}
