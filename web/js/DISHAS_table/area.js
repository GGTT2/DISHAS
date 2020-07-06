/* global forWrapper */
/* global toolMemory */

function allTrue(list) {
    for(var i=0; i<list.length; i++)
        if(!list[i])
            return false;
    return true;
}

function allFalse(list) {
    for(var i=0; i<list.length; i++)
        if(list[i])
            return false;
    return true;
}

function flatten(tree) {
    if(tree.length === undefined)
        return [tree];
    var res = [];
    for(var i=0; i<tree.length; i++) {
        res = res.concat(flatten(tree[i]));
    }
    return res;
}

function computeOnTree(tree, comp) {
    if(tree.length === undefined) {
        return comp(tree);
    }
    var res = [];
    for(var i=0; i<tree.length; i++) {
        res.push(computeOnTree(tree[i], comp));
    }
    return res;
}

function computeTreeFromTree(tree, comp, tree2) {
    if(tree.length === undefined)
        return comp(tree, tree2);
    var res = [];
    for(var i=0; i<tree.length; i++) {
        res.push(computeOnTree(tree[i], comp));
    }
    return res;
}

function getSmartNumberOnTree(tree) {
    function getsn(elm) {
        return elm.getSmartNumber();
    }
    return computeOnTree(tree, getsn);
}

function setSmartNumberOnTree(tree, treeval) {
    function setsn(elm, val) {
        return elm.setSmartNumber(val);
    }
    return computeTreeFromTree(tree, setsn, treeval);
}

function setPropOnTree(tree, key, val) {
    function setprop(elm) {
        return elm.setProp(key, val, false);
    }
    return computeOnTree(tree, setprop);
}

function appendPropOnTree(tree, key, val) {
    function setprop(elm) {
        if(key === "suggestion_source") {
            for(var s=0; s<elm.props.suggestion_source.length; s++) {
                // hypothesis: targetList always is a singleton
                if(elm.props.suggestion_source[s].area.targetList[0] === val.area.targetList[0]) {
                    elm.props.suggestion_source[s].area = val.area;
                    elm.props.suggestion_source[s].tool = val.tool;
                    return;
                }
            }
        }
        return elm.appendProp(key, val);
    }
    return computeOnTree(tree, setprop);
}


