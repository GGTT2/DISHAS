/* eslint-disable */
if (false) {
    /**
     * This example show how to create a zone, divided in 4 sub-zones, each one
     * divided again in 16 cells.
     * @example
     * >> var main = new RootZone();
     * >> var quads = [];
     * >> for(var i=0; i<4; i++) {
     *     quads.push(new Zone());
     *     for(var l=0; l<4; l++) {
     *         for(var c=0; c<4; c++) {
     *             quads[i].addZone(new Cell(), new CartesianCoordinates(l, c));
     *         }
     *     }
     * }
     * >> main.addZone(quads[0], new CartesianCoordinates(0,0));
     * >> main.addZone(quads[1], new CartesianCoordinates(0,4));
     * >> main.addZone(quads[2], new CartesianCoordinates(4,0));
     * >> main.addZone(quads[3], new CartesianCoordinates(4,4));
     *
     * // fill info at the cell level, optimize access
     * >> main.setup();
     *
     * // now the table is usable
     * >> main.grid[2][3].val = 12;
     * // we can check that accessing the #11 cell of the #0 sub-zone is the
     * // same as accessing tbackhe cell at cartesian coordinates (2,3):
     * >> console.log(main.fromPath([0,11]) === main.grid[2][3]);
     * true
     * 
     * @type {Example}
     */
    var ExampleZone = undefined;
}

if(false) {
    /**
     * A cell can be accessed by its \ :js:class:`ZoneCoordinates`\ . It is
     * the sequence of the sub-zones to be accessed to get to the cell.
     * 
     * @example
     * // retrieve a given cell by its zone coordinates in a 2-arg table
     * >>  var cell = table.selectedMetaZone.zones[0].zones[2].zones[0].zones[1]; // == (entry, line #2, superCell #0, cell #1)
     * // retrieve a given cell by its zone coordinates in a 1-arg table
     * >>  var cell = table.selectedMetaZone.zones[0].zones[2].zones[1]; // == (entry, superCell #2, cell #1)
     *
     * @type {Object}
     */
    var ZoneCoordinates = undefined;
}

/* eslint-enable */

class CartesianCoordinates {
    /**
     * Class for representing cartesian coordinates in a table
     * 
     * @param  {Number} row
     * @param  {Number} col
     * @return {CartesianCoordinates}
     */
    constructor(row, col) {
        this.row = row;
        this.col = col;
    }
}


