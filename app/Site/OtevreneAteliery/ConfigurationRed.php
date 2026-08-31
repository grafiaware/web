<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Site\OtevreneAteliery;

use Pes\Database\Handler\DbTypeEnum;
use Pes\Logger\FileLogger;

/**
 * Description of Configuration
 *
 * @author pes2704
 */
class ConfigurationRed extends ConfigurationConstants {

    public static function itemActionControler() {
        return [
            'timeout' => 'PT30M'   // 'PT1H' 1 hodina
        ];
    }

    /**
     * Konfigurace prezentace - vrací parametry pro ComponentControler
     * @return array
     */
    public static function componentControler() {

        return [
                'templates' => self::WEB_TEMPLATES_SITE,
                'static' => self::WEB_STATIC,
                'compiled' => self::WEB_STATIC.'__compiled/',
            ];
    }

    /**
     * Konfigurace - parametry pro templateControler
     * @return array
     */
    public static function redTemplates() {

        return [
            'templates.defaultExtension' => '.php',
            // pole složek, jsou prohledávány postupně při hledání souboru s šablonou zadaného typu
            'templates.folders' => [
                'author'=>[self::WEB_TEMPLATES_SITE.'red/author/', self::WEB_TEMPLATES_COMMON.'red/author/'], 
                'paper' => [self::WEB_TEMPLATES_SITE.'red/paper/', self::WEB_TEMPLATES_COMMON.'red/paper/'],
                'article' => [self::WEB_TEMPLATES_SITE.'red/article/', self::WEB_TEMPLATES_COMMON.'red/article/'],
                'multipage' => [self::WEB_TEMPLATES_SITE.'red/multipage/', self::WEB_TEMPLATES_COMMON.'red/multipage/'],
                ],
            'templates.list' => [
                'multipage' => [
                    [ 'title' => 'template multipage test', 'description' => 'popis',       'url' => 'red/v1/multipagetemplate/test'],
                    [ 'title' => 'template multipage carousel', 'description' => 'popis',       'url' => 'red/v1/multipagetemplate/carousel'],
                ],
                'article' => [
                    [ 'title' => 'Šablona pro příspěvek', 'description' => 'Jednoduchá šablona pro vložení textu a obrázku.',       'url' => 'red/v1/articletemplate/web_post'],
                    [ 'title' => 'Šablona pro popis knihy', 'description' => 'Popis knihy i autora, obrázky a důležité informace.',       'url' => 'red/v1/articletemplate/book_description'],
                    [ 'title' => 'Kousíčková šablona *', 'description' => 'Popis knihy i autora, obrázky a důležité informace.',       'url' => 'red/v1/articletemplate/book_description_Lebenhart'],
                    [ 'title' => 'Šablona pro kurz', 'description' => 'Hlavní stránka kurzu. Napište lidem základní informace i recenze od účastníků.',       'url' => 'red/v1/articletemplate/retraining_course'],
                    [ 'title' => 'Šablona pro produkt / službu', 'description' => 'Zviditelněte svůj produkt či službu.',       'url' => 'red/v1/articletemplate/product_page'],
                    [ 'title' => 'Šablona pro produkt / službu 2', 'description' => 'Zviditelněte svůj produkt či službu.',       'url' => 'red/v1/articletemplate/introduce_subject'],
                 ],
                'paper' => [
                    [ 'title' => 'template paper default', 'description' => 'popis',       'url' => 'red/v1/papertemplate/default'],
                    [ 'title' => 'template paper columns perex and contents', 'description' => 'popis',       'url' => 'red/v1/papertemplate/columns_perex_contents'],
                    [ 'title' => 'template paper columns in circle with js', 'description' => 'popis',       'url' => 'red/v1/papertemplate/contents_in_circle_js'],
                    [ 'title' => 'template paper with images', 'description' => 'popis',       'url' => 'red/v1/papertemplate/images_paper'],
                    [ 'title' => 'template paper column cards', 'description' => 'popis',       'url' => 'red/v1/papertemplate/column_cards'],
                    [ 'title' => 'template paper columns', 'description' => 'popis',       'url' => 'red/v1/papertemplate/columns'],
                    [ 'title' => 'template paper divided_rows *', 'description' => 'popis',       'url' => 'red/v1/papertemplate/divided_rows'],
                    [ 'title' => 'template paper bordered_rows *', 'description' => 'popis',       'url' => 'red/v1/papertemplate/bordered_rows'],
                    [ 'title' => 'template paper rows', 'description' => 'popis',       'url' => 'red/v1/papertemplate/rows'],
                    [ 'title' => 'template paper carousel', 'description' => 'popis',       'url' => 'red/v1/papertemplate/carouselForPaper'],
                ],
                'author' => [
                    [ 'title' => 'Profil umělce', 'description' => 'Vložení lorem ipsum', 'url' => 'red/v1/authortemplate/profil_umelce'],
                    [ 'title' => 'Profil instituce', 'description' => 'Vložení lorem ipsum', 'url' => 'red/v1/authortemplate/profil_instituce'],
                    [ 'title' => 'Profil místa', 'description' => 'Vložení lorem ipsum', 'url' => 'red/v1/authortemplate/profil_mista'],
                    [ 'title' => 'Podprofil umělec', 'description' => 'Vložení lorem ipsum', 'url' => 'red/v1/authortemplate/podprofil_umelec'],
                    [ 'title' => 'Podprofil instituce', 'description' => 'Vložení lorem ipsum', 'url' => 'red/v1/authortemplate/podprofil_instituce'],
                    [ 'title' => 'Kontakt', 'description' => 'Grafia web - kontakt',       'url' => 'red/v1/authortemplate/contact'],
                    [ 'title' => 'Publikace - novinka', 'description' => 'Grafia web - publikace',   'url' => 'red/v1/authortemplate/eshop_new'],
                    [ 'title' => 'Publikace - 2', 'description' => 'Vložení publikací na stránku', 'url' => 'red/v1/authortemplate/eshop_row'],
                    [ 'title' => 'Obrázek vlevo a text', 'description' => 'Bez obtékání. Dva sloupce', 'url' => 'red/v1/authortemplate/imgleft_block'],
                    [ 'title' => 'Obrázek vpravo a text', 'description' => 'Bez obtékání. Dva sloupce', 'url' => 'red/v1/authortemplate/imgright_block'],
                    [ 'title' => 'Blok pro citaci', 'description' => 'Bez obtékání. Dva sloupce', 'url' => 'red/v1/authortemplate/citation'],
                    [ 'title' => 'Vnitřní ohraničení bloků', 'description' => 'Bez obtékání. Dva sloupce', 'url' => 'red/v1/authortemplate/celled_blocks'],
                    [ 'title' => 'Ohraničený obsah s odkazem - 1 položka', 'description' => 'Vložení ohraničené položky na stránku. Položka obsahuje odkaz, připojte správnou adresu či odkaz odeberte', 'url' => 'red/v1/authortemplate/menu_1polozka_2'],
                    [ 'title' => 'Ohraničený obsah s odkazem - 1 položka rozdělená na sloupce', 'description' => 'Vložení ohraničené položky na stránku. Položka obsahuje odkaz, připojte správnou adresu či odkaz odeberte', 'url' => 'red/v1/authortemplate/menu_1polozka'],
                    [ 'title' => 'Ohraničený obsah s odkazem - 2 položky', 'description' => 'Vložení 2 položek na stránku. Každá položka obsahuje odkaz, připojte správnou adresu či odkaz odeberte', 'url' => 'red/v1/authortemplate/menu_2polozky'],
                    [ 'title' => 'Ohraničený obsah s odkazem - 3 položky', 'description' => 'Vložení 3 položek menu na stránku. Každá položka obsahuje odkaz, připojte správnou adresu či odkaz odeberte', 'url' => 'red/v1/authortemplate/menu_3polozky'],
                    [ 'title' => 'Upoutávka na kurz', 'description' => 'popis',       'url' => 'red/v1/authortemplate/teaser_about_item'],
                    [ 'title' => '3 stejně vysoké obrázky  v řádce', 'description' => 'popis',       'url' => 'red/v1/authortemplate/stretched_images'],
                    [ 'title' => '3 stejně vysoké obsahy v řádce', 'description' => 'popis',       'url' => 'red/v1/authortemplate/stretched_blocks'],
                    [ 'title' => 'template two columns divided', 'description' => 'popis',       'url' => 'red/v1/authortemplate/two_columns_divided'],
                    [ 'title' => 'template two blocks styled *', 'description' => 'popis',       'url' => 'red/v1/authortemplate/two_blocks_styled'],
                    [ 'title' => 'template img & text styled', 'description' => 'popis',       'url' => 'red/v1/authortemplate/img_text_styled'],
                    [ 'title' => 'template job', 'description' => 'popis',       'url' => 'red/v1/authortemplate/job'],
                    [ 'title' => 'Lorem ipsum', 'description' => 'Vložení lorem ipsum', 'url' => 'red/v1/authortemplate/lorem_ipsum'],

                ],
            ]
        ];
    }

