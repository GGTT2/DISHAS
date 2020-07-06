/* global commentaryBinding */
/* global codeList */
/* global keyList */
/* global mainKeyBindings */
/* global toolsKeyBindings */
/* global ForwardLinearInterpolation */
/* global BetweenLinearInterpolation */
/* global BetweenArgument */
/* global HorizontalLinearInterpolation */
/* global BackwardLinearInterpolation */
/* global BackwardHorizontalLinearInterpolation */
/* global previewKeyBinding */
/* global TableInterface */
/* global ajaxSave */
/* global hots */
/* global forWrapper */
/* global Plotly */
/* global LSQ */
/* global Get_1Arg_Complete */
/* global Get_2Arg_Complete */
/* global models */
/* global ComputationZone1Arg */
/* global ComputationZone2Arg */
/* global Identity2Arg */
/* global InfoHTMLTable */
/* global VerticalInfoZone */
/* global HorizontalInfoZone */


commentaryBinding = "c";

codeList = ["Digit0", "Digit1", "Digit2", "Digit3", "Digit4", "Digit5", "Digit6", "Digit7", "Digit8", "Digit9",
    "Numpad0", "Numpad1", "Numpad2", "Numpad3", "Numpad4", "Numpad5", "Numpad6", "Numpad7", "Numpad8", "Numpad9",
    "ArrowRight", "ArrowLeft", "ArrowUp", "ArrowDown",
    "PageUp", "PageDown", "ControlLeft", "Enter", "Tab"
];

keyList = ["*", "Delete", "Backspace", "a", "-"];

mainKeyBindings = {
    undo: (key) => (key.ctrlKey && key.key === "z"),
    redo: (key) => (key.ctrlKey && key.key === "Z"),
    minusOne: (key) => (key.code === "KeyQ" && !key.ctrlKey),
    plusOne: (key) => (key.code === "KeyE" && !key.ctrlKey),
    switchZone: (key) => (key.key === "Tab" && key.shiftKey),
    nop: (key) => ((key.key === commentaryBinding && !key.ctrlKey) || ((keyList.indexOf(key.key) === -1) && (codeList.indexOf(key.code) === -1)))
};

toolsKeyBindings = {
    KeyS : [ForwardLinearInterpolation],
    KeyB : [BetweenLinearInterpolation, BetweenArgument],
    KeyD : [HorizontalLinearInterpolation],
    KeyW : [BackwardLinearInterpolation],
    KeyA : [BackwardHorizontalLinearInterpolation],
    KeyX : []
};

previewKeyBinding = "KeyX";

/**
 * Increase the size an array so that it fits the size of a
 * \ :js:class:`MetaZone`\  object.
 * **NOT USED ANYMORE**
 * @param  {MetaZone} selectedMetaZone
 * @param  {Array} data
 * @return {undefined}
 */
function resizeArraysFromZone(selectedMetaZone, data) {
    var nl = selectedMetaZone.R;
    var nc = selectedMetaZone.C;
    //if necessary, increase data size (important because to fills in default values)
    while(data.length < nl)
        data.push([]);
    for(var i=0; i<data.length; i++)
        while(data[i].length < nc)
            data[i].push("");
}

/**
 * Fill an array with the content of a \ :js:class:`MetaZone`\  object.
 * @param  {MetaZone} selectedMetaZone.
 * **NOT USED ANYMORE**
 * @param  {Array} data
 * @return {undefined}
 */
function fillDataFromCells(selectedMetaZone, data) {
    for(var i=0; i<selectedMetaZone.R; i++) {
        var line = selectedMetaZone.grid[i];
        for(var j=0; j<selectedMetaZone.C; j++) {
            if(line[j] === null) {
                data[i][j] = "";
                continue;
            }
            data[i][j] = line[j].val;
        }
    }
}

/**
 * Fill an array with the properties of a \ :js:class:`MetaZone`\  object.
 * @param  {MetaZone} selectedMetaZone.
 * **NOT USED ANYMORE**
 * @param  {MetaZone} selectedMetaZone
 * @param  {Array} data
 * @return {undefined}
 */
function fillDataPropsFromCells(selectedMetaZone, data) {
    for(var i=0; i<selectedMetaZone.R; i++) {
        var line = selectedMetaZone.grid[i];
        for(var j=0; j<selectedMetaZone.C; j++) {
            if(line[j] === null) {
                data[i][j] = "";
                continue;
            }
            data[i][j] = {
                "cell": JSON.parse(JSON.stringify(line[j].props)),
                "SuperCell": JSON.parse(JSON.stringify(line[j].parent.props, ["user_comment"]))
            };
            data[i][j].cell.source = false;
            data[i][j].cell.target = false;

            data[i][j].SuperCell.suggestion_target = line[j].parent.props.suggestion_target;
            data[i][j].SuperCell.suggestion_source = [];
            for(var k=0; k<line[j].parent.props.suggestion_source.length; k++) {
                data[i][j].SuperCell.suggestion_source.push(line[j].parent.props.suggestion_source[k]);
            }
        }
    }
}