class Zone {
    /**
     * Class representing a zone in the table
     * 
     * @param  {Dictionary} infos   Dictionary of information. Shared with subzones.
     * @return {Zone}
     */
    constructor(infos) {
        if(infos === undefined)
            var infos = {};
        /**
         * List of subzones
         * @type {Array}
         */
        this.zones = [];
        /**
         * List of subzones positions
         * @type {Array}
         */
        this.zonesPositions = [];
        /**
         * Number of rows in this zone
         * @type {Number}
         */
        this.R = 0;
        /**
         * Number of columns in this zone
         * @type {Number}
         */
        this.C = 0;
        /**
         * Custom informations. Shared with subzones
         * @type {Object}
         */
        this.infos = infos;
    }
    /**
     * Vain attempt to free the memory when one wants to stop using this zone.
     * Not to be used as such.
     * 
     * @return {undefined}
     */
    destroy() {
        delete this.zones;
        delete this.zonesPositions;
        delete this.infos;
        delete this.parent;
    }
    /**
     * Recursive function checking if a given cell is inside this zone.
     * 
     * @param  {CartesianCoordinates} cartesian     relative coordinates of the cell inside this \ :js:class:`Zone`\ 
     * @return {Boolean}
     */
    hasInside(cartesian) {
        for(var i=0; i<this.zones.length; i++) {
            if(this.zones[i].hasInside(new CartesianCoordinates(
                cartesian.row - this.zonesPositions[i].row,
                cartesian.col - this.zonesPositions[i].col
            )))
                return true;
        }
        return false;
    }
    /**
     * Returns the subzone present at the given coordinates.
     * 
     * @param  {CartesianCoordinates} cartesian     relative coordinates of the cell inside this \ :js:class:`Zone`\ 
     * @return {Zone}
     */
    getZone(cartesian) {
        for(var i=0; i<this.zones.length; i++) {
            if(this.zones[i].hasInside(new CartesianCoordinates(
                cartesian.row - this.zonesPositions[i].row,
                cartesian.col - this.zonesPositions[i].col
            )))
                return this.zones[i];
        }
        return null;
    }
    /**
     * Returns the leaf of this zone at a given coordinates. This corresponds to the object of type \ :js:class:`Cell`\  present at those coordinates.
     * 
     * @param  {CartesianCoordinates} cartesian     relative coordinates of the cell inside this \ :js:class:`Zone`\ 
     * @return {Cell}
     */
    getLeaf(cartesian) {
        var zone = null;
        var index = 0;
        for(var i=0; i<this.zones.length; i++) {
            if(this.zones[i].hasInside(new CartesianCoordinates(
                cartesian.row - this.zonesPositions[i].row,
                cartesian.col - this.zonesPositions[i].col
            ))) {
                zone = this.zones[i];
                index = i;
            }
        }
        
        if(zone === null)
            return null;
        return zone.getLeaf(new CartesianCoordinates(
            cartesian.row - this.zonesPositions[index].row,
            cartesian.col - this.zonesPositions[index].col
        ));
    }
    /**
     * Method to add a sub-zone to this zone.
     * 
     * @param {Zone}                    zone        subzone to be added
     * @param {CartesianCoordinates}    position    relative position at which it will be added
     */
    addZone(zone, position) {
        this.zones.push(zone);
        this.zonesPositions.push(position);
        this.R = Math.max(this.R, this.zonesPositions[this.zonesPositions.length-1].row + this.zones[this.zones.length-1].R);
        this.C = Math.max(this.C, this.zonesPositions[this.zonesPositions.length-1].col + this.zones[this.zones.length-1].C);
        zone.parent = this;
        zone.indexInParent = this.zones.length - 1;
    }
    /**
     * Method to update dimensions of the zone.
     * Used by the removeZone method.
     * 
     * @return {undefined}
     */
    computeDimensions() {
        this.R = 0;
        this.C = 0;
        for(var i=0; i<this.zones.length; i++) {
            this.R = Math.max(this.R, this.zonesPositions[i].row + this.zones[i].R);
            this.C = Math.max(this.C, this.zonesPositions[i].col + this.zones[i].C);
        }
    }
    /**
     * Method to remove the subzone at a given index
     * 
     * @param  {Number}     index   The index of the subzone to be removed
     * @return {undefined}
     */
    removeZone(index) {
        this.zones.splice(index, 1);
        this.computeDimensions();
    }
    /**
     * Retrieve the subzone at given zone coordinates.
     * 
     * @param  {Array}  path    zone coordinates
     * @return {undefined}
     */
    fromPath(path) {
        if(path.length === 0)
            return this;
        return this.zones[path[0]].fromPath(path.slice(1));
    }
    
    /**
     * Informs the current zone of its absolute cartesian and zone coordinates. It will then compute and pass down the
     * absolute coordinates of its subzones.
     * Used by the setup method of the \ :js:class:`RootZone`\ .
     * 
     * @param  {CartesianCoordinates}   cartesian
     * @param  {Array}                  zoneCoordinates
     * @return {undefined}
     */
    fillInfos(cartesian, zoneCoordinates) {
        this.row = cartesian.row;
        this.col = cartesian.col;
        this.zoneCoordinates = zoneCoordinates;
        for(var i=0; i<this.zones.length; i++) {
            for(var key in this.infos) {
                this.zones[i].infos[key] = this.infos[key];
            }
            var newZoneCoordinates = JSON.parse(JSON.stringify(zoneCoordinates));
            newZoneCoordinates.push(i);
            this.zones[i].fillInfos(new CartesianCoordinates(cartesian.row+this.zonesPositions[i].row,cartesian.col+this.zonesPositions[i].col), newZoneCoordinates);
        }
    }
}