    /**
     * Konfigurace upload files - vrací parametry pro FilesUploadControler
     * @return array
     */
    public static function redUploads() {

        return [
            'upload.red' => PES_RUNNING_ON_PRODUCTION_HOST ? self::WEB_FILES_SITE.'upload/red/' : self::WEB_FILES_SITE.'upload/red/',
            ];
    }

    /**
     * Konfigurace kontejneru - vrací parametry pro ApiContainerConfigurator
     * @return array
     */
    public static function api() {
        return [
            #################################
            # Sekce konfigurace účtů databáze pro api kontejner
            # - konfigurováni dva uživatelé - jeden pro vývoj a druhý pro běh na produkčním stroji
            #
            # Konfigurace připojení k databázi je v delegate kontejneru.
            # Konfigurace připojení k databázi může být v aplikačním kontejneru nebo různá v jednotlivých middleware kontejnerech.
            #
            'red.db.everyone.name' => PES_RUNNING_ON_PRODUCTION_HOST ? 'otevreneateli003' : 'oa_everyone',
            'red.db.everyone.password' => PES_RUNNING_ON_PRODUCTION_HOST ? 'otevreneateli003' : 'oa_everyone',
            'red.db.authenticated.name' => PES_RUNNING_ON_PRODUCTION_HOST ? 'otevreneateli003' : 'oa_auth',
            'red.db.authenticated.password' => PES_RUNNING_ON_PRODUCTION_HOST ? 'otevreneateli003' : 'oa_auth',
            'red.db.administrator.name' => PES_RUNNING_ON_PRODUCTION_HOST ? 'otevreneateli003' : 'oa_admin',
            'red.db.administrator.password' => PES_RUNNING_ON_PRODUCTION_HOST ? 'otevreneateli003' : 'oa_admin',
            #
            ###################################
            # Konfigurace logu databáze
            #
            'red.logs.view.directory' => 'Logs/Red',
            'red.logs.view.file' => 'Render.log',
            'red.logs.view.type' => FileLogger::FILE_PER_DAY
            #
            ###################################
        ];
    }

