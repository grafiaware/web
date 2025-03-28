<?php

/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */

class A {
    public function publicMethod() {
        echo "A::publicMethod() called\n";
    }
}
class B {
    public function __construct(A $a) {}
    public function publicMethod() {
        echo "B::publicMethod() called\n";
    }
}
class C {
    public function __construct(B $b) {}
    public function publicMethod() {
        echo "C::publicMethod() called\n";
    }
}