class MainTableInterface extends TableInterface {
    /**
	 * Virtual class representing the main table of the input interface
	 * @param  {String}  containerId   DOM id of the div that will contain the table
	 * @param  {Object}  keyBindings  Dictionary for key bindings. Put it to "standard" to use standard key bindings
	 * @return {MainTableInterface}
	 */
    constructor(containerId, keyBindings={}) {
        super(containerId);
        if (keyBindings === "standard")
            keyBindings = mainKeyBindings;
        /*constructor(containerId, keyBindings) {
        super(containerId);
        if(keyBindings === undefined)
            var keyBindings = {};
        if(keyBindings === "standard")
            var keyBindings = mainKeyBindings;*/
        /**
         * Key bindings for the mathematical tools
         * @type {Object}
         */
        this.toolsKeyBindings = toolsKeyBindings;
        /**
         * General key bindings
         * @type {Object}
         */
        this.keyBindings = keyBindings;
        /**
         * Queue of snapshots of the table. It was used for the undo/redo
         * actions. **NOT USED ANYMORE**.  See instead \ :js:func:`MetaZone.history`\ 
         * @type {Array}
         */
        this.snapshotQueue = [];
        /**
         * Queue of snapshots of the table. It was used for the redo/redo
         * actions. **NOT USED ANYMORE**. See instead \ :js:func:`MetaZone.history`\ 
         * @type {Array}
         */
        this.redoQueue = [];
        /**
         * Maximum queue of snapshots. **NOT USED ANYMORE**.
         * See instead \ :js:func:`MetaZone.history`\ 
         * @type {Number}
         */
        this.maxQueue = 500;
        
        /**
         * Argument index(es) of the last selected cell
         * @type {Array}
         */
        this.oldArgPos = [-1,-1];

        /**
         * Type of visualization for the 1-arg graphs
         * @type {String}
         */
        this.graphType = "bar";
        
        /**
         * Dictionary to keep track of which keys are currently pressed, and
         * for how long they have been. Useful for the coloration of
         * cells when maintaining the shortcut for prediction.
         * @type {Object}
         */
        this.keyTimes = {};
        for(var key in this.toolsKeyBindings) {
            this.keyTimes[key] = 0;
        }
        /**
         * List of zones for this table (can be multiple, for example in the case of difference tables)
         * @type {Array}
         */
        this.zones = [];
        /**
         * Currently selected zone
         * @type {MetaZone}
         */
        this.selectedMetaZone = undefined;
        /**
         * Dictionnary of \ :js:class:`SmartNumberField`\  objects
         * for the several fields in the model box.
         * @type {Object}
         */
        this.parameter_snfs = {};
        /**
         * Currently selected \ :js:class:`ParametricFunction`\ 
         * @type {ParametricFunction}
         */
        this.model = undefined;
        
        this.interfaceBindings();
    }
    /**
     * A \ :js:class:`MainTableInterface`\  can have several zones (e.g difference tables)
     * @param {MetaZone} zone
     */
    addZone(zone) {
        this.zones.push(zone);
    }
    /**
     * Fill all empty \ :js:class:`MetaCell`\ s with '*' symbols
     * @return {undefined}
     */
    fillStarsIfEmpty() {
        for(var i=0; i<this.zones.length; i++) {
            this.zones[i].fillStarsIfEmpty();
        }
    }
    /**
     * Returns true if the content has been altered since its creation (checking the history attribute)
     * @return {Boolean} true if the table has been altered
     */
    hasChanged() {
        for(var i=0; i<this.zones.length; i++) {
            if(this.zones[i].hasChanged())
                return true;
        }
        return false;
    }
    /**
     * Validate all \ :js:class:`MetaCell`\ s
     * @return {undefined}
     */
    validateAll() {
        for(var i=0; i<this.zones.length; i++) {
            this.zones[i].validateAll();
        }
    }
    /**
     * Select the displayed zone (in case the table has several zones, e.g. difference tables)
     * @param  {Number} index  index in the \ :js:attr:`zones`\  attribute
     * @return {undefined}
     */
    switchZone(index) {
        if(index === undefined) {
            var index = this.zoneIndex + 1;
            if(index >= this.zones.length)
                index = 0;
        }
        this.selectedMetaZone = this.zones[index];
        this.hot.selectedMetaZone = this.zones[index];
        this.updateSettings({data : this.zones[index].data});
        this.render();
        this.zoneIndex = index;
        
        this.onSwitchZone(index);
    }
    /**
     * Callback function, called when the selection in the table is changed. Will toggle the commentary window 
     * if needed, and update the information tables (if needed in the case 2-arg tables)
     * @return {undefined}
     */
    onSelectionChanged() {
        var cells = this.selectionToSuperCells();
        var metacells = this.selectionToMetaCells();
        if(cells.length === 1) {
            this.showCommentary(cells[0], metacells);
        }
        else {
            this.focusLog();
        }
        
        if(this.getSelectedSuperCell() === null) {
            this.highlightInfos([]);
            return;
        }
        var argPos = this.getSelectedSuperCell().infos.argPos;
        this.highlightInfos(argPos);
        var stop = true;
        for(var i=0; i<argPos.length; i++)
            if(argPos[i] !== this.oldArgPos[i]) {
                stop = false;
                break;
            }
        if(stop) return;
        
        if(this.nargs === 2) {
            this.updateInfos();
            this.renderInfos();
        }
        this.oldArgPos = argPos;
    }
    /**
     * Callback function for all keys. To be given as a callback to the key listener
     * @param  {Object} key  key event object
     * @return {undefined}
     */
    beforeKeyDown(key) {
        if(!this.isActive())
            return;
        if(key.ctrlKey) {
            if(key.key === "ArrowUp" || key.key === "ArrowRight" ||
                    key.key === "ArrowDown" || key.key === "ArrowLeft")
                this.validateSelection();
        }
        if(key.key === " " && !key.shiftKey)
            this.validateSelection();
        super.beforeKeyDown(key);
        
        for(var func in this.keyBindings) {
            if(this.keyBindings[func](key)) {
                this[func]();
                key.stopImmediatePropagation();
            }
        }
        
        if(this.toolsKeyBindings[key.code] !== undefined && !key.shiftKey && !key.ctrlKey) {
            if(!this.selectedMetaZone.spec.readonly) {
                this.keyDown(key);
                key.stopImmediatePropagation();
            }
        }
        
        if(key.key === "s") {
            if(key.ctrlKey) {
                ajaxSave(true);
            }
            key.preventDefault();
        }
    }
    /**
     * Callback function to be called on key down
     * @param  {Object} keyEvent  key event object
     * @return {undefined}
     */
    keyDown(keyEvent) { //event must be sent from own beforKey
        var key = keyEvent.code;
        if(this.keyTimes[key] !== undefined && this.keyTimes[key] === 0) {
            this.keyTimes[key] = performance.now();
            for(var i=0; i<this.toolsKeyBindings[key].length; i++)
                this.previewTool(this.toolsKeyBindings[key][i]);
            if(key === previewKeyBinding)
                this.previewOrigin();
            this.render();
        }
    }
    /**
     * Callback function to be called on key up
     * @param  {Object} key  key event object
     * @return {undefined}
     */
    keyUp(key) { //event must be sent from mainWindow
        if(this.keyTimes[key] !== undefined) {
            var delay = performance.now() - this.keyTimes[key];
            this.keyTimes[key] = 0;
            for(var i=0; i<this.toolsKeyBindings[key].length; i++)
                this.cleanPreview(this.toolsKeyBindings[key][i]);
            if(delay < 500) {
                for(var i=0; i<this.toolsKeyBindings[key].length; i++)
                    this.activateTool(this.toolsKeyBindings[key][i]);
                if(key === "KeyS")
                    this.moveDown();
                if(key === "KeyW")
                    this.moveUp();
                if(key === "KeyA")
                    this.moveLeft();
                if(key === "KeyD")
                    this.moveRight();
            }
            if(key === previewKeyBinding)
                this.cleanPreviewOrigin();
            this.render();
        }
    }
    /**
     * Read the props attribute of the currently selected \ :js:class:`SuperCell`\  , and 
     * show, if existing, the computation pattern (\ :js:class:`SuperArea`\ ) used to produce it
     * @return {undefined}
     */
    previewOrigin() {
        var selection = this.getSelectedCell();
        var area = this.selectedMetaZone.superGrid[selection[0]][selection[1]].props.suggestion_target;
        if(area === null || area === undefined)
            return;
        area.viewArea();
        this.areasToClean.push(area);
        this.render();
    }
    /**
     * Clean the preview generated by the \ :js:func:`previewOrigin`\  method
     * @return {undefined}
     */
    cleanPreviewOrigin() {
        for(var i=0; i<this.areasToClean.length; i++) {
            this.areasToClean[i].cleanView();
        }
        this.areasToClean = [];
        hots[0].render();
    }
    /**
     * Adjsut the value of the currently selected \ :js:class:`MetaCell`\ , by adding or 
     * substracting 1 to it
     * @param  {Number} delta  value to add, +/-1
     * @return {undefined}
     */
    plusMinusOne(delta) {
        var originalSelectedCells = this.getSelectedCells();
        var sc1 = this.selectedMetaZone.superGrid[originalSelectedCells[originalSelectedCells.length-1][0]][originalSelectedCells[originalSelectedCells.length-1][1]];
        if(sc1.props.symmetry_source !== undefined)
            return ;
        if(sc1.isComplete()) {
            if(this.lockSuper) {
                var pos = sc1.zones.length-1;
            }
            else {
                var pos = this.selectedMetaZone.grid[this.getSelectedCell()[0]][this.getSelectedCell()[1]].infos.position;
            }
            var sn = sc1.getSmartNumber();
            sn.addOne(pos,delta);
            sc1.setSmartNumber(sn);
            sc1.validateNonEmpty();
            sc1.onTouchValue();
            this.render();
        }
    }
    /**
     * Adjsut the value of the currently selected \ :js:class:`MetaCell`\ , by adding 1 to it
     * @return {undefined}
     */
    plusOne() {
        this.plusMinusOne(1);
    }
    /**
     * Adjsut the value of the currently selected \ :js:class:`MetaCell`\ , by substracting 1 to it
     * @return {undefined}
     */
    minusOne() {
        this.plusMinusOne(-1);
    }
    /**
     * Validate the currently selected cells
     * @param  {Array} selections  Selection of cells in the \ :js:class:`BoundingBoxes`\  format [ [x_0, y_0, x_1, y_1], ... ]
     * @return {undefined}
     */
    validateSelection(selections) {
        if(selections === undefined) {
            selections = this.getSelectedCells();
        }
        var cells = this.selectionToSuperCells(selections);
        
        var hot = this;
        setTimeout(function() {
            forWrapper(cells.length, 0, 1000,
                function(i) {
                    cells[i].validateNonEmpty();
                    cells[i].onTouchValue();
                }, function() {
                    hot.render();
                }, 1000);
        }, 100);
    }
    /**
     * Propagate the modifications of all cells (for e.g. update the 1st and 
     * 2nd differences if you manually changed some values)
     * @return {undefined}
     */
    touchAllValidated() {
        for(var z=0; z<this.zones.length; z++) {
            var selection = [[0,0,this.zones[z].R-1,this.zones[z].C-1]];
            var scs = this.selectionToSuperCells(selection, z);
            for(var i=0; i<scs.length; i++) {
                if(scs[i].isComplete() && scs[i].testFullProp("suggested", false)) {
                    scs[i].onTouchValue();
                }
            }
        }
    }
    /**
     * Undo the previous action on the table.
     * @return {undefined}
     */
    undo() {
        this.selectedMetaZone.undo();
    }
    /**
     * Redo the previously cancelled action on the table.
     * @return {undefined}
     */
    redo() {
        this.selectedMetaZone.redo();
    }
    /**
     * Perform a snapshot of the table.
     * **NOT USED**
     * @return {undefined}
     */
    snapshot() {
        var data = [];
        var dataprops = [];
        resizeArraysFromZone(this.selectedMetaZone, data);
        resizeArraysFromZone(this.selectedMetaZone, dataprops);
        fillDataFromCells(this.selectedMetaZone, data);
        fillDataPropsFromCells(this.selectedMetaZone, dataprops);
        return {"data" : data, "dataprops": dataprops};
    }
    /**
     * Restore the table from a given snapshot.
     * **NOT USED**
     * @param  {Array} snap  a snaphsot of the table, as provided by the \ :js:func:`snapshot`\  method
     * @return {undefined}
     */
    restore(snap) {
        for(var i=0; i<this.selectedMetaZone.R; i++) {
            var line = this.selectedMetaZone.grid[i];
            for(var j=0; j<this.selectedMetaZone.C; j++) {
                if(line[j] === null) {
                    continue;
                }
                line[j].fill(snap.data[i][j]);
            }
        }
        for(var i=0; i<this.selectedMetaZone.R; i++) {
            var line = this.selectedMetaZone.grid[i];
            for(var j=0; j<this.selectedMetaZone.C; j++) {
                if(line[j] === null) {
                    continue;
                }
                for(var key in snap.dataprops[i][j].cell) {
                    line[j].setProp(key, snap.dataprops[i][j].cell[key]);
                }
                for(var key in snap.dataprops[i][j].SuperCell) {
                    line[j].parent.setProp(key, snap.dataprops[i][j].SuperCell[key]);
                }
            }
        }
        this.render();
    }
    /**
     * Use the specfied \ :js:class:`InputTool`\  object on the table, based on its currently selected cells
     * @param  {InputTool} tool    \ :js:class:`InputTool`\  object to use on this table
     * @param  {Boolean}   render  whether to render the table or not (TODO check if necessary)
     * @return {undefined}
     */
    activateTool(tool, render) {
        var done = tool.activateTool([this]);
        if(!done) {
            this.snapshotQueue.splice(-1,1);
        }
        if(render !== undefined && render)
            this.render();
    }
    /**
     * Display the computation pattern (i.e. \ :js:class:`SuperArea`\ ) 
     * of the specified \ :js:class:`InputTool`\  as it would be if used 
     * with the currently selected cells
     * @param  {InputTool} tool  \ :js:class:`InputTool`\  whose computation pattern is to be displayed
     * @return {undefined}
     */
    previewTool(tool) {
        tool.previewTool([this]);
    }
    /**
     * Clean the preview displayed by the \ :js:func:`previewTool`\  method
     * @param  {InputTool} tool    The \ :js:class:`InputTool`\  object whose pattern has been displayed
     * @param  {Boolean}   render  whether to render the table or not (TODO check if necessary)
     * @return {undefined}
     */
    cleanPreview(tool, render) {
        tool.cleanPreview(render);
    }
    /**
     * Create a new Plotly graph representing the current \ :js:attr:`selectedMetaZone`\  and display it in the DOM
     * @param  {String} idGraph  DOM id of the div where to display the graph
     * @return {undefined}
     */
    fillGraph(idGraph) {
        if(this.nargs === 1) {
            var plotx = [];
            var ploty = [];
            for(var i=0; i<this.selectedMetaZone.spec.args[0].nsteps; i++) {
                if(!this.selectedMetaZone.fromPath([1,0,i]).isComplete() || !this.selectedMetaZone.fromPath([0,0,i]).isComplete())
                    continue;
                plotx.push(this.selectedMetaZone.fromPath([1,0,i]).getSmartNumber().computeDecimal());
                ploty.push(this.selectedMetaZone.fromPath([0,0,i]).getSmartNumber().computeDecimal());
            }
            Plotly.newPlot(idGraph, [{
                x: plotx,
                y: ploty,
                type: this.graphType
            }]);
        }
        else {
            var plotx = [];
            var ploty = [];
            var plotz = [];
            for(var i=0; i<this.selectedMetaZone.spec.args[0].nsteps; i++) {
                if(this.selectedMetaZone.fromPath([1,0,i,0]).isComplete())
                    plotx.push(this.selectedMetaZone.fromPath([1,0,i,0]).getSmartNumber().computeDecimal());
            }
            for(var j=0; j<this.selectedMetaZone.spec.args[1].nsteps; j++) {
                var col = j;
                if(this.selectedMetaZone.fromPath([1,1,0,col]).isComplete())
                    ploty.push(this.selectedMetaZone.fromPath([1,1,0,col]).getSmartNumber().computeDecimal());
            }
            for(var i=0; i<this.selectedMetaZone.spec.args[0].nsteps; i++) {
                if(!this.selectedMetaZone.fromPath([1,0,i,0]).isComplete())
                    continue;
                plotz.push([]);
                for(var j=0; j<this.selectedMetaZone.spec.args[1].nsteps; j++) {
                    var col = j;
                    if(!this.selectedMetaZone.fromPath([1,1,0,col]).isComplete())
                        continue;
                    plotz[i].push(this.selectedMetaZone.fromPath([0,0,i,col]).getSmartNumber().computeDecimal());
                }
            }
            Plotly.newPlot(idGraph, [{
                x: plotx,
                y: ploty,
                z: plotz,
                type: "heatmap"
            }], {yaxis: {autorange: "reversed"}});
        }
    }
    /**
     * Callback function to call when the table type is changed. 
     * **Now that the table type is fixed, it is NOT USED ANYMORE**
     * @param  {String} typeId  Table type id (in the database)
     * @return {undefined}
     */
    typeChanged(typeId) {
        return ;
    }
    /**
     * Use the current \ :js:attr:`model`\  (i.e. \ :js:class:`ParametricFunction`\ ) to fill the table
     * @return {undefined}
     */
    fillTableFromModel() {
        this.model.tool.activateTool([this.zones[0]], []);
        this.render();
    }
    /**
     * Perform several steps of LSQ procedure with the current \ :js:attr:`model`\ 
     * @return {undefined}
     */
    performLSQ() {
        if(this.model.dimension === 1) {
            LSQ(this.model, Get_1Arg_CompleteAndValidated([this.zones[0]]));
        }
        else {
            LSQ(this.model, Get_2Arg_CompleteAndValidated([this.zones[0]]));
        }
    }
    /**
     * Get the currently principal selected \ :js:class:`SuperCell`\ 
     * @return {undefined}
     */
    getSelectedSuperCell() {
        return this.selectedMetaZone.superGrid[this.getSelectedCell()[0]][this.getSelectedCell()[1]];
    }
    /**
     * Put in place a "auto-computation", i.e. a computation to be performed each time a value is changed
     * @param  {String} name     name of the auto-computation
     * @param  {InputTool} tool  \ :js:class:`InputTool`\  used
     * @return {undefined}
     */
    createMathZone(name, tool) {
        var mathSpec = JSON.parse(JSON.stringify(this.selectedMetaZone.spec));
        mathSpec.readonly = false;
        if(this.nargs === 1)
            var mathZone = new ComputationZone1Arg(mathSpec, {nargs: this.nargs});
        else
            var mathZone = new ComputationZone2Arg(mathSpec, {nargs: this.nargs});
        mathZone.tool = tool;
        this.mathZones[name] = mathZone;
    }
    /**
     * Update the highlight zone of all information tables
     * @param  {Array} argpos  argument index(es) of the selected cell (in the format [arg0, arg1])
     * @return {undefined}
     */
    highlightInfos(argpos) {
        for(var i=0; i<this.verticalInformationTables.length; i++)
            if(this.verticalInformationTables[i] !== undefined)
                if(argpos.length > 0)
                    this.verticalInformationTables[i].updateHighlight(argpos[0]);
                else
                    this.verticalInformationTables[i].removeHighlight();
        for(var j=0; j<this.horizontalInformationTables.length; j++)
            if(this.horizontalInformationTables[j] !== undefined) {
                if(argpos.length > 1)
                    this.horizontalInformationTables[j].updateHighlight(argpos[1]);
                else
                    this.horizontalInformationTables[j].removeHighlight();
            }
    }
    /**
     * Transfert values from the mathZone of the information tables to its graphicZone. 
     * The argpos parameter is usefull in the 2-arg case, where the graphicZone is only 
     * a restricted 1-arg view of the mathZone which depends on the currently selected cell.
     * @param  {Array} argpos   argument index(es) of the selected cell (in the format [arg0, arg1])
     * @return {undefined}
     */
    updateInfos(argpos) {
        if(argpos === undefined && this.selectedMetaZone.spec.args.length === 2) {
            if(this.getSelectedSuperCell() === null || this.getSelectedSuperCell() === undefined || !this.getSelectedSuperCell().isEntry())
                return;
            var argpos = this.getSelectedSuperCell().infos.argPos;
        }
        if(argpos === undefined && this.selectedMetaZone.spec.args.length === 1) {
            if(this.getSelectedSuperCell() === null || this.getSelectedSuperCell() === undefined) {
                var argpos = [0];
            }
            else {
                var argpos = this.getSelectedSuperCell().infos.argPos;
            }
        }
        for(var i=0; i<this.verticalInformationTables.length; i++)
            if(this.verticalInformationTables[i] !== undefined)
                this.verticalInformationTables[i].updateGraphicZone(argpos);
        for(var j=0; j<this.horizontalInformationTables.length; j++)
            if(this.horizontalInformationTables[j] !== undefined)
                this.horizontalInformationTables[j].updateGraphicZone(argpos);
    }
    /**
     * Render (update graphically) each information table
     * @return {undefined}
     */
    renderInfos() {
        for(var i=0; i<this.verticalInformationTables.length; i++)
            if(this.verticalInformationTables[i] !== undefined)
                this.verticalInformationTables[i].render();
        for(var j=0; j<this.horizontalInformationTables.length; j++)
            if(this.horizontalInformationTables[j] !== undefined)
                this.horizontalInformationTables[j].render();
    }
    /**
     * Create a vertical information table
     * @param  {Number}    [index=undefined]         defaults to the first available slot 
     * @param  {InputTool} tool         			 \ :js:class:`InputTool`\  used
     * @param  {Object}    [graphicSpec=undefined]  defaults to the same template as the entry
     * @param  {String}    toolName
     * @return {undefined}
     */
    createVerticalComputation(index, tool, graphicSpec, toolName, jsonData) {
        console.log(graphicSpec);
        if(graphicSpec === undefined) {
            var graphicSpec = this.selectedMetaZone.spec.entries[0];
            graphicSpec.nsteps = this.selectedMetaZone.spec.args[0].nsteps;
        }
        if(tool === undefined) {
            // var tool = Identity2Arg;
        }
        if(index === undefined)
            index = hot.verticalInformationTables.length;
        
        if(jsonData) {
            var mathSpec = jsonData.template;
        }
        else {
            var mathSpec = JSON.parse(JSON.stringify(this.selectedMetaZone.spec));
            mathSpec.entries[0].ncells = graphicSpec.ncells;
            mathSpec.entries[0].type = graphicSpec.type;
        }
        graphicSpec.readonly = false;
        mathSpec.readonly = false;
        
        this.resizeVerticalInfoDiv(index, graphicSpec);
        var containerId = this.getVerticalInfoId(index);
        //var verticalComputation = new InfoHotTable(containerId, tool, true);
        var verticalComputation = new InfoHTMLTable(containerId, true);
        
        if(jsonData === undefined) {
            var graphicZone = new VerticalInfoZone(graphicSpec, {hot: verticalComputation, nargs: this.nargs});
        }
        else {
            if(graphicSpec.args.length > 1) {
                var graphicZone = new VerticalInfoZone(graphicSpec, {hot: verticalComputation, nargs: this.nargs, show_args: true});
            }
            else {
                var graphicZone = new Table1ArgZone(graphicSpec, {nargs: this.nargs});
                verticalComputation.readonly = true;
            }
        }
        
        // optimization if tools already in auto_computation
        var hot = this;
        if(toolName in hot.mathZones) {
            var mathZone = hot.mathZones[toolName];
            verticalComputation.tool = undefined;
        }
        else {
            if(jsonData === undefined) {
                if(this.nargs === 1)
                    var mathZone = new ComputationZone1Arg(mathSpec, {nargs: this.nargs});
                else
                    var mathZone = new ComputationZone2Arg(mathSpec, {nargs: this.nargs});
            }
            else {
                if(this.nargs === 1)
                    var mathZone = new Table1ArgZone(mathSpec, {nargs: this.nargs});
                else
                    var mathZone = new Table2ArgZone(mathSpec, {nargs: this.nargs});
                mathZone.fromOriginalJSON(jsonData.original);
            }
        }
        if(!(toolName in hot.mathZones))
            mathZone.tool = tool;
        
        verticalComputation.createFromZones(graphicZone, mathZone);
        
        this.verticalInformationTables[index] = verticalComputation;
        this.zones[0].verticalInformationTables[index] = verticalComputation;
        
        this.createVerticalInfoHeader(index, toolName);
    }
    /**
     * Create a horizontal information table. Only for 2-arg tables
     * @param  {Number}    [index=undefined]         defaults to the first available slot 
     * @param  {InputTool} tool         			 \ :js:class:`InputTool`\  used
     * @param  {Object}    [graphicSpec=undefined]  defaults to the same template as the entry
     * @param  {String}    toolName
     * @return {undefined}
     */
    createHorizontalComputation(index, tool, graphicSpec, toolName, jsonData) {
        if(graphicSpec === undefined) {
            var graphicSpec = hot.spec.entries[0];
            graphicSpec.nsteps = hot.spec.args[1].nsteps;
            graphicSpec.offset = hot.spec.args[0].ncells + 1; /* +1 because we use InfoHTMLTable */
        }
        if(tool === undefined) {
            // var tool = Identity2Arg;
        }
        if(index === undefined)
            index = hot.horizontalInformationTables.length;
        
        if(jsonData) {
            var mathSpec = jsonData.template;
        }
        else {
            var mathSpec = JSON.parse(JSON.stringify(this.selectedMetaZone.spec));
            mathSpec.entries[0].ncells = graphicSpec.ncells;
            mathSpec.entries[0].type = graphicSpec.type;
        }

        mathSpec.readonly = false;
        graphicSpec.readonly = false;
        
        this.resizeHorizontalInfoDiv(index, graphicSpec);
        var containerId = this.getHorizontalInfoId(index);
        //var horizontalComputation = new InfoHotTable(containerId, tool, false);
        var horizontalComputation = new InfoHTMLTable(containerId, false);
        // TODO change here
        var showArgs = false;
        if(jsonData) {
            showArgs = true;
        }
        var graphicZone = new HorizontalInfoZone(graphicSpec, {hot: horizontalComputation, nargs: this.nargs, show_args: showArgs});
        
        // optimization if tools already in auto_computation
        var hot = this;
        if(toolName in hot.mathZones) {
            var mathZone = hot.mathZones[toolName+"_horizontal"];
            horizontalComputation.tool = undefined;
        }
        else {
            if(jsonData === undefined) {
                if(this.nargs === 1)
                    var mathZone = new ComputationZone1Arg(mathSpec, {nargs: this.nargs});
                else
                    var mathZone = new ComputationZone2Arg(mathSpec, {nargs: this.nargs});
            }
            else {
                if(this.nargs === 1)
                    var mathZone = new Table1ArgZone(mathSpec, {nargs: this.nargs});
                else
                    var mathZone = new Table2ArgZone(mathSpec, {nargs: this.nargs});
                mathZone.fromOriginalJSON(jsonData.original);
            }
        }
        if(!(toolName in hot.mathZones))
            mathZone.tool = tool;

        horizontalComputation.createFromZones(graphicZone, mathZone);
        
        this.horizontalInformationTables[index] = horizontalComputation;
        this.zones[0].horizontalInformationTables[index] = horizontalComputation;
        
        this.createHorizontalInfoHeader(index, toolName);
    }
    /**
     * Used to create a vertical and a horizontal information tables, linked so that the suppression of one 
     * implies the suppression of the other
     * @param  {Number} index1
     * @param  {Number} index2
     * @param  {InputTool} tool1
     * @param  {InputTool} tool2
     * @param  {Object} graphicSpec1
     * @param  {Object} graphicSpec2
     * @param  {String} toolName
     * @return {undefined}
     */
    createLinkedComputation(index1, index2, tool1, tool2, graphicSpec1, graphicSpec2, toolName, jsonData) {
        this.createVerticalComputation(index1, tool1, graphicSpec1, toolName, jsonData);
        this.createHorizontalComputation(index2, tool2, graphicSpec2, toolName, jsonData);
        this.horizontalInformationTables[index2].linkedIndex = index1;
        this.verticalInformationTables[index1].linkedIndex = index2;
    }
    /**
     * Remove the vertical information table at the specified slot
     * @param  {Number} index  vertical slot
     * @return {undefined}
     */
    removeVerticalInformationTable(index) {
        var autotool = this.verticalInformationTables[index];
        if(autotool === undefined)
            return;
        if(autotool.linkedIndex !== undefined) {
            this.horizontalInformationTables[autotool.linkedIndex].linkedIndex = undefined;
            this.removeHorizontalInformationTable(autotool.linkedIndex);
        }
        this.verticalInformationTables[index].destroy();
        this.verticalInformationTables[index] = undefined;
        this.zones[0].verticalInformationTables[index] = undefined;
        //$('#'+this.toolBox['right_info_slot']+'-'+index+' div.wtHolder').html("");
        this.removeVerticalInfoDiv(index);
    }
    /**
     * Remove the horizontal information table at the specified slot
     * @param  {Number} index  horizontal slot
     * @return {undefined}
     */
    removeHorizontalInformationTable(index) {
        var autotool = this.horizontalInformationTables[index];
        if(autotool.linkedIndex !== undefined) {
            this.verticalInformationTables[autotool.linkedIndex].linkedIndex = undefined;
            this.removeVerticalInformationTable(autotool.linkedIndex);
        }
        this.horizontalInformationTables[index].destroy();
        this.horizontalInformationTables[index] = undefined;
        this.zones[0].horizontalInformationTables[index] = undefined;
        //$('#'+this.toolBox['bottom_info_slot']+'-'+index+' div.wtHolder').html("");
        this.removeHorizontalInfoDiv(index);
    }
    /**
     * Get the DOM id of the specified horizontal slot. 
     * To be implemented by inheritance
     * @param  {Number} index  index of the horizontal slot
     * @return {undefined}
     */
    getHorizontalInfoId(index) {
        throw "getHorizontalInfoId not implemented";
    }
    /**
     * Get the DOM id of the specified vertical slot. 
     * To be implemented by inheritance
     * @param  {Number} index  index of the vertical slot
     * @return {undefined}
     */
    getVerticalInfoId(index) {
        throw "getVerticalInfoId not implemented";
    }
    /**
     * Prepare the specified horizontal slot, according to the given graphic template. 
     * To be implemented by inheritance
     * @param  {Number} index         index of the horizontal slot
     * @param  {Object} graphicSpec  graphical template of the information table
     * @return {undefined}
     */
    resizeHorizontalInfoDiv(index, graphicSpec) {
        throw "resizeHorizontalInfoDiv not implemented";
    }
    /**
     * Prepare the specified vertical slot, according to the given graphic template. 
     * To be implemented by inheritance
     * @param  {Number} index         index of the vertical slot
     * @param  {Object} graphicSpec  graphical template of the information table
     * @return {undefined}
     */
    resizeVerticalInfoDiv(index, graphicSpec) {
        throw "resizeVerticalInfoDiv not implemented";
    }
    /**
     * Remove the specified vertical slot. 
     * To be implemented by inheritance
     * @param  {Number} index  index of the vertical slot
     * @return {undefined}
     */
    removeVerticalInfoDiv(index) {
        throw "removeVerticalInfoDiv not implemented";
    }
    /**
     * Remove the specified horizontal info slot. 
     * To be implemented by inheritance
     * @param  {Number} index  index of the horizontal slot
     * @return {undefined}
     */
    removeHorizontalInfoDiv(index) {
        throw "removeHorizontalInfoDiv not implemented";
    }
    /**
     * Create a header for the specified vertical slot. 
     * To be implemented by inheritance
     * @param  {Number} index  index of the vertical slot
     * @param  {String} name   content of the header
     * @return {undefined}
     */
    createVerticalInfoHeader(index, name) {
        throw "createVerticalInfoHeader not implemented";
    }
    /**
     * Create a header for the specified horizontal slot. 
     * To be implemented by inheritance
     * @param  {Number} index  index of the horizontal slot
     * @param  {String} name   content of the header
     * @return {undefined}
     */
    createHorizontalInfoHeader(index, name) {
        throw "createHorizontalInfoHeader not implemented";
    }
    /**
     * Callback function that will be called when zones are switched. 
     * To be implemented by inheritance
     * @param  {Number} index  Index of the newly selected zone
     * @return {undefined}
     */
    onSwitchZone(index) {
        throw "onSwitchZone not implemented";
    }
    /**
     * Update settings for this table. 
     * To be implemented by inheritance
     * @param  {Object} settings  Dictionnary of settings
     * @return {undefined}
     */
    updateSettings(settings) {
        throw "updateSettings not implemented";
    }
    /**
     * Fills the commentary box with the content of the specified \ :js:class:`SuperCell`\ . 
     * To be implemented by inheritance
     * @param  {SuperCell} SuperCell  \ :js:class:`SuperArea`\  whose user-defined comment will be printed
     * @return {undefined}
     */
    showCommentary(SuperCell) {
        throw "showCommentary not implemented";
    }
    /**
     * Fill the log console with the given string. 
     * To be implemented by inheritance
     * @param  {String} str  log to print
     * @return {undefined}
     */
    updateLog(str) {
        throw "updateLog not implemented";
    }
    /**
     * Hide the commentary box. 
     * To be implemented by inheritance
     * @return {undefined}
     */
    focusLog() {
        throw "focusLog not implemented";
    }
    /**
     * Focus the commentary box. 
     * To be implemented by inheritance
     * @return {undefined}
     */
    focusCommentary() {
        throw "focusCommentary not implemented";
    }
    /**
     * Make the div containing the graph visible. 
     * To be implemented by inheritance
     * @return {undefined}
     */
    showGraph() {
        throw "showGraph not implemented";
    }
    /**
     * Read linked mathematical parameters (if any) and copies their value in the mathematical parameters
     * of the currently selected \ :js:attr:`model`\ . 
     * To be implemented by inheritance
     * @return {undefined}
     */
    updateMathematicalParameters() {
        throw "updateMathematicalParameters not implemented";
    }
    /**
     * Callback function that will be called when a new model is selected. 
     * To be implemented by inheritance
     * @return {undefined}
     */
    onNewModel() {
        throw "onNewModel not implemented";
    }
    /**
     * Bind all the jquery events associated to the UI. 
     * To be implemented by inheritance
     * @return {Undefined}
     */
    interfaceBindings() {
        throw "toolBoxBinding not implemented";
    }
}