    /**
     * Konfigurace kontejneru - vrací parametry pro DbUpgradeContainerConfigurator
     * @return array
     */
    public static function dbUpgrade() {
        return [
            #####################################
            # Konfigurace připojení k databázi nového redakčního systému
            #
            # Konfigurována jsou dvě připojení k databázi - jedno pro vývoj a druhé pro běh na produkčním stroji
            #
            'red.db.type' => DbTypeEnum::MySQL,
            'red.db.port' => '3306',
            'red.db.charset' => 'utf8',
            'red.db.collation' => 'utf8_general_ci',
            'red.db.connection.host' => PES_RUNNING_ON_PRODUCTION_HOST ? '127.0.0.1' : '127.0.0.1' ,   // 'localhost' zbytečně překládá jméno,
            'red.db.connection.name' => PES_RUNNING_ON_PRODUCTION_HOST ? 'otevreneatelierycz02' : 'oa_upgrade',
            #
            ###################################
            # Konfigurace logu databáze
            #
            'red.logs.db.directory' => 'Logs/Red',
            'red.logs.db.file' => 'Database.log',
            'red.logs.db.type' => FileLogger::FILE_PER_DAY,
            #
            #################################

        ];
    }

    /**
     * Konfigurace kontejneru - vrací parametry pro HierarchyContainerConfigurator
     * @return array
     */
    public static function hierarchy() {
        return  [
            ###################################
            # Konfigurace hierarchy tabulek
            #
            'hierarchy.table' => 'hierarchy',
            'hierarchy.view' => 'hierarchy_view',
            'hierarchy.menu_item_table' => 'menu_item',
            #
            ##################################
            # konfigurace menu
            #
            'hierarchy.new_title' => 'Nová položka',
            #
            #####################################
        ];
    }

    /**
     * Konfigurace kontejneru - vrací parametry pro WebContainerConfigurator
     * @return array
     */
    public static function web() {
        return [
            #################################
            # Sekce konfigurace uživatelů databáze nového redakčního systému
            #
            # Zde je konfigurace údajů uživatele pro připojení k databázi.
            #
            # Konfigurace připojení k databázi je v delegate kontejneru.
            # Konfigurace připojení k databázi může být v aplikačním kontejneru nebo různá v jednotlivých middleware kontejnerech.
            #
            'web.db.account.everyone.name' => PES_RUNNING_ON_PRODUCTION_HOST ? 'otevreneateli003' : 'oa_everyone',
            'web.db.account.everyone.password' => PES_RUNNING_ON_PRODUCTION_HOST ? 'otevreneateli003' : 'oa_everyone',
            'web.db.account.authenticated.name' => PES_RUNNING_ON_PRODUCTION_HOST ? 'otevreneateli003' : 'oa_auth',
            'web.db.account.authenticated.password' => PES_RUNNING_ON_PRODUCTION_HOST ? 'otevreneateli003' : 'oa_auth',
            'web.db.account.administrator.name' => PES_RUNNING_ON_PRODUCTION_HOST ? 'otevreneateli003' : 'oa_admin',
            'web.db.account.administrator.password' => PES_RUNNING_ON_PRODUCTION_HOST ? 'otevreneateli003' : 'oa_admin',
            #
            ###################################
        ];
    }
}
