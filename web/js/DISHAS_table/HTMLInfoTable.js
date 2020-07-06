/* global $ */
/* global InfoTableInterface */

/*
 * Implementation of a simple HTML table for InfoTable
 */

class InfoHTMLTable extends InfoTableInterface {
    /**
	 * Implementation of a HTML information table
	 * @param  {String}    containerId  DOM id of the div that will contain the table
	 * @param  {Boolean}   vertical     Wether this info table is vertical or horizontal
	 * @return {InfoHTMLTable}
	 */
    constructor(containerId, vertical) {
        super(containerId, vertical);
        /**
         * The DOM id of the container of this table
         * @type {String}
         */
        this.containerId = containerId;
        /**
         * The data of the table (a 2 dimensional array)
         * @type {Array}
         */
        this.data = [];
        /**
         * The number of rows of this table
         * @type {Number}
         */
        this.R = 0;
        /**
         * The number of columns of this table
         * @type {Number}
         */
        this.C = 0;
    }
    
    /**
     * Create the information table from the specified \ :js:class:`MetaZone`\ .
     * @param  {MetaZone} zone   \ :js:class:`MetaZone`\  that will be represented by this graphic table
     * @param  {Object} options  dictionary of options. UNUSED for now
     * @return {undefined}
     */
    createFromZone(zone, options) {
        this.R = zone.R;
        this.C = zone.C;
        
        this.data = zone.data;
        this.nargs = 1;
        
        var tableString = "<table style=\"width:" + (50*this.C) + "px\">";
        
        for(var l=0; l<this.R; l++) {
            tableString += "<tr>";
            for(var c=0; c<this.C; c++) {
                tableString += "<td class=\"htmlCell" + zone.cssGrid[l][c] + "\">";
                tableString += "";
                tableString += "</td>";
            }
            tableString += "</tr>";
        }
        tableString += "</table>";
        $("#"+this.containerId).append(tableString);
        $("#"+this.containerId).parent().width(50*this.C + 35);
        $("#"+this.containerId).width(50*this.C + 50);
        
        //bloc scrolling for information tables
        $("#"+this.containerId).on("wheel", function(){ return false; });
        
    }

    /**
     * Perform the specified action on every DOM cell of the table.
     * @param  {function} action  An action to perform on each DOM cell. It is a function taking a DOM element, a row and a line as parameters.
     * @return {undefined}
     */
    iterateDomCells(action) {
        var hot = this;
        $("#" + this.containerId + " tr").each(function(index) {
            $("td", this).each(function(index2) {
                action($(this), index, index2);
            });
        });
    }
    
    /**
     * Render the table, i.e. update the graphic elements so that they reflect the changes made in the \ :js:class:`MetaZone`\ .
     * @return {undefined}
     */
    render() {
        var hot = this;
        $("#" + this.containerId + " tr").each(function(index) {
            $("td", this).each(function(index2) {
                $(this).html(hot.data[index][index2]);
            });
            
        });
    }
    
    /**
     * Call this method to free the memory associated with the table. UNUSED
     * @return {undefined}
     */
    destroy() {
    }
}