class RootZone extends Zone {
    /**
     * Class representing the root zone of the table.
     * 
     * @param  {Object} infos   Dictionnary of informations. Shared with subzones.
     * @return {RootZone}
     */
    constructor(infos) {
        super(infos);
        /**
         * We store a reference to this main zone in the information dictionnary, so that every subzone will have access to the root zone if needed.
         * @type {RootZone}
         */
        this.infos.root = this;
    }
    /**
     * Vain attempt to free the memory when one wants to stop using this zone.
     * Not to be used as such.
     * 
     * @return {undefined}
     */
    destroy() {
        if(this.grid == undefined)
            return;
        for(var i=0; i<this.R; i++) {
            for(var j=0; j<this.C; j++) {
                if(this.grid[i][j] !== null)
                    this.grid[i][j].destroy();
                delete this.grid[i][j];
            }
        }
        delete this.grid;
        delete this.superGrid;
    }
    /**
     * Create two-dimensional arrays (grids) containing references to \ :js:class:`Cell`\ s that will allow to access a leaf (object of type \ :js:class:`Cell`\ )
     * at given cartesian coordinates. It is an optimization preventing the use of the slow recursive function "getLeaf".
     * Used by the setup method, which will fill these grids with the correct references thanks to the getLeaf method.
     * 
     * @return {undefined}
     */
    buildGrid() {
        // TODO find a way to call it in the constructor.
        /**
         * Two dimensional array allowing to access the \ :js:class:`Cell`\  at given cartesian coordinates.
         * Will be filled by the setup method.
         *
         * @example
         * >> var cell_in_coordinates_17_3 = main_zone.grid[17][3];                                // fast
         * >> var cell_in_coordinates_17_3_bis = main_zone.getLeaf(CartesianCoordinates(17,3));    // slow
         * >> console.log(cell_in_coordinates_17_3 === cell_in_coordinates_17_3_bis)
         * true
         * 
         * @type {Array}
         */
        this.grid = [];
        /**
         * Two dimensional array allowing to access the SuperCell at given cartesian coordinates
         * @example
         * >> var SuperCell_in_coordinates_17_3 = main_zone.superGrid[17][3];                                  // fast
         * >> var SuperCell_in_coordinates_17_3_bis = main_zone.getLeaf(CartesianCoordinates(17,3)).parent;    // slow
         * >> console.log(SuperCell_in_coordinates_17_3 === SuperCell_in_coordinates_17_3_bis)
         * true
         * 
         * @type {Array}
         */
        this.superGrid = [];
        /**
         * Array that will be used by handsontable to store the numbers in the table. Do not modify by hand
         * @type {Array}
         */
        this.data = [];

        for(var i=0; i<this.R; i++) {
            this.grid.push([]);
            this.superGrid.push([]);
            this.data.push([]);
            for(var j=0; j<this.C; j++) {
                this.grid[i].push(null);
                this.superGrid[i].push(null);
                this.data[i].push("");
            }
        }
    }
    /**
     * Method to call right after creation, in order to compute grids and initialize infos in subzones.
     * 
     * @return {undefined}
     */
    setup() {
        // TODO find a way to call it in the constructor.
        this.computeDimensions();
        this.buildGrid();
        this.fillInfos(new CartesianCoordinates(0,0), []);
    }
}


class Cell extends Zone {
    /**
     * Represents a cell in the table (a leaf in the hierarchical tree). It cannot have any subzones,
     * and its size must be (1,1).
     * 
     * @param  {Object} infos   Dictionnary of informations
     * @return {Cell}
     */
    constructor(infos) {
        super(infos);
        this.R = 1;
        this.C = 1;
        /**
         * The value held by this cell. Must be synchronized with this.infos.root.data[this.row][this.col]
         * @type {String}
         */
        this.val = "";

        /**
         * Row number of this cell (part of \ :js:class:`CartesianCoordinates`\ )
         * @type {Number}
         */
        this.row = undefined;
        /**
         * Column number of this cell (part of \ :js:class:`CartesianCoordinates`\ )
         * @type {Number}
         */
        this.col = undefined;
        /**
         * \ :js:class:`ZoneCoordinates`\  of this cell.
         * @type {Number}
         */
        this.zoneCoordinates = undefined;
    }
    /**
     * End of the recusive call hasInside
     * 
     * @param  {CartesianCoordinates} cartesain
     * @return {Boolean}
     */
    hasInside(cartesian) {
        if(cartesian.row === 0 && cartesian.col === 0) {
            return true;
        }
        return false;
    }
    /**
     * End of the recurive call getLeaf
     * 
     * @param  {CartesianCoordinates} cartesian
     * @return {this}
     */
    getLeaf(cartesian) {
        if(cartesian.row === 0 && cartesian.col === 0) {
            return this;
        }
        return null;
    }
    /**
     * A leaf \ :js:class:`Cell`\  cannot have subzones
     * 
     * @param  {CartesianCoordinates} cartesian
     * @return {undefined}
     */
    getZone(cartesian) {
        throw "calling getZone on a Cell object: FORBIDDEN!";
    }
    /**
     * A leaf \ :js:class:`Cell`\  cannot have subzones
     * 
     * @param {CartesianCoordinates} cartesian
     */
    addZone(cartesian) {
        throw "calling addZone on a Cell object: FORBIDDEN!";
    }
    
    /**
     * End of the recusive call "fillInfos".
     * We also fill the uninitialized arrays of the root zone (\ :js:class:`RootZone`\ )
     * 
     * @param  {CartesianCoordinates} cartesian
     * @param  {Array} zoneCoordinates
     * @return {undefined}
     */
    fillInfos(cartesian, zoneCoordinates) {
        this.row = cartesian.row;
        this.col = cartesian.col;
        this.zoneCoordinates = zoneCoordinates;
        this.infos.root.grid[this.row][this.col] = this;
        this.infos.root.superGrid[this.row][this.col] = this.parent;
    }
}

