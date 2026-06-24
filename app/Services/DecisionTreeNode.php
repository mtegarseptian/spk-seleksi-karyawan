<?php

namespace App\Services;

class DecisionTreeNode
{
    public ?int $featureIndex = null;
    public ?float $threshold = null;
    public ?DecisionTreeNode $left = null;
    public ?DecisionTreeNode $right = null;
    public ?float $probability = null; // hanya terisi jika node ini leaf
    public bool $isLeaf = false;
}