/* global $ */
/* global selectionKeyBindings */
/* global flatten */

selectionKeyBindings = {
    superSelect: (key) => (key.code === "Space" && key.shiftKey),
    columnSelect: (key) => ((key.code === "KeyW" || key.code === "KeyS") && key.shiftKey),
    lineSelect: (key) => ((key.code === "KeyA" || key.code === "KeyD") && key.shiftKey)
};

if (false) {
    /**
     * When talking about a selection in a table, we want to describe a multi-selection
     * For that, we use a list of bounding box 
     * (each bounding box corresponding to one of the selection).
     * A bounding box is described as a list of 4 integer:
     * [row_top_left, col_top_left, row_bottom_right, col_bottom_right]
     * 
     * @example
     * // a multi-selection with 2 selections
     * >> double_selection
     * [
     *     [1, 2, 3, 7],
     *     [10, 11, 22, 13]
     * ]
     *
     * @type {Object}
     */
    BoundingBoxes = [
        [x_0_top_left, y_0_top_left, x_0_bottom_right, y_0_bottom_right],
        // ...
        [x_n_top_left, y_n_top_left, x_n_bottom_right, y_n_bottom_right]
    ];
}


class TableInterface {
    /**
     * Virtual class for a graphic table.
     * It contains selection and cursor movement methods,
     * as well as graphic methods to be implemented by inheritance.
     * @param  {String} containerId  DOM id of the div that will contain the table
     * @return {TableInterface}
     */
    constructor(containerId) {
        if(this.constructor == TableInterface) {
            throw "TableInterface is virtual. Can't instantiate it.";
        }
        /**
         * DOM id of the div that will contain the table
         * @type {String}
         */
        this.containerId = containerId;
        /**
         * List of vertical information tables
         * @type {Array}
         */
        this.verticalInformationTables = [];
        /**
         * List of horizontal information tables
         * @type {Array}
         */
        this.horizontalInformationTables = [];

        /**
         * Dictionary of \ :js:class:`ComputationZone`\ s to be computed 
         * automatically when a value is changed.
         * Currently contains fisrt and second differences.
         * @type {Object}
         */
        this.mathZones = {};
        /**
         * Boolean specifying whether to keep the selection at a SuperCell level or not
         * @type {Boolean}
         */
        this.lockSuper = false;
        /**
         * Boolean specifying whether to raise a warning based on 1st differences or not.
         * **Currently UNUSED**
         * @type {Boolean}
         */
        this.diff1Warning = false;
        /**
         * Boolean specifying whether to raise a warning based on 2nd differences or not.
         * **Currently UNUSED**
         * @type {Boolean}
         */
        this.diff2Warning = false;
        /**
         * List of \ :js:class:`SuperArea`\ s previews to clean
         * (i.e. cells highlighted when visualizing forward or in-between
         * interpolation).
         * @type {Array}
         */
        this.areasToClean = [];
    }
    /**
     * Initialize this table from a \ :js:class:`RootZone`\  object.
     * To be implemented by inheritance
     * @param  {RootZone} zone  \ :js:class:`RootZone`\  of the table
     * @return {undefined}
     */
    createFromZone(zone) {
        throw "createFromZone: Not implemented";
    }
    /**
     * Print the table on screen. To be called when a modification is made
     * and the graphic table is not up to date.
     * To be implemented by inheritance
     * @return {undefined}
     */
    render() {
        throw "render: Not implemented";
    }
    /**
     * Fill the cell with a given value to the specified cartesian coordinates.
     * To be implemented by inheritance
     * @param  {Number} row    row of the \ :js:class:`MetaCell`\ 
     * @param  {Number} col    column of the \ :js:class:`MetaCell`\ 
     * @param  {String} value  Value to be put in the \ :js:class:`MetaCell`\ 
     * @return {undefined}
     */
    fillCell(row, col, value) {
        throw "fillCell: Not implemented";
    }
    /**
     * Select the cells in the specfified selection. 
     * To be implemented by inheritance
     * @param  {Array} selection_list  Selection of cells in the \ :js:class:`BoundingBoxes`\  format [ [x_0, y_0, x_1, y_1], ... ]
     * @return {undefined}
     */
    selectCells(selection_list) {
        throw "selectCell: Not implemented";
    }
    /**
     * Get the list of currently selected cells,
     * in the \ :js:class:`BoundingBoxes`\  format [ [x_0, y_0, x_1, y_1], ... ].
     * To be implemented by inheritance
     * @return {Array}  Array of bounding boxes
     */
    getSelectedCells() {
        throw "getSelectedCells: Not implemented";
    }
    /**
     * Get the principal selected cell's cartesian coordinates
     * @return {Array}  Cartesian coordinates
     */
    getSelectedCell() {
        var selection = this.getSelectedCells();
        return [selection[selection.length-1][0], selection[selection.length-1][1]];
    }
    /**
     * Get the value in the \ :js:class:`MetaCell`\  at cartesian coordinates (row, col). 
     * To be implemented by inheritance
     * @param  {Number} row  row of the \ :js:class:`MetaCell`\ 
     * @param  {Number} col  column of the \ :js:class:`MetaCell`\ 
     * @return {String}
     */
    getValue(row, col) {
        throw "getValue: Not implemented";
    }
    /**
     * Check whether the table is active or not (i.e. whether it is focused). 
     * To be implemented by inheritance
     * @return {Boolean}
     */
    isActive() {
        throw "isActive: Not implemented";
    }
    /**
     * Deselect all cells. To be implemented by inheritance
     * @return {undefined}
     */
    deselectCell() {
        throw "deselectCell: Not implemented";
    }
    /**
     * Refocus the table (by clicking on it)
     * @return {undefined}
     */
    refocus() {
        $(this.containerId).click();
        this.selectCells(this.getSelectedCells());
    }
    /**
     * Function doing nothing
     * @return {undefined}
     */
    nop() {}
    /**
     * Move the selection down
     * @return {undefined}
     */
    moveDown() {
        var lockState = this.lockSuper;
        var originalSelectedCells = this.getSelectedCells();
        this.moveSelectionsVertically(originalSelectedCells, 1);
        this.selectCells(originalSelectedCells);
        if(lockState && !this.isSelectionSuperSelect(this.getSelectedCells()))
            this.superSelect();
    }
    /**
     * Move the selection up
     * @return {undefined}
     */
    moveUp() {
        var lockState = this.lockSuper;
        var originalSelectedCells = this.getSelectedCells();
        this.moveSelectionsVertically(originalSelectedCells, -1);
        this.selectCells(originalSelectedCells);                
        if(lockState && !this.isSelectionSuperSelect(this.getSelectedCells()))
            this.superSelect();
    }
    /**
     * Move the selection right
     * @return {undefined}
     */
    moveRight() {
        var lockState = this.lockSuper;
        var originalSelectedCells = this.getSelectedCells();
        if(lockState)
            this.moveSelectionsNextSuperCell(originalSelectedCells, 1);
        else
            this.moveSelectionsHorizontally(originalSelectedCells, 1);
        this.selectCells(originalSelectedCells);
        if(lockState)
            this.superSelect();
    }
    /**
     * Move the selection left
     * @return {undefined}
     */
    moveLeft() {
        var lockState = this.lockSuper;
        var originalSelectedCells = this.getSelectedCells();
        if(lockState)
            this.moveSelectionsNextSuperCell(originalSelectedCells, -1);
        else
            this.moveSelectionsHorizontally(originalSelectedCells, -1);
        this.selectCells(originalSelectedCells);
        if(lockState)
            this.superSelect();
    }
    /**
     * Callback function for selection movement keys. 
     * To be given as a callback to the key listener
     * @param  {Object} key  key event object
     * @return {undefined}
     */
    beforeKeyDown(key) {
        switch(key.key) {
        case "Escape":
            var current = this.getSelectedCell();
            this.selectCell(current[0], current[1]);
            break;
        case "ArrowUp":
            if(key.shiftKey) {
                break;
            }
            this.moveUp();
            key.stopImmediatePropagation();
            break;
        case "ArrowRight":
            if(key.shiftKey) {
                break;
            }
            this.moveRight();
            key.stopImmediatePropagation();
            break;
        case "ArrowDown":
            if(key.shiftKey) {
                break;
            }
            this.moveDown();
            key.stopImmediatePropagation();
            break;
        case "ArrowLeft":
            if(key.shiftKey) {
                break;
            }
            this.moveLeft();
            key.stopImmediatePropagation();
            break;
        default:
            break;
        }
        for(var func in selectionKeyBindings) {
            if(selectionKeyBindings[func](key) && !key.ctrlKey) {
                this[func]();
                key.stopImmediatePropagation();
            }
        }
    }
    /**
     * Checks whether a selection corresponds entirely to a "super selection" or not
     * @param  {Array}   selections  Selection of cells, in the \ :js:class:`BoundingBoxes`\  format [ [x_0, y_0, x_1, y_1], ..., ]
     * @param  {Number}  [index=0]   Index of the considered zone
     * @return {Boolean}             false if \ :js:class:`SuperCell`\ s are only partially selected
     */
    isSelectionSuperSelect(selections, index) {
        if(selections === undefined)
            var selections = this.getSelectedCells();
        if(index === undefined)
            var index = this.zoneIndex;
        return this.zones[index].isSelectionSuperSelect(selections);
    }
    /**
     * Returns the list of \ :js:class:`SuperCell`\ s corresponding to the given selection
     * @param  {Array}   selection   Selection of cells, in the \ :js:class:`BoundingBoxes`\  format [ [x_0, y_0, x_1, y_1], ..., ]
     * @param  {Number}  [index=0]   Index of the considered zone
     * @return {Array}           corresponding \ :js:class:`SuperCell`\ s
     */
    selectionToSuperCells(selections, index) {
        if(selections === undefined)
            var selections = this.getSelectedCells();
        if(index === undefined)
            var index = this.zoneIndex;
        return this.zones[index].selectionToSuperCells(selections);
    }

