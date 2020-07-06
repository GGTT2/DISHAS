class TreeGraph {

    constructor() {
        this.nodes = {};
    }

    loadJSONTree(dependanceTree) {
        var j = 0;
        for (var label in dependanceTree) {
            var options = dependanceTree[label]['option'];
            this.addNode(label, options);
            j++;
        }
        for (var label in this.nodes) {
            var node = this.nodes[label];
            for (var i = 0; i < dependanceTree[label].children.length; i++) {
                var child = this.nodes[dependanceTree[label].children[i]];
                this.connect(node, child);
            }
        }
    }

    generateVisGraph(positions) {
        if(positions === undefined) {
            var positions = {};
        }
        var visNodes = [];
        var visEdges = [];
        for (var label in this.nodes) {
            var node = this.nodes[label];
            if(node.options.visLabel !== undefined) {
                var visLabel = node.options.visLabel;
            }
            else {
                var visLabel = label;
            }
            var visNode = {id: label, label: visLabel};
            if(label in positions) {
                visNode.x = positions[label].x;
                visNode.y = positions[label].y;
            }
            visNodes.push(visNode);
        }
        for (var label in this.nodes) {
            var node = this.nodes[label];
            for (var i = 0; i < node.children.length; i++) {
                var child = node.children[i];
                visEdges.push({from: node.label, to: child.label});
            }
        }
        return {nodes: visNodes, edges: visEdges};
    }
    
    getNodeList() {
        var nodeList = [];
        for (var key in this.nodes){
            nodeList.push(this.nodes[key]);
        }
        return nodeList;
    }

    descendantGraph(nodeList) {
        var nodesInGraph = [];
        for (var i = 0; i < nodeList.length; i++)
            nodesInGraph.push(nodeList[i]);
        for (var i = 0; i < nodeList.length; i++) {
            var node = nodeList[i];
            for (var j = 0; j < node.getDescendants().length; j++)
                nodesInGraph.push(node.getDescendants()[j]);
        }
        return this.subGraph(nodesInGraph);
    }
    
    copyGraph() {
        
        return this.subGraph(this.getNodeList());
    }

    subGraph(nodeList) {
        //construct and return the subgraph
        var subTree = new TreeGraph();
        for (var i = 0; i < nodeList.length; i++) {
            var node = nodeList[i];
            subTree.addNode(node.label, node.options);
        }
        for (var label in subTree.nodes) {
            var newNode = subTree.nodes[label];
            var oldNode = this.nodes[label];
            //connection aux enfants
            for (var i = 0; i < oldNode.children.length; i++) {
                var oldChild = oldNode.children[i];
                var newChild = subTree.nodes[oldChild.label];
                if (newChild === undefined)
                    continue;
                subTree.connect(newNode, newChild);
            }
            //connection aux parents
            for (var i = 0; i < oldNode.parents.length; i++) {
                var oldParent = oldNode.parents[i];
                var newParent = subTree.nodes[oldParent.label];
                if (newParent === undefined)
                    continue;
                subTree.connect(newParent, newNode);
            }
        }
        return subTree;
    }

    addNode(label, options) {
        if (label in this.nodes)
            return this.nodes[label];
        var node = new TreeNode(label, options);
        this.nodes[label] = node;
        return node;
    }

    connect(parent, child) {
        if (parent.getAncestors().indexOf(child) !== -1)
            throw "Cycle detected! \\_o<";
        if (parent.children.indexOf(child) === -1)
            parent.children.push(child);
        if (child.parents.indexOf(parent) === -1)
            child.parents.push(parent);
    }
    
    disconnect(parent, child) {
        var index_child_in_parent = parent.children.indexOf(child);
        var index_parent_in_child = child.parents.indexOf(parent);
        if(index_child_in_parent >= 0) {
            parent.children.splice(index_child_in_parent, 1);
        }
        if(index_parent_in_child >= 0) {
            child.parents.splice(index_parent_in_child, 1);
        }
    }
    
    removeChildren(node) {
        var children = [];
        for(var i=0; i<node.children.length; i++)
            children.push(node.children[i]);
        for(var i=0; i<children.length; i++)
            this.disconnect(node, children[i]);
    }
    
    removeParents(node) {
        var parents = [];
        for(var i=0; i<node.parents.length; i++)
            parents.push(node.parents[i]);
        for(var i=0; i<parents.length; i++)
            this.disconnect(parents[i], node);
    }

}

class TreeNode {

    constructor(label, options) {
        this.parents = [];
        this.children = [];
        this.descendants = null;
        this.ancestors = null;
        this.label = label;
        if(options === undefined) {
            var options = {};
        }
        this.options = options;
    }
    getDescendants(descendantList) {
        if (descendantList === undefined)
            var descendantList = [];
        if (this.descendants !== null)
            return this.descendants;
        for (var i = 0; i < this.children.length; i++) {
            if (descendantList.indexOf(this.children[i]) === -1)
                descendantList.push(this.children[i]);
            this.children[i].getDescendants(descendantList);
        }
        return descendantList;
    }
    getAncestors(ancestorList) {
        if (ancestorList === undefined)
            var ancestorList = [];
        if (this.ancestors !== null)
            return this.ancestors;
        for (var i = 0; i < this.parents.length; i++) {
            if (ancestorList.indexOf(this.parents[i]) === -1)
                ancestorList.push(this.parents[i]);
            this.parents[i].getAncestors(ancestorList);
        }
        return ancestorList;
    }
}