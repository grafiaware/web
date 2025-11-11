<?php

/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */


/**
 * Registry sleduje všechny vytvořené objekty.
 * Na konci běhu skriptu zavolá zvolenou metodu (publicMethod()) u všech instancí, které ji mají. 
 * Pokud třída metodu nemá, nic se nestane.
 */
class DependencyTracker {
    private static $instances = [];

    public static function register(object $instance, array $args) {
        self::$instances[spl_object_id($instance)] = $instance;
    }

    public static function executeAll(string $methodName) {
        foreach (self::$instances as $instance) {
            if (method_exists($instance, $methodName)) {
                $instance->$methodName();
            }
        }
    }
}

// Příklad tříd
class A {
    public function publicMethod() {
        echo "A::publicMethod() called\n";
    }
}
class B {
    public function __construct(A $a) {
        DependencyTracker::register($this, [$a]);
    }
    public function publicMethod() {
        echo "B::publicMethod() called\n";
    }
}
class C {
    public function __construct(B $b) {
        DependencyTracker::register($this, [$b]);
    }
    public function publicMethod() {
        echo "C::publicMethod() called\n";
    }
}

// Vytvoření instancí
$a = new A();
$b = new B($a);
$c = new C($b);

// Volání všech publicMethod() na konci skriptu
DependencyTracker::executeAll('publicMethod');
