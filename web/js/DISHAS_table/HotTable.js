/* global validSymbols */
/* global $ */
/* global Handsontable */
/* global MainTableInterface */

/**
 * List of the symbols that can be input in a table
 * @type {Array}
 */
validSymbols = ["*", "0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "-"];

/**
 * Return the cell properties (as needed by the handsontable library) given its position.
 * @param  {HotTable} currentHot  Specified \ :js:class:`HotTable`\  object.
 * @param  {Number} row           row of the specified cell
 * @param  {Number} col           column of the specified cell
 * @param  {Object} prop          some properties provided by the handsontable callback: **UNUSED**
 * @param  {Array} colWidth       array of width for each column. Can be undefined
 * @return {undefined}
 */
function cellFunctionHot(currentHot, row, col, prop, colWidth) {
    var cellProperties = {};
    cellProperties.renderer = cellRenderer;
    cellProperties.class = "test";
    cellProperties.readOnly = currentHot.readonly;
    var MetaCell = currentHot.selectedMetaZone.grid[row];
    if(MetaCell === undefined) {
        return {"readonly" : true};
    }
    MetaCell = MetaCell[col];
    if(MetaCell === undefined)
        return {};
    if(MetaCell === null) {
        cellProperties.readOnly = true;
        if(colWidth === undefined) {
            if(row < currentHot.selectedMetaZone.R)
                cellProperties.width = cellFunctionHot(currentHot, row+1, col, prop).width;
            if(col === 0)
                cellProperties.width = 100;
        }
        else {
            cellProperties.width = colWidth[col];
        }
        return cellProperties;
    }
    if(MetaCell.isInfo()) {
        cellProperties.readOnly = true;
    }
    if(MetaCell.parent.props.symmetry_source !== undefined) {
        cellProperties.readOnly = true;
    }
    if(MetaCell.isFirst()) {
        if(colWidth === undefined)
            cellProperties.width = 100;
        else
            cellProperties.width = colWidth[col];
    }
    return cellProperties;
}

/**
 * Callback function provided to the handsontable library in order to customize the cell rendering.
 * @param  {Object} instance        the instance return by the Handsontable library
 * @param  {Object} td              DOM element representing the cell
 * @param  {Number} row             row of the specified cell
 * @param  {Number} col             column of the specified cell
 * @param  {Object} prop            **UNUSED**
 * @param  {String} value           value inside the cell
 * @param  {Object} cellProperties  wished properties of the cell, as computed with the 
 * @return {undefined}
 */
function cellRenderer(instance, td, row, col, prop, value, cellProperties) {
    Handsontable.renderers.TextRenderer.apply(instance, arguments);
    instance.selectedMetaZone.applyCSS(row, col, $(td));
}


class HotTable extends MainTableInterface {
    /**
	 * Handsontable implementation of the main table for the interface
	 * @param  {String}  containerId   DOM id of the div that will contain the table
	 * @param  {Object}  keyBindings  Dictionnary for key bindings. Put it to "standard" to use standard key bindings
	 * @return {HotTable}
	 */
    constructor(containerId, keyBindings) {
        super(containerId, keyBindings);
        var container = document.getElementById(containerId);
        /**
         * Handsontable Object
         * @type {Handsontable}
         */
        this.hot = new Handsontable(container, {}); // NOTE : instanciation of an HandsOnTable
        /**
         * Object containing information about the selection
         * @type {Object}
         */
        this.selection = {
            selectedCells: [[0,0,0,0]],
            originalSelectedCells: [[0,0,0,0]],
            nConnex: 0,
            previousSelectionMode: "none",
            previousSelectionModeCounter: 0,
            previousSelectedCells: [[0,0,0,0]],
            previousOriginalSelectedCells: [[0,0,0,0]],
            previousNConnex: 0
        };
    }
    /**
     * Call this method to free the memory associated with the table.
     * Does not work (the memory is not freed). **UNUSED**
     * @return {undefined}
     */
    destroy() {
        for(var i in this.verticalInformationTables) {
            this.removeVerticalInformationTable(i);
        }
        delete this.redoQueue;
        delete this.snapshotQueue;
        for(var i in this.zones) {
            this.zones[i].destroy();
            delete this.zones[i];
        }
        if(this.selectedMetaZone.destroy !== undefined)
            this.selectedMetaZone.destroy();
        delete this.selectedMetaZone;
        
        this.hot.destroy();
        delete this.hot;
        
    }
    /**
     * Render the table, i.e. update the graphic elements 
     * so that they reflect the changes made in the \ :js:class:`MetaZone`\ .
     * @return {undefined}
     */
    render() {
        // the render action is synchronized with the adding of a null object to the history.
        // this is because calls to render have been optimized (they
        // are done only when necessary) and thus correspond to the
        // delimitation of actions we want to be able to cancel in bulk.
        this.selectedMetaZone.history.push(null);
        this.hot.render();
        if(this.selectedMetaZone !== undefined) {
            this.updateInfos();
            this.renderInfos();
        }
    }
    /**
     * Ensure that the scrolling of the side vertical information tables are
     * synchronized with the scrolling of this main table.
     * To be completed by inheritance.
     * @return {undefined}
     */
    synchronizeVerticalScrollings() {
        console.log("warning, info scrolling not synchronized");
    }
    /**
     * Ensure that the scrolling of the side horizontal information tables are
     * synchronized with the scrolling of this main table.
     * To be completed by inheritance.
     * @return {undefined}
     */
    synchronizeHorizontalScrollings() {
        console.log("warning, info scrolling not synchronized");
    }
    /**
     * Create the table from the specified \ :js:class:`MetaZone`\ .
     * @param  {MetaZone} zone    specified \ :js:class:`MetaZone`\ 
     * @param  {Object} options   additional settings to pass to the handsontable constructor. Usually undefined
     * @return {undefined}
     */
    createFromZone(zone, options) {
        this.selectedMetaZone = zone;
        this.zones = [zone];
        this.zoneIndex = 0;
        this.hot.selectedMetaZone = zone;
        var that = this;
        this.hot.readonly = zone.spec.readonly;
        this.colWidth = [];
        for(var j=0; j<this.selectedMetaZone.C; j++) {
            var width = 50;
            for(var i=0; i<this.selectedMetaZone.R; i++) {
                var MetaCell = this.selectedMetaZone.grid[i];
                if(MetaCell === undefined)
                    continue;
                MetaCell = MetaCell[j];
                if(MetaCell === undefined || MetaCell === null)
                    continue;
                if(MetaCell.isFirst() && (MetaCell.infos.position <= MetaCell.spec.decpos - 1)) {
                    width = 100;
                }
            }
            this.colWidth.push(width);
        }
        var settings = {
            rowHeaders: false,
            afterRender : () => (this.postRender()),
            afterChange : (edit, operation) => (this.afterChange(edit, operation)),
            afterSelection : (line1, col1, line2, col2, info, index) => (this.dataSelected(line1, col1, line2, col2, info, index)),
            //colHeaders : hot.nargs === 1,
            //beforeOnCellMouseDown : beforeOnCellMouseDown,
            //cells: cell_function,
            fixedRowsTop : 1, // freeze the first line
            fixedColumnsLeft : zone.spec.args[0].ncells, // freeze as many columns as there are cell to describe the first argument
            //viewportColumnRenderingOffset : 5,
            //viewportRowRenderingOffset : 5,
            afterScrollVertically : function(event) {
                that.synchronizeVerticalScrollings();
            },
            afterScrollHorizontally : function(event) {
                that.synchronizeHorizontalScrollings();
            },
            beforeKeyDown : (key) => (this.beforeKeyDown(key)),
            cells: (row, col, prop) => cellFunctionHot(this.hot, row, col, prop, this.colWidth),
            readOnly : zone.spec.readonly,
            afterCreateRow: function(index, amount){
                //that.hot.selectedMetaZone.data.splice(index, amount);
            },
            undo : false,
            redo: false,
            maxRows: this.selectedMetaZone.R,
            maxCols: this.selectedMetaZone.C,
            data: this.hot.selectedMetaZone.data,
            outsideClickDeselects: false
        };
        if(options !== undefined) {
            for(var key in options) {
                settings[key] = options[key];
            }
        }
        this.hot.updateSettings(settings);
        this.selectedMetaZone = zone;
        this.nargs = zone.spec.args.length;
    }
    /**
     * Get the list of currently selected cells
     * @return {Array}  list of the currently selected cells
     */
    getSelectedCells() {
        return this.selection.originalSelectedCells;
    }
    /**
     * Select the cells in the specfified selection
     * @param  {Array} selections  Selection of cells in the \ :js:class:`BoundingBoxes`\  format [ [x_0, y_0, x_1, y_1], … ]
     * @return {undefined}
     */
    selectCells(selections) {
        for(var i=0; i<selections.length; i++) {
            if(selections[i][0] >= this.selectedMetaZone.R)
                selections[i][0] = this.selectedMetaZone.R - 1;
            if(selections[i][2] >= this.selectedMetaZone.R)
                selections[i][2] = this.selectedMetaZone.R - 1;
            if(selections[i][1] >= this.selectedMetaZone.C)
                selections[i][1] = this.selectedMetaZone.C - 1;
            if(selections[i][3] >= this.selectedMetaZone.C)
                selections[i][3] = this.selectedMetaZone.C - 1;
        }
        this.hot.selectMultipleCell(selections);
    }
    /**
     * Select the cell at the specified position. **UNUSED**
     * @param  {Number} x  row of the cell
     * @param  {Number} y  column of the cell
     * @return {undefined}
     */
    selectCell(x, y) {
        this.hot.selectCell(x,y);
    }
    /**
     * Check whether the table is active or not (i.e. whether it is focused)
     * @return {Boolean}
     */
    isActive() {
        return this.hot.getSelected() !== undefined;
    }
    /**
     * Deselect all cells
     * @return {undefined}
     */
    deselectCell() {
        this.hot.deselectCell();
    }
    
    /* =========================================
                    Event callbacks
    ========================================= */
    
    /**
     * Action to perform just after rendering the table
     * @return {undefined}
     */
    postRender() {
        // Get the list of the superCells corresponding to the given selection (even partially selected)
        var cells = this.selectionToSuperCells();
        // Get the list of the metaCells corresponding to the given selection
        var mcells = this.selectionToMetaCells();

        if(mcells.length === 1)
            $('#textarea-critical-apparatus').val(mcells[0].props.critical_apparatus);
        else
            $('#textarea-critical-apparatus').val('');
        if(cells.length === 1)
            $('#textarea-commentary').val(cells[0].props.user_comment);
        else
            $('#textarea-commentary').val('');

        if(mcells.length === 1) {
            if(mcells[0].props.critical_apparatus !== "") {
                if(!this.commentaryFocused && !this.criticalFocused) {
                    if($('#bottom-right-zone li.active>a').attr('id') !== "tab-critical") {
                        $('#tab-critical').click();
                        this.showCommentary(cells[0], mcells);
                    }
                }
            }
            else {
                if((mcells[0].props.critical_apparatus == "" || mcells[0].props.critical_apparatus == undefined) && (cells[0].props.user_comment == "" || cells[0].props.user_comment == undefined)) {
                    if(!this.commentaryFocused && !this.criticalFocused)
                        if($('#bottom-right-zone li.active>a').attr('id') !== "tab-logs") {
                            $('#tab-logs').click();
                        }
                }
            }
        }
        if(cells.length === 1) {
            this.updateLog(cells[0].log());
        }
    }
    /**
     * Action to perform after a region selection is done in the table
     * @param  {Number} line1  line of the first cell of the area
     * @param  {Number} col1   column of the first cell of the area
     * @param  {Number} line2  line of the last cell of the area
     * @param  {Number} col2   column of the last cell of the area
     * @param  {Object} info   additonal informations. **UNUSED**
     * @param  {Number} index  in the case of a multi-area selection, give the index of the area (in the multi-selection)
     * @return {undefined}
     */
    dataSelected(line1, col1, line2, col2, info, index) {
        this.lockSuper = false;
        
        //backup of previous selection
        this.selection.previousNConnex = this.selection.nConnex;
        if(index === 0) { //on commence une nouvelle selection
            this.selection.previousSelectedCells = JSON.parse(JSON.stringify(this.selection.selectedCells));
            this.selection.previousOriginalSelectedCells = JSON.parse(JSON.stringify(this.selection.originalSelectedCells));
        }
        
        //hack with counter to know from where this selection comes from
        if(this.selection.previousSelectionModeCounter <= 0) {
            this.selection.previousSelectionMode = "none";
        }
        else
            this.selection.previousSelectionModeCounter -= 1;
        
        var originalSelectedCells = [line1, col1, line2, col2];
        if(col1 > col2) {
            var coll = col2;
            var colr = col1;
        }
        else {
            var coll = col1;
            var colr = col2;
        }
        if(line1 > line2) {
            var linel = line2;
            var liner = line1;
        }
        else {
            var linel = line1;
            var liner = line2;
        }
        var selectedCells = [linel, coll, liner, colr];
        
        this.selection.nConnex = index;
        if(this.selection.nConnex === this.selection.previousNConnex) {
            this.selection.selectedCells[index] = selectedCells;
            this.selection.originalSelectedCells[index] = originalSelectedCells;
        }
        else if(this.selection.nConnex > this.selection.previousNConnex) {
            this.selection.selectedCells.push(selectedCells);
            this.selection.originalSelectedCells.push(originalSelectedCells);
        }
        else {
            this.selection.selectedCells = [selectedCells];
            this.selection.originalSelectedCells = [originalSelectedCells];
        }
        
        if(this.isSelectionSuperSelect(this.selection.selectedCells))
            this.lockSuper = true;
        
        this.onSelectionChanged();
    }
    /**
     * Action to perform after a value is changed
     * @param  {Array}  edit       list of object describing the all the edits
     * @param  {String} operation  type of change made (usually "edit")
     * @return {undefined}
     */
    afterChange(edit, operation) {
        if(typeof debug !== "undefined" && debug)
            console.log(edit);
        var modified_scs = [];
        if(edit){
            for(var nedit=0;nedit<edit.length;nedit++) {
                var row = edit[nedit][0];
                var col = edit[nedit][1];
                var oldState = this.selectedMetaZone.grid[row][col].props["suggested"];
                var oldVal = edit[nedit][2];
                var val = edit[nedit][3];
                var valid = true;
                for(var i=0; i<val.length; i++) {
                    if(validSymbols.indexOf(val[i]) < 0) {
                        valid = false;
                        break;
                    }
                }
                //this.hot.selectedMetaZone.grid[row][col].onTouchValue(oldVal, val);
                if(valid)
                    this.hot.selectedMetaZone.grid[row][col].fill(val, true, false, false);
                else
                    this.hot.selectedMetaZone.grid[row][col].fill(oldVal, !oldState, false, false);
                //content, validated
                modified_scs.push(this.hot.selectedMetaZone.superGrid[row][col]);
            }
            modified_scs = new Set(modified_scs);
            modified_scs = Array.from(modified_scs);
            for(var i=0; i<modified_scs.length; i++) {
                var sc = modified_scs[i];
                sc.onTouchValue(true);
            }
            this.render();
        }
        if(typeof debug !== "undefined" && debug)
            console.log("end edit");
    }
    
}

