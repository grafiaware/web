<?php
//  Automatické sledování instancí – sleduje se každá instance načtená přes get()

$container = new MyContainer();

$container->set(A::class, function() {return new A();});
$container->set(B::class, function($c) {return new B($c->get(A::class));});
$container->set(C::class, function($c) {return new C($c->get(B::class));});

// Vytvoření instancí přes DI
$a = $container->get(A::class);
$b = $container->get(B::class);
$c = $container->get(C::class);

// Zavolání publicMethod() na všech sledovaných objektech
$container->executeAll('publicMethod');
