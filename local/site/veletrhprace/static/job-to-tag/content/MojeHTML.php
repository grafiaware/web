<?php
//namespace Pes\Text;
//use Pes\Text\Html;

/**
 * Description of mojeHTML
 *
 * @author vlse2610
 */
class MojeHTML {
    
     public static function checkbox(iterable $checkboxsetLabelsNameValuePairs=[], array $context=[]) {
        $attributes["type"] = "checkbox";
        foreach ($checkboxsetLabelsNameValuePairs as $label => $nameValuePair) {
            $name = key($nameValuePair);
            $value = $nameValuePair[$name];
            $checkedValue = array_key_exists($name, $context) ? $context[$name] : null;
            $attributes["id"] = uniqid();
            $attributes["name"] = $name;
            $attributes["value"] = $value;
            $attributes["checked"] = ($checkedValue==$value) ;
            $html[] = MojeHTML::tagMoje('span', [],
                    MojeHTML::tagNopairMoje("input", $attributes)
                    );
            $html[] = MojeHTML::tagMoje("label", ["for"=>$attributes["id"]], $label);
        }
        return implode(PHP_EOL, $html);
    }
    
    
     /**
     * Generuje html kód nepárového tagu.
     *
     * @param string $name Jméno tagu. Bude použito bez změny malách a velkých písmen
     * @param iterable $attributes Asociativní pole. Viz metoda attributes().
     * @return string
     */
     private static function tagNopairMoje($name, iterable $attributes=[]) {
        if ($name) {
            $attr = self::attributes($attributes);
            $html = "<$name".($attr ? " $attr" : '')." />";
        }
        return $html ?? '';
    }
    
    
     /**
     * Generuje html kód párového tagu.
     *
     * @param string $name Jméno tagu. Bude použito bez změny malách a velkých písmen
     * @param iterable $attributes Atributy - iterable proměnná s dvojicemi key=>value
     * @param variadic $innerTag Sada proměnných, které budou vloženy jako textový obsah tagu, jednotlivé proměnné mohou být typu string nebo pole
     * @return string
     */
     private static function tagMoje($name, iterable $attributes=[], ...$innerTag) {
        $html = [];
        if ($name) {
            $attr = self::attributes($attributes);
            if ($innerTag) {
                $innerHtml = MojeHtml::implodeInnerTagsMoje($innerTag);
                $ret = "<$name".($attr ? " $attr" : '').">".$innerHtml."</$name>";
            } else {
                $ret = "<$name".($attr ? " $attr" : '')."></$name>";
            }
        }
        return $ret ?? '';
    }

    
    
    /**
     *
     * @param array $innerTag
     * @return type
     */
    private static function implodeInnerTagsMoje(array $innerTag) {
        $innerHtml = [];
        foreach ($innerTag as $value) {
            if (is_array($value)) {
                if (count($value)>0) {
                    $innerHtml[] = MojeHTML::implodeInnerTagsMoje($value);
                } else {
                    $innerHtml[] = "";
                }
            } else {
                $innerHtml[] = (string) $value;
            }
        }
        if (count($innerHtml)>1) {
            $impl = self::EOL.implode(self::EOL, $innerHtml).self::EOL;
        } else {
            $impl = $innerHtml[0];
        }
        return $impl;
    }

   
      /**
     * Metoda generuje textovou reprezentaci atributů html tagu z dat zadaných jako iterable proměnnou s dvojicemi key=>value.
     *
     * Jednotlivé hodnoty atrubutů vždy uzavírá do uvozovek. Tím se zmenšuje riziko XSS útoku kódem v atributech. Přesto nevkládejte uživatelský vstup do hodnot atributů.
     *
     * Podle typu hodnoty atributu:
     * <ul>
     * <li>Pro atributy s hodnotou typu boolean s hodnotou true generuje jen jméno parametru (standard html nikoli xml), s hodnotou false negeneruje nic.</li>
     * <li>Pro atributy s hodnotou typu array generuje dvojici jméno="řetězec hodnot oddělených mezerou", řetězec hodnot vytvoří zřetězením hodnot v poli oddělených mezerou a obalí uvozovkami</li>
     * <li>Ostatní atributy jako dvojici jméno="hodnota" s tím, že hodnotu prvku převede na string a obalí uvozovkami.</li>
     * </ul>
     * Pokud je hodnota atributu řetězec, který obsahuje uvozovky, výsledné html bude chybné. Hodnota atributu je vždy obalena uvozovkami.
     * Výsledný navrácený řetězec začíná mezerou a atributy v řetězci jsou odděleny mezerami.
     *
     * Příklad:
     * ['id'=>'alert', 'class'=>['ui', 'item', 'alert'], 'readonly'=>TRUE, data-info=>'a neni b'] převede na: id="alert" class="ui item alert" readonly data-info="a neni b".
     * Víceslovné řetězce (typicky class) lze tedy zadávat jako pole nebo víceslovný řetězec.
     *
     * @param iterable $attributes Atributy - iterable proměnná s dvojicemi key=>value
     * @return string
     */
    public static function attributes(iterable $attributes=[]) {
        foreach ($attributes as $type => $value) {
            if (is_bool($value)) {
                if ($value) {
                    $attr[] = $type;  // jen pro true
                }
            } elseif (is_array($value)) {
                $attr[] = $type.'="'.implode(' ', $value).'"';
            } else {
                $attr[] = $type.'="'.trim((string) $value).'"';
            }
        }
        return isset($attr) ? implode(' ',$attr) : '';
    }
  
    
    
}
