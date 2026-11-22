<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Site\NajdiSi;

use Pes\Database\Handler\DbTypeEnum;

/**
 * Description of ConfigurationEvents
 *
 * @author pes2704
 */
class ConfigurationEvents extends ConfigurationConstants {

    // local
    const EVENTS_TEMPLATES_COMMON = 'local/site/common/templates/events/';
    const EVENTS_TEMPLATES_SITE = 'local/site/'.self::WEB_SITE.'templates/events/';

    /**
     * D1 (#46): losování – platnost losu na místě, akce prodlužující platnost, stanoviště skeneru.
     * Popis: docs/vp/zadani/D1.md (Konfigurace). Funkci zapíná přepínač vp.losovani (ConfigurationFeatures).
     */
    public static function lottery() {
        return [
            'timezone' => 'Europe/Prague',      // časy kol a losů (DATETIME v DB) jsou v této zóně
            'oneWinAcrossLotteries' => false,
            'presence' => [
                'issueMinMinutes' => 30,   // los hraje nejdřív v kole za 30 min od vydání
                'issueRounds' => 2,        // a nejvýš ve 2 kolech
                // akce prodlužující platnost: klíč => minuty (platnost do času akce + minuty);
                // klíč, který tu není, platnost neprodlouží (akce se jen zaznamená)
                'refresh' => [
                    'vchod' => 60,         // opakovaný sken u vchodu při platném losu
                    'infostanek' => 60,
                    'rucne' => 60,         // ruční prodloužení obsluhou (vyhledání podle jména)
                    // 'stanek' => 60,     // sken u stánku (D2)
                ],
            ],
            'stations' => [               // stanoviště skeneru: klíč => [název, vydává los, akce prodloužení]
                'vchod' => ['label' => 'Vchod', 'issue' => true, 'refresh' => 'vchod'],
                'infostanek' => ['label' => 'Infostánek', 'issue' => false, 'refresh' => 'infostanek'],
            ],
            'scan' => ['maxOfflineMinutes' => 240],   // starší sken z fronty offline server odmítne
            'mail' => ['sender' => 'info'],           // odesílatel e-mailu po zapojení (MailSettings::party)
            'paper' => ['perPage' => 4],              // D1c: papírových lístků na stránku A4 při tisku (4 nebo 2)
        ];
    }

    /**
     * R1 (#48): rezervace míst na přednáškách pro školy. Popis: docs/vp/zadani/R1.md (Konfigurace).
     * Funkci zapíná přepínač vp.rezervace (ConfigurationFeatures). Upozornění organizátorovi jdou na skupinu
     * příjemců 'reservation.notify' v ConfigurationWeb::mail() (mail.recipients).
     */
    public static function reservation() {
        return [
            'timezone' => 'Europe/Prague',
            'maxPerEmail' => 5,            // aktivních rezervací z jednoho e-mailu v nabídce
            'maxPupils' => 200,            // nejvýš žáků v jedné rezervaci
            'maxTeachers' => 20,
            'changeDaysBefore' => 2,       // výchozí uzávěrka změn při založení nabídky: 2 dny před akcí
            // NUTNO NAHRADIT po právní konzultaci (issue #140): texty souhlasů ve formuláři a odkaz na zásady (A4).
            // Dokud text obsahuje „Nutno nahradit“, formulář ho zobrazí s upozorněním; vp.rezervace na produkci nezapínat.
            'consentText' => '[Nutno nahradit] Souhlasím se zpracováním uvedených údajů za účelem rezervace a organizace '
                . 'přednášky na Veletrhu práce a vzdělávání.',
            'phoneConsentText' => '[Nutno nahradit] Souhlasím, aby mi organizátor v den akce krátce před začátkem přednášky '
                . 'zavolal a ověřil příchod skupiny. Telefon se použije jen k tomu a po skončení dne akce se smaže.',
            'policyUrl' => '',             // [Nutno nahradit] odkaz na zásady zpracování osobních údajů
            // kontakt na organizátora (po uzávěrce změn) – adresu doplní Petr
            'organizerContact' => PES_RUNNING_ON_PRODUCTION_HOST ? 'rezervace.skoly@mail' : 'rezervace@najdisi.test',
            // odesílatel 'rezervace' a příjemci 'reservation.notify' jsou v ConfigurationWeb::mail()
            'mail' => ['sender' => 'rezervace', 'notifyGroup' => 'reservation.notify'],
        ];
    }

    public static function dbEvents() {

        return [
            #####################################
            # Konfigurace připojení k databázi Events
            #
            # Konfigurována jsou dvě připojení k databázi - jedno pro vývoj a druhé pro běh na produkčním stroji
            #
            'dbEvents.db.type' => DbTypeEnum::MySQL,
            'dbEvents.db.port' => '3306',
            'dbEvents.db.charset' => 'utf8',
            'dbEvents.db.collation' => 'utf8_general_ci',
            'dbEvents.db.connection.host' => PES_RUNNING_ON_PRODUCTION_HOST ? '127.0.0.1' : '127.0.0.1',            
            'dbEvents.db.connection.name' => PES_RUNNING_ON_PRODUCTION_HOST ? 'najdisicz03' : 'events',
            #
            ###################################
            # Konfigurace logu databáze
            #
            'dbEvents.logs.db.directory' => 'Logs/Events',
            'dbEvents.logs.db.file' => 'Database.log',
            'dbEvents.logs.db.validateuser' => 'ValidateUser.log',
            #
            #################################
            ];
    }  
    
    /**
     * Konfigurace prezentace - vrací parametry pro ComponentControler
     * @return array
     */
    public static function eventTemplates() {

        return [
                'templates' => self::EVENTS_TEMPLATES_SITE,
            ];
    }    
    

    /**
     * Konfigurace upload files - vrací parametry pro FilesUploadControler a VisitorProfileControler
     * @return array
     */
    public static function eventsUploads() {

        return [
            'upload.events.visitor' => PES_RUNNING_ON_PRODUCTION_HOST ? self::WEB_FILES_SITE.'upload/events/visitor' : self::WEB_FILES_SITE.'upload/events/visitor',
            'upload.events.acceptedextensions' => [".doc", ".docx", ".dot", ".odt", ".pages", ".xls", ".xlsx", ".ods", ".txt", ".pdf"],
            ];
    }    
    
}
