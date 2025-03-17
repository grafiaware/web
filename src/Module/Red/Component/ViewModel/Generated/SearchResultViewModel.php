<?php
namespace Red\Component\ViewModel\Generated;

use Component\ViewModel\ViewModelAbstract;

use Component\ViewModel\StatusViewModelInterface;
use Red\Model\Repository\MenuItemRepo;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SearchComponent
 *
 * @author pes2704
 */
class SearchResultViewModel extends ViewModelAbstract implements SearchResultViewModelInterface {

    private $status;

    private $query;

    private $menuItemRepo;

    public function __construct(
            StatusViewModelInterface $status,
            MenuItemRepo $menuItemRepo) {
        $this->status = $status;
        $this->menuItemRepo = $menuItemRepo;
    }

    /**
     * Text pro hledání metodou getSearchedMenuItems(). Info v dokumntaci getSearchedMenuItems().
     * @param string $text
     * @return $this
     */
    public function setQuery($text) {
        $this->query = $text;
        return $this;
    }

    /**
     * Vrací text nastavený pro hledání metodou getSearchedMenuItems().
     * @return string
     */
    public function getQuery() {
        return $this->query;
    }


    /**
     * Vyhledá menu items, které obsahují šlánky, ve kterých se vyskytují v polích nazev nebo obsah v zadaném jazyce slova textu nastaveného metodou setKey().
     * Hledají se jednotlivá slova v MySQL módu IN NATURAL LANGUAGE MODE.
     * Slova v zadaném textu musí být oddělená mezerou, nejkratší vyhledávané slovo má 3 znaky. Vyhledávání podporuje i operátor ""
     * (nepř. "word"), podrobnosti nutnu nastudovat v dokumentaci MySQL. Pro vahledávání se použije jen prvních 200 znaků parametru text.
     *
     * @return MenuItemInterface array of
     */
    public function getSearchedMenuItems() {
            $text = trim($this->query);
            $text = substr ($text,0,200);
            $text = strip_tags($text);
            $text = preg_replace("$".preg_quote("+|-|*|~|\"|\\|<|>|(|)")."$","",$text);
            $text = trim(implode(' ', explode(" ",$text)));

            // hledají se jednotlivá slova IN NATURAL LANGUAGE MODE
            $langCodeFk = $this->status->getPresentedLanguageCode();
            return $this->menuItemRepo->findByPaperFulltextSearch($langCodeFk, $text);
    }

    public function getString() {
        if ($this->query == '') {
            switch ($lang) {
                case 'lan2':
                    echo '<p class=chyba>You did not state the key word!</p><p>Please write the key word into the box below and press the "Search" button.</p>';
                    break;
                case 'lan3':
                    echo '<p class=chyba>You did not state the key word!</p><p>Please write the key word into the box below and press the "Search" button.</p>';
                    break;
                case 'lan1':
                default:
                    echo '<p class=chyba>Neuvedli jste slovo, podle kterého chcete vyhledávat!</p><p>Do kolonky níže napište co chcete vyhledat a stiskněte tlačítko "Vyhledat".</p>';
                    break;
            }
        } else {

            $n = count($papers);
            if ( $n== 0) {
                switch ($lang) {
                    case 'lan2':
                        echo "<p>There were no results matching the query. </p>";
                        break;
                    case 'lan3':
                        echo "<p>There were no results matching the query. </p>";
                        break;
                    case 'lan1':
                    default:
                        echo "<p>Nebyly nalezeny žádné záznamy obsahující: <b>$this->query</b></p>";
                        break;
                }
            } else {
                switch ($lang) {
                    case 'lan2':
                        echo '<p>There were found '.$n.' results.</p>';
                        break;
                    case 'lan3':
                        echo '<p>There were found '.$n.' results.</p>';
                        break;
                    case 'lan1':
                    default:
                        echo '<p>Bylo nalezeno '.$n.' záznamů.</p>';
                        break;
                }
                $n = 1;
                foreach($papers as $paper) {
                    echo '<p>'.$n++.'. <a href="index.php?list='.$paper->getHierarchyAggregate().'&language='.$this->lang.'">'.$paper->getPaper().'</a></p>';
                }
            }

        }
    }

    public function getIterator(): \Traversable {
        $this->appendData(
                ['searchedMenuItems' => $this->getSearchedMenuItems()]
                );
        return parent::getIterator();
    }
}
