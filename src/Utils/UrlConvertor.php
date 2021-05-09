<?php

namespace Utils;

/**
 * Description of UrlConvertor
 *
 * @author pes2704
 */
class UrlConvertor {

public function getUrlSanitizeFunction() {
    return function($url) {
            $url = preg_replace('~[^\\pL0-9_]+~u', '-', $url);
            $url = trim($url, "-");
            $url = iconv("utf-8", "us-ascii//TRANSLIT", $url);
            $url = strtolower($url);
            $url = preg_replace('~[^-a-z0-9_]+~', '', $url);
            return $url;
        };
}

public function sanitize($url) {
    $url = preg_replace('~[^\\pL0-9_]+~u', '-', $url);
    $url = trim($url, "-");
    $url = iconv("utf-8", "us-ascii//TRANSLIT", $url);
    $url = strtolower($url);
    $url = preg_replace('~[^-a-z0-9_]+~', '', $url);
    return $url;
}

}