    /**
     * Returns the list of \ :js:class:`MetaCell`\ s corresponding to the given selection
     * @param  {Array}   selection   Selection of cells, in the \ :js:class:`BoundingBoxes`\  format [ [x_0, y_0, x_1, y_1], ..., ]
     * @param  {Number}  [index=0]   Index of the considered zone
     * @return {Array}           corresponding \ :js:class:`MetaCell`\ s
     */
    selectionToMetaCells(selections, index) {
        if(selections === undefined)
            var selections = this.getSelectedCells();
        if(index === undefined)
            var index = this.zoneIndex;
        return this.zones[index].selectionToMetaCells(selections);
    }
    
    /**
     * Translates a selection vertically with the given step.
     * The selection is modified in-place.
     * @param  {Array}   selections  Selection of cells, in the \ :js:class:`BoundingBoxes`\  format [ [x_0, y_0, x_1, y_1], ..., ]
     * @param  {Number}  delta       Step of the translation. The sign specifies the direction
     * @param  {Number}  [index=0]   Index of the considered zone
     * @return {undefined}
     */
    moveSelectionsVertically(selections, delta, index) {
        if(selections === undefined)
            var selections = this.getSelectedCells();
        if(index === undefined)
            var index = this.zoneIndex;
        return this.zones[index].moveSelectionsVertically(selections, delta);
    }
    /**
     * Translates a selection vertically with the given step.
     * The selection is modified in-place.
     * @param  {Array}   selections  Selection of cells, in the \ :js:class:`BoundingBoxes`\  format [ [x_0, y_0, x_1, y_1], ..., ]
     * @param  {Number}  delta       Step of the translation. The sign specifies the direction
     * @param  {Number}  [index=0]   Index of the considered zone
     * @return {undefined}
     */
    moveSelectionsHorizontally(selections, delta, index) {
        if(selections === undefined)
            var selections = this.getSelectedCells();
        if(index === undefined)
            var index = this.zoneIndex;
        return this.zones[index].moveSelectionsHorizontally(selections, delta);
    }
    /**
     * Translates a selection horizontally along the second argument.
     * The selection is modified in-place.
     * @param  {Array}   selections  Selection of cells, in the \ :js:class:`BoundingBoxes`\  format [ [x_0, y_0, x_1, y_1], ..., ]
     * @param  {Number}  delta       Step of the translation. The sign specifies the direction
     * @param  {Number}  [index=0]   Index of the considered zone
     * @return {undefined}
     */
    moveSelectionsNextSuperCell(selections, delta, index) {
        if(selections === undefined)
            var selections = this.getSelectedCells();
        if(index === undefined)
            var index = this.zoneIndex;
        return this.zones[index].moveSelectionsNextSuperCell(selections, delta);
    }
    /**
     * Broaden the selection at \ :js:class:`SuperCell`\  level.
     * The selection is modified in-place.
     * @param  {Array}   selections_original  Selection of cells, in the \ :js:class:`BoundingBoxes`\  format [ [x_0, y_0, x_1, y_1], ..., ]
     * @param  {Number}  [index=0]            Index of the considered zone
     * @return {undefined}
     */
    superSelect(selections, index) {
        if(selections === undefined)
            var selections = this.getSelectedCells();
        if(index === undefined)
            var index = this.zoneIndex;
        var res = this.zones[index].superSelect(selections);
        this.selectCells(res);
    }
    /**
     * Expands the selection to the whole column (strictly speaking along the second argument arg1).
     * The selection is **NOT** modified in-place, but a new selection is returned.
     * @param  {Array}   selections_original  Selection of cells, in the \ :js:class:`BoundingBoxes`\  format [ [x_0, y_0, x_1, y_1], ..., ]
     * @param  {Number}  [index=0]            Index of the considered zone
     * @return {Array}                     Result of the expansion, in the \ :js:class:`BoundingBoxes`\  format [ [x_0, y_0, x_1, y_1], ..., ]
     */
    columnSelect(selections, index) {
        if(selections === undefined)
            var selections = this.getSelectedCells();
        if(index === undefined)
            var index = this.zoneIndex;
        var res = this.zones[index].columnSelect(selections);
        this.selectCells(res);
    }
    /**
     * Expands the selection to the whole column (strictly speaking along the first argument arg0).
     * The selection is **NOT** modified in-place, but a new selection is returned.
     * @param  {Array}   selections_original  Selection of cells, in the \ :js:class:`BoundingBoxes`\  format [ [x_0, y_0, x_1, y_1], ..., ]
     * @param  {Number}  [index=0]            Index of the considered zone
     * @return {Array}                     Result of the expansion, in the \ :js:class:`BoundingBoxes`\  format [ [x_0, y_0, x_1, y_1], ..., ]
     */
    lineSelect(selections, index) {
        if(selections === undefined)
            var selections = this.getSelectedCells();
        if(index === undefined)
            var index = this.zoneIndex;
        var res = this.zones[index].lineSelect(selections);
        this.selectCells(res);
    }
}
