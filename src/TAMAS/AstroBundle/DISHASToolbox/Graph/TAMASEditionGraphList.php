<?php
//NB : THIS FILE IS NOT USED ! THE FIRST FUNCTION = GETDEPENDANCETREE CAN BE FOUND IN EDITEDTEXT REPO
//THE REST IS IN GRAPH/TAMASGRAPH!!

/**
 * TAMASEditionGraph is a service that helps calculating the relation between edited texts and edited text, and edited text and original text. 
 * It is equivalent to javascript counterpart. 
 */

namespace TAMAS\AstroBundle\DISHASToolbox\Graph;

use Doctrine\ORM\EntityManagerInterface;

class TAMASEditionGraphList {

    /**
     * @var EntityManagerInterface
     */
    private $em;
    private $dependanceTree = [];
    private $jsonDependanceTree = '';

    public function __construct(EntityManagerInterface $entityManager) {
        $this->em = $entityManager;
        $this->dependanceTree = $this->getDependanceTree();
        $this->jsonDependanceTree = json_encode($this->dependanceTree);
    }

    public function getDependanceTree() {
        $dependanceTree = [];
        //this is not uptodate, there is a specific function for retrieving this list
        $editedTexts = $this->em->getRepository(\TAMAS\AstroBundle\Entity\EditedText::class)->getList(true);
        if ($editedTexts && !empty($editedTexts)) {
            foreach ($editedTexts as $editedText) {
                $tableType = '';
                if ($editedText->getTableType()) {
                    $tableType = $editedText->getTableType()->getId();
                }
                $children = [];
                foreach ($editedText->getRelatedEditions() as $child) {
                    $children[] = 'e' . $child->getId();
                }
                foreach ($editedText->getOriginalTexts() as $child) {
                    $children[] = 'o' . $child->getId();
                }
                $dependanceTree['e' . $editedText->getId()] = ['children' => $children, 'type' => 'editedText', 'id' => $editedText->getId(), 'option' => ['tableType' => $tableType, 'editionType' => $editedText->getType()]];
            }
        }
        //not uptodate
        $originalTexts = $this->em->getRepository(\TAMAS\AstroBundle\Entity\OriginalText::class)->getList();
        if ($originalTexts && !empty($originalTexts)) {
            foreach ($originalTexts as $originalText) {
                $tableType = '';
                if ($originalText->getTableType()) {
                    $tableType = $originalText->getTableType()->getId();
                }
                $dependanceTree['o' . $originalText->getId()] = ['children' => [], 'type' => 'originalItem', 'id' => $originalText->getId(), 'option' => ['tableType' => $tableType, 'editionType' => null]];
            }
        }
        return $dependanceTree;
    }

    function getJsonDependanceTree() {
        return $this->jsonDependanceTree;
    }

    function setJsonDependanceTree($jsonDependanceTree) {
        $this->jsonDependanceTree = $jsonDependanceTree;
    }

    function getGraphTree() {
        return new TAMASEditionGraphTree($this->dependanceTree);
    }

}

class TAMASEditionGraphNode {

    private $parents = [];
    private $children = [];
    private $descendants = null;
    private $ancestors = null;
    private $label;
    private $directChildrenName;
    private $option;

    public function __construct(string $label, array $node) {
        $this->label = $label;
        $this->directChildrenName = $node['children'];
        $this->option = $node['option'];
    }

    public function getDescendants() {
        if ($this->descendants !== null) {
            return $this->descendants;
        }
        $result = [];
        foreach ($this->children as $children) {
            $result[] = $children;
            $result += $this->$children->getDescendants();
        }
        $this->descendants = $result;
        return $this->descendants;
    }

    // Pb de récursivité! Boucle infinie si ancestors a un parent qui est son enfant.
    public function getAncestors() {
        if ($this->ancestors !== null) {
            return $this->ancestors;
        }
        $result = [];
        foreach ($this->parents as $parent) {
            $result[] = $parent;
            $result = array_merge($result, $parent->getAncestors());
        }
        $this->ancestors = $result;
        return $this->ancestors;
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

    function getDirectChildrenName() {
        return $this->directChildrenName;
    }

    function getOption() {
        return $this->option;
    }

    function addParents($parents) {
        $this->parents[] = $parents;
    }

    function addChildren($children) {
        $this->children[] = $children;
    }

    function setLabel($label) {
        $this->label = $label;
    }

    function setDirectChildrenName($directChildrenName) {
        $this->directChildrenName = $directChildrenName;
    }

    function setOption($option) {
        $this->option = $option;
    }

}

class TAMASEditionGraphTree {

    private $dependanceTree = null;
    private $nodes = [];

    public function __construct(array $dependanceTree) {
        $this->dependanceTree = $dependanceTree;
        foreach ($dependanceTree as $label => $treeChild) {
            $node = $this->addNode($label);
        }
        foreach ($this->nodes as $node) {
            foreach ($node->getDirectChildrenName() as $childrenName) {
                $this->connect($node, $this->nodes[$childrenName]);
            }
        }
    }

    public function addNode($label) {
        $node = new TAMASEditionGraphNode($label, $this->dependanceTree[$label]);
        $this->nodes[$label] = $node;
        return $node;
    }

    public function connect($parent, $child) {
        $parent->addChildren($child);
        $child->addParents($parent);
    }

    function getDependanceTree() {
        return $this->dependanceTree;
    }

    function getNodes() {
        return $this->nodes;
    }

    function setDependanceTree($dependanceTree) {
        $this->dependanceTree = $dependanceTree;
    }

    function setNodes($nodes) {
        $this->nodes = $nodes;
    }

}