class SuperArea {
    /**
     * Class describing a computation pattern. It is composed of source 
     * \ :js:class:`SuperCell`\ s,
     * used to perform the computation, and of target \ :js:class:`SuperCell`\ s, 
     * which will be filled with the result
     * of the computation
     * @param  {Array}    sourceArea  Tree of source \ :js:class:`SuperCell`\ s
     * @param  {Array}    targetArea  Tree of target \ :js:class:`SuperCell`\ s
     * @param  {Object}   options     DEPRACATED! options
     * @param  {RootZone} hot         DEPRECATED! The \ :js:class:`RootZone`\  object in which the computation will take place
     * @return {SuperArea}
     */
    constructor(sourceArea, targetArea, options, hot) {
        /**
         * Tree of source \ :js:class:`SuperCell`\ s
         * @type {Array}
         */
        this.sourceArea = sourceArea;
        /**
         * Tree of target \ :js:class:`SuperCell`\ s
         * @type {Array}
         */
        this.targetArea = targetArea;
        /**
         * options
         * @type {Object}
         */
        this.options = options;
        
        /**
         * List of source \ :js:class:`SuperCell`\ s
         * @type {Array}
         */
        this.sourceList = flatten(this.sourceArea);
        /**
         * List of target \ :js:class:`SuperCell`\ s
         * @type {Array}
         */
        this.targetList = flatten(this.targetArea);

        // To be removed
        if(hot === undefined && this.sourceList.length > 0)
            this.sourceHot = this.sourceList[0].infos.hot;
        else
            this.sourceHot = hot;
        if(hot === undefined && this.targetList.length > 0)
            this.targetHot = this.targetList[0].infos.hot;
        else
            this.targetHot = hot;
        /**
         * List of sources and targets currently displayed on screen
         * @type {Array}
         */
        this.alteredCells = [];
    }
    /**
     * Return a tree of \ :js:class:`SmartNumber`\ s from the tree of sources \ :js:attr:`sourceArea`\ 
     * @return {Array}
     */
    getSmartNumberSource() {
        return getSmartNumberOnTree(this.sourceArea);
    }
    // DEPRECATED
    render() {
    }
    /**
     * Fill the target \ :js:class:`SuperCell`\ s (i.e. the tree \ :js:attr:`targetArea`\ ) with a tree of \ :js:attr:`SmartNumber`\ s
     * @param {Array} tree  tree of \ :js:attr:`smartNumber`\ s
     * @return {undefined}
     */
    setSmartNumberTarget(tree) {
        setSmartNumberOnTree(this.targetArea, tree);
    }
    /**
     * Erase all the \ :js:attr:`SuperCell`\ s in \ :js:attr:`targetArea`\ 
     * @return {undefined}
     */
    eraseTarget() {
        computeOnTree(this.targetArea, function(elm) {elm.erase(false, false);});
    }
    /**
     * For each \ :js:attr:`SuperCell`\  in \ :js:attr:`targetArea`\ , set the props[specified key] to the given value
     * @param {string} key  key of the props attribute in target  \ :js:attr:`SuperCell`\ s
     * @param {object} val  value to be put
     * @return {undefined}
     */
    setPropTarget(key, val) {
        setPropOnTree(this.targetArea, key, val);
    }
    /**
     * For each \ :js:attr:`SuperCell`\  in \ :js:attr:`sourceArea`\ , set the props[specified key] to the given value
     * @param {string} key  key of the props attribute in source  \ :js:attr:`SuperCell`\ s
     * @param {object} val  value to be put
     * @return {undefined}
     */
    setPropSource(key, val) {
        setPropOnTree(this.sourceArea, key, val);
    }
    /**
     * For each \ :js:attr:`SuperCell`\  in \ :js:attr:`sourceArea`\ , append the given value to props[specified key]
     * @param {string} key  key of the props attribute in source  \ :js:attr:`SuperCell`\ s
     * @param {object} val  value to be appended
     * @return {undefined}
     */
    appendPropSource(key, val) {
        appendPropOnTree(this.sourceArea, key, val);
    }
    /**
     * Apply the specified boolean test to every source \ :js:attr:`SuperCell`\ 
     * @param  {function} check  boolean function to be applied on each source \ :js:attr:`SuperCell`\ 
     * @return {Boolean}
     */
    checkSourceArea(check) {
        var boolTree = computeOnTree(this.sourceArea, check);
        var boolList = flatten(boolTree);
        return allTrue(boolList);
    }
    /**
     * Apply the specified boolean test to every target \ :js:attr:`SuperCell`\ 
     * @param  {function} check  boolean function to be applied on each target \ :js:attr:`SuperCell`\ 
     * @return {Boolean}
     */
    checkTargetArea(check) {
        var boolTree = computeOnTree(this.targetArea, check);
        var boolList = flatten(boolTree);
        return allTrue(boolList);
    }
    // TO REMOVE
    isNull() {
        if(this.sourceArea.length === 0 && this.targetArea.length === 0)
            return true;
        return false;
    }
    /**
     * Set the "source" and "target" properties of the corresponding \ :js:attr:`SuperCell`\ s, so that
     * the \ :js:attr:`SuperArea`\  can be visualized.
     * @param  {Boolean} render  Deprecated
     * @return {undefined}
     */
    viewArea(render) {
        var area = this;
        if(area.isNull())
            return;
        for(var i=0; i<area.sourceList.length; i++) {
            area.sourceList[i].setProp("source", true, false);
            this.alteredCells.push(area.sourceList[i]);
        }
        for(var i=0; i<area.targetList.length; i++) {
            area.targetList[i].setProp("target", true, false);
            this.alteredCells.push(area.targetList[i]);
        }
    }
    /**
     * Unset the "source" and "target" properties of the corresponding \ :js:attr:`SuperCell`\ s, so that
     * the \ :js:attr:`SuperArea`\  is no longer visualized.
     * @param  {Boolean} render  DEPRECATED
     * @return {undefined}
     */
    cleanView(render) {
        for(var i=0; i<this.alteredCells.length; i++) {
            this.alteredCells[i].setProp("target", false, false);
            this.alteredCells[i].setProp("source", false, false);
        }
        this.alteredCells = [];
    }
}

