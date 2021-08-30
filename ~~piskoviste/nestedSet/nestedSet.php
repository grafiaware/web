<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Children extends SplPriorityQueue
{
    public function compare($priority1, $priority2)
    {
        if ($priority1 === $priority2) return 0;
        return $priority1 < $priority2 ? -1 : 1;
    }
}


class Node {
    private $left;
    private $right;
    private $children;

    public function __construct() {
        $this->children = new Children();
    }

    public function add(Node $node): Node {
        $this->right = $this->right + 2;
        $node->setLeft($this->right-2);
        $node->setRight($this->right-1);
        $this->children->insert($node, $node->getLeft());
        return $node;
    }

    public function getLeft() {
        return $this->left;
    }

    public function getRight() {
        return $this->right;
    }

    public function setLeft($left) {
        $this->left = $left;
        return $this;
    }

    public function setRight($right) {
        $this->right = $right;
        return $this;
    }


}

$nestedSet = new Node();
$nestedSet->setLeft(0);
$nestedSet->setRight(1);

$a1 = $nestedSet->add(new Node());
$a2 = $nestedSet->add(new Node());
$a3 = $nestedSet->add(new Node());
$b1 = $a1->add(new Node());
$a2->add(new Node());
$b1->add(new Node());

while($nestedSet->valid()){
    print_r($nestedSet->current());
    echo "<BR>";
    $nestedSet->next();
}