<?php

/**
 * TAMASEditionGraph is a service that helps calculating the relation between edited texts and edited text, and edited text and original text. 
 * It is equivalent to javascript counterpart. 
 */

namespace TAMAS\AstroBundle\DISHASToolbox\Graph;

use Doctrine\ORM\EntityManagerInterface;
use Exception;

function inArray($object, $array) {
    foreach($array as $o) {
        if(spl_object_id($o) == spl_object_id($object)) {
            return true;
        }
    }
    return false;
}

class TAMASGraph {

    private $nodes = [];

    public function loadJSONTree($jsonDependanceTree) {
        $dependanceTree = json_decode($jsonDependanceTree, 1);
        foreach ($dependanceTree as $label => $treeNode) {
            $options = $treeNode['option'];
            $this->addNode($label, $options);
        }
        foreach ($this->nodes as $label => $node) {
            foreach ($dependanceTree[$label]['children'] as $treeChild) {
                $child = $this->nodes[$treeChild];
                $this->connect($node, $child);
            }
        }
    }

    public function addNode($label, $options) {
        if (in_array($label, $this->nodes)) {
            return;
        }
        $node = new TAMASGraphNode($label, $options);
        $this->nodes[$label] = $node;
        return $node;
    }

    public function connect($parent, $child) {
        if (inArray($child, $parent->getAncestors())) {
            throw new Exception("Cycle detected");
        }
        if (!inArray($child, $parent->getChildren())) {
            $parent->addChild($child);
        }
        if (!inArray($parent, $child->getParents())) {
            $child->addParent($parent);
        }
    }

    function getNodes() {
        return $this->nodes;
    }

    function setNodes($nodes) {
        $this->nodes = $nodes;
    }
    
    function getNode($id){
        return $this->nodes[$id];
    }

}

class TAMASGraphNode {

    private $parents = [];
    private $children = [];
    private $descendants = null;
    private $ancestors = null;
    private $label;
    private $options;
    private $public;

    public function __construct($label, $options, bool $public = true) {
        $this->public = $public;
        $this->label = $label;
        if (!$options) {
            $options = [];
        }
        $this->options = $options;
    }

    public function getDescendants(&$descendantList = null) {
        if (!isset($descendantList) || $descendantList == null ) {
            $descendantList = [];
        }
        if ($this->descendants !== null) {
            return $this->descendants;
        }
        foreach ($this->children as $child) {
            if (!inArray($child, $descendantList)) {
                array_push($descendantList, $child);
            }
            $child->getDescendants($descendantList);
        }
        return $descendantList;
    }

    public function getAncestors(&$ancestorList = null) {
        if (!isset($ancestorList) || $ancestorList == null ) {
            $ancestorList = [];
        }
        if ($this->ancestors !== null) {
            return $this->ancestors;
        }

        foreach ($this->parents as $parent) {
            if (!inArray($parent, $ancestorList)) {
               $ancestorList[] = $parent;
            }
            $parent->getAncestors($ancestorList);
        }
        return $ancestorList;
    }

    function getParents() {
        return $this->parents;
    }

    function getChildren() {
        return $this->children;
    }

    function getLabel() {
        return $this->label;
    }

    function getOption() {
        return $this->options;
    }

    function addParent($parent) {
        $this->parents[] = $parent;
    }

    function addChild($child) {
        $this->children[] = $child;
    }

    function setLabel($label) {
        $this->label = $label;
    }

    function setOption($option) {
        $this->options = $option;
    }

    /**
     * @return bool
     */
    public function isPublic(): bool
    {
        return $this->public;
    }

}