class InputTool {
    /**
     * Class representing a tool, able to act on a \ :js:class:`SuperArea`\ .
     * @param  {function}  areaGenerator  a function that will generate a \ :js:class:`SuperArea`\  (i.e. source and target
     * \ :js:class:`SuperCell`\ s) from a given selection
     * @param  {function}  mathFunction   the mathematical function to be applied
     * @return {InputTool}
     */
    constructor(areaGenerator, mathFunction) {
        this.areaGenerator = areaGenerator;
        this.mathFunction = mathFunction;
        
        this.alteredCells = [];
        this.alteredHots = [];
    }
    /**
     * Generate a \ :js:class:`SuperArea`\  based on the selection in the specified \ :js:class:`RootZone`\ .
     * The "source" and "target" properties of the corresponding \ :js:class:`SuperCell`\ s are updated so that
     * the tool is visualized.
     * @param  {Array} hots  A list of one or two \ :js:class:`RootZone`\ s. If there are 2, the sources are picked in the first one
     * and the target in the second one.
     * @return {undefined}
     */
    previewTool(hots) {
        this.alteredHots = hots;
        var selection = hots[0].selectedCells;
        var areas = this.areaGenerator(selection, hots);
        for(var k=0; k<areas.length; k++) {
            var area = areas[k];
            if(area.isNull())
                continue;
            for(var i=0; i<area.sourceList.length; i++) {
                area.sourceList[i].setProp("source", true, false);
                this.alteredCells.push(area.sourceList[i]);
            }
            for(var i=0; i<area.targetList.length; i++) {
                area.targetList[i].setProp("target", true, false);
                this.alteredCells.push(area.targetList[i]);
            }
        }
    }
    /**
     * Clean the "source" and "target" properties of the previously visualized \ :js:class:`SuperCell`\ s.
     * @param  {Boolean} render  DEPRECATED
     * @return {undefined}
     */
    cleanPreview(render) {
        for(var i=0; i<this.alteredCells.length; i++) {
            this.alteredCells[i].setProp("target", false, false);
            this.alteredCells[i].setProp("source", false, false);
        }
        this.alteredCells = [];
    }
    /**
     * Apply the tool in the specified \ :js:class:`RootZone`\ s.
     * Generate a \ :js:class:`SuperArea`\  based on the selection in the first specified \ :js:class:`RootZone`\ ,
     * and apply the mathematical function.
     * @param  {Array}    hotss      A list of one or two \ :js:class:`RootZone`\ s
     * @param  {Array}    selection  The selection from which to generate the \ :js:class:`SuperArea`\ .
     * @param  {Boolean}  render     DEPRECATED
     * @param  {Boolean}  isDiff     If used for a 1st or 2nd difference computation, do not update "suggestion_target"
     * and "suggestion_source" properties of concerned \ :js:class:`SuperCell`\ s.
     * @return {undefined}
     */
    activateTool(hotss, selection, render, isDiff) {
        if(selection === undefined)
            var selection = hotss[0].getSelectedCells();
        var areas = this.areaGenerator(selection, hotss);
        var hot = this;
        forWrapper(areas.length, 0, 1000,
            function(i) {
                hot.activateToolFromArea(areas[i], render, isDiff);
            }, function() {
                if(areas.length > 1000) {
                    table.render();
                }
            }, 1000);
        
        return true;
    }
    /**
     * Apply the tool based on the specified \ :js:class:`SuperArea`\ .
     * @param  {SuperArea}  area    The area on which to apply the mathematical function
     * @param  {Boolean}    render  DEPRECATED
     * @param  {Boolean}    isDiff  If used for a 1st or 2nd difference computation, do not update "suggestion_target"
     * and "suggestion_source" properties of concerned \ :js:class:`SuperCell`\ s.
     * @return {undefined}
     */
    activateToolFromArea(area, render, isDiff) {
        if(area.isNull())
            return;
        if( !area.checkTargetArea(function(sc){return sc.testFullProp("suggested", true);})) {
            return;
        }
        if(
            !area.checkSourceArea(function(sc){return sc.isComplete();}) ||
                !area.checkSourceArea(function(sc){return sc.testFullProp("suggested", false);})
        ) {
            area.eraseTarget();
            return false;
        }
        
        var numbertree = this.mathFunction(area.getSmartNumberSource(), area.options);
        
        area.eraseTarget();
        area.setSmartNumberTarget(numbertree);
        
        if(toolMemory && (isDiff === undefined || !isDiff)) {
            area.setPropTarget("suggestion_target", area);
            // need to append in SuperCell only if it contains no suggestion_source having this area.target as target
            area.appendPropSource("suggestion_source", {area: area, tool: this});
        }
        
        return true;
    }
}

