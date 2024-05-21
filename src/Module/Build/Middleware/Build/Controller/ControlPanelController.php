<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Build\Middleware\Build\Controller;

use Psr\Http\Message\ServerRequestInterface;

use Container\BuildContainerConfigurator;

use Pes\Text\Html;

/**
 * Description of BuildControlPanel
 *
 * @author pes2704
 */
class ControlPanelController  extends BuildControllerAbstract {
    public function panel(ServerRequestInterface $request) {
        $configurator = new BuildContainerConfigurator();
        $prikazy['Vypiš konfiguraci'] = 'build/listconfig';
        $prikazy['Vytvoř novou databázi'] = 'build/createdb';
        $prikazy['Smaž databázi'] = 'build/dropdb';
        $prikazy['Vytvoř databázové uživatele'] = 'build/createusers';
        $prikazy['Smaž databázové uživatele'] = 'build/dropusers';
        $prikazy['Smaž tabulky z databáze'] = 'build/droptables';
        $prikazy['Převeď starou databázi na novou'] = 'build/convert';
        $prikazy['Naplň novou databázi základními daty'] = 'build/make';
        $prikazy['Importuj starou databázi do položky menu (do db v nové struktuře)'] = 'build/import';
        
        $html[] = "<h3>Panel</h3>";
        $html[] =  "<div>";
        foreach ($prikazy as $title => $uri) {
            $html[] = $this->form($title, $uri);
        }
        $html[] = "</div>" ;
        return $this->createResponseFromString($this->page($html));
    }
    
    private function page($formsHtmlArray) {
        return 
        Html::tag('head',[],
            Html::tag('base', ['href'=>'/web/'])
        )
        .
        Html::tag('body', [],
                $formsHtmlArray
        );
    }
    
    private function form($title, $uri) {
        return 
            Html::tag("form", ["method"=>"POST", "action"=>$uri],
                    Html::input("uri", $title, ["uri"=>$uri], ["type"=>"submit", "size"=>"8", "maxlength"=>"10", 'style'=>'margin-left: 1em;'], [])
                );
    }
}
