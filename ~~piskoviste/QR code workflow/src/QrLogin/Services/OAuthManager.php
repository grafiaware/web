<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace QrLogin\Services;

use League\OAuth2\Client\Provider\Google;
use League\OAuth2\Client\Provider\Github;
use League\OAuth2\Client\Provider\Facebook;
use Stevenmaguire\OAuth2\Client\Provider\Microsoft;

/**
 * Description of OAuthManager
 *
 * @author pes2704
 */
class OAuthManager
{
    private $providers = [];  // private array $providers = [];

    public function __construct(array $cfg) {       //  (private array $cfg)
        $this->providers['google'] = function($cfg) {return new Google($cfg['oauth']['google']);}; // fn() => new Google($cfg['oauth']['google']);
        $this->providers['github'] = function($cfg) {return new Github($cfg['oauth']['github']);};// fn() => new Github($cfg['oauth']['github']);
        $this->providers['facebook'] = function($cfg) {return new Facebook($cfg['oauth']['facebook']);};// fn() => new Facebook($cfg['oauth']['facebook']);
        $this->providers['microsoft'] = function($cfg) {return new Microsoft($cfg['oauth']['microsoft']);};// fn() => new Microsoft($cfg['oauth']['microsoft']);
    }

    public function provider(string $name) {
        if (!isset($this->providers[$name])) {
            throw new \InvalidArgumentException('Unknown provider: ' . $name);
        }
        return ($this->providers[$name])();
    }
}