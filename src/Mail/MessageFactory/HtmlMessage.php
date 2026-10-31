<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Mail\MessageFactory;

use Pes\View\ViewFactory;
use Pes\View\Renderer\PhpTemplateRenderer;

use Mail\MessageFactory\ContextExceptionInterface;
use Mail\MessageFactory\ContextException;

/**
 * Description of Html
 *
 * @author pes2704
 */
class HtmlMessage {
    public function create(string $templateFilePath, array $context): string {
       //kontrola contextu
        //if ( $this->obsahujePrazdneHodnoty($context) ){ 
        $res = $this->najdiNevalidniHodnoty($context);
        if ( !(empty($res)) ) { 
            throw new ContextException("Mail,MessageFactory: v context jsou polozky obsahujici prazdne retezce  nebo hodnoty null.". $res);             
        }
        
        return (string) (new ViewFactory())->phpTemplateView($templateFilePath, $context)->setRenderer(new PhpTemplateRenderer());
    }

    
    private function obsahujePrazdneHodnoty(array $context): bool {
        foreach ($context as $hodnota) {
            // Pokud narazíme na vnořené pole, prohledáme ho rekurzivně
            if (is_array($hodnota)) {
                if (obsahujePrazdneHodnoty($hodnota)) {
                    return true;
                }
            } 
            // Kontrola na null
            elseif ($hodnota === null) {
                return true;
            }
            // Kontrola na prázdný řetězec nebo řetězec plný mezer
            // is_string zaručí, že trim() voláme pouze na text (ne na čísla či booleany)
            elseif (is_string($hodnota) && trim($hodnota) === "") {
                return true;
            }
        }
        return false;
    }
    
    
    
    
    
    private function najdiNevalidniHodnoty(array $pole): string {
        $chyby = $this->sesbirejNevalidniHodnoty($pole);
        if (empty($chyby)) {
            return "";
        }
        $text = "\nNalezeny nevalidní hodnoty:\n";
        foreach ($chyby as $chyba) {
            $text .= "- Na pozici '{$chyba['cesta']}' je hodnota typu [{$chyba['typ']}]\n";
        }
        return $text;
    }

    /**
     * Rekurzivně sesbírá položky s hodnotou null nebo prázdným řetězcem (i ve vnořených polích).
     *
     * @return list<array{cesta: string, typ: string}>
     */
    private function sesbirejNevalidniHodnoty(array $pole, string $aktualniCesta = ""): array {
        $chyby = [];
        foreach ($pole as $klic => $hodnota) {
            // Sestavení cesty ke klíči (např. uzivatel.kontakty.email)
            $novaCesta = $aktualniCesta === "" ? (string) $klic : $aktualniCesta . "." . $klic;
            if (is_array($hodnota)) {
                $chyby = array_merge($chyby, $this->sesbirejNevalidniHodnoty($hodnota, $novaCesta));
            } elseif ($hodnota === null) {
                $chyby[] = ["cesta" => $novaCesta, "typ" => "null"];
            } elseif (is_string($hodnota) && trim($hodnota) === "") {
                $chyby[] = ["cesta" => $novaCesta, "typ" => "prazdny_retezec"];
            }
        }
        return $chyby;
    }

    
    
}
