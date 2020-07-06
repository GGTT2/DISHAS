class InfoTableInterface extends TableInterface {
    /**
     * Virtual class for a graphic information table (e.g. first or second differences). 
     * Inherits from \ :js:class:`TableInterface`\ 
     * @param  {String} containerId   DOM id of the div that will contain the table
     * @param  {Boolean} vertical     Whether this info table is vertical or horizontal
     * @param  {Boolean} readonly
     * @return {InfoTableInterface}
     */
    constructor(containerId, vertical, readonly) {
        super(containerId);
        /**
         * Boolean specifying whether this table is vertical or horizontal
         * @type {Boolean}
         */
        this.vertical = vertical;
        /**
         * Boolean specifying if the table is read only.
         * Unused for information tables (they are necessarily readonly)
         * @type {Boolean}
         */
        this.readonly = readonly;
        /**
         * Boolean specifying if the table has been changed.
         * @type {Boolean}
         */
        this.updatedOnce = false;
    }
    /**
     * Perform the specified action on every DOM cell of the table.
     * To be implemented by inheritance.
     * @param  {function} action  An action to perform on each DOM cell. It is a function taking a DOM element, a row and a line as parameters.
     * @return {undefined}
     */
    iterateDomCells(action) {
        throw "To be implemented in child class";
    }
    /**
     * Initialize this table from a graphicZone (the \ :js:class:`RootZone`\  of this table)
     * @param  {MetaZone} graphicZone  \ :js:class:`RootZone`\  of this table
     * @param  {MetaZone} mathZone     Intermediary zone where the computation is performed, before beeing transfered
     *                                 to the graphicZone
     * @return {undefined}
     */
    createFromZones(graphicZone, mathZone) {
        this.createFromZone(graphicZone);
        /**
         * Intermediary zone in which the computation is stored. The template of this 
         * zone is the same as the astronomical table in the interface
         * @type {MetaZone}
         */
        this.mathZone = mathZone;
        /**
         * This is the zone that will be displayed. In the case of a 2-arg astronomical table, this zone is
         * still a 1-arg zone, corresponding to a view on the mathZone.
         * @type {MetaZone}
         */
        this.graphicZone = graphicZone;
        /**
         * Alias of graphicZone
         * @type {MetaZone}
         */
        this.selectedMetaZone = graphicZone;

        /**
         * Will contain the list of \ :js:class:`SuperCell`\ s
         * to be highlighted accordingly to the cursor on
         * the main table
         * @type {Array}
         */
        this.highlights = [];
    }
    /**
     * Remove all the highlights
     * (see \ :js:attr:`InfoTableInterface.highlights`\ )
     * @return {undefined}
     */
    removeHighlight() {
        var hot = this;
        for(var sc of this.highlights) {
            sc.setProp("highlight_zone", false);
            this.iterateDomCells(function(dom, index, index2) {
                if(hot.graphicZone.grid[index][index2] === null)
                    return;
                if(hot.graphicZone.grid[index][index2].parent === sc) {
                    $(dom).removeClass("highlight_zone");
                }
            });
        }
        this.highlights = [];
    }
    /**
     * Update the highlight zone of this information table
     * (see \ :js:attr:`InfoTableInterface.highlights`\ )
     * @param  {Array} argpos  argument index(es) of the selected cell (in the format [arg0, arg1]) in the main table
     * @return {undefined}
     */
    updateHighlight(argpos) {
        var hot = this;
        this.removeHighlight();
        var new_sc = this.graphicZone.zones[0].zones[argpos];
        this.highlights.push(new_sc);
        new_sc.setProp("highlight_zone", true);
        this.iterateDomCells(function(dom, index, index2) {
            if(hot.graphicZone.grid[index][index2] === null)
                    return;
            if(hot.graphicZone.grid[index][index2].parent === new_sc) {
                $(dom).addClass("highlight_zone");
            }
        });
    }
    /**
     * Update the graphicZone of this table, i.e transfert values from the mathZone to the graphicZone. 
     * The argpos parameter is usefull in the 2-arg case, where the graphicZone is only a restricted 1-arg view 
     * of the mathZone which depends on the currently selected cell.
     * @param  {Array} argpos  argument index(es) of the selected cell (in the format [arg0, arg1]).
     * @return {undefined}
     */
    updateGraphicZone(argpos) {
        if(this.readonly && this.updatedOnce)
            return ;
        var argPosTemp = JSON.parse(JSON.stringify(argpos));
        
        if(this.vertical) {
            for(var index=0; index<this.graphicZone.zones.length; index++) {
                if(index == 0 || (index > 0 && !this.graphicZone.infos.show_args)) {
                    for(var argpos0=0; argpos0<this.mathZone.spec.args[0].nsteps; argpos0++) {
                        argPosTemp[0] = argpos0;
                        if(this.mathZone.fromPath(flatten([index,0,argPosTemp])).isComplete()) {
                            this.graphicZone.zones[index].zones[argpos0].setSmartNumber(
                                this.mathZone.fromPath(flatten([index,0,argPosTemp])).getSmartNumber()
                            );
                        }
                        else {
                            this.graphicZone.zones[index].zones[argpos0].erase();
                        }
                    }
                }
                else { // this part is for updating the top argument part in the case of 2-args tables
                    if(this.mathZone.fromPath(flatten([index,0,argPosTemp])).isComplete()) {
                        this.graphicZone.zones[index].setSmartNumber(
                            this.mathZone.zones[2].zones[argpos[1]].getSmartNumber()
                        );
                    }
                    else {
                        this.graphicZone.zones[index].erase();
                    }
                }
            }
        }
        else { // TODO for horizontal
            for(var argpos1=0; argpos1<this.mathZone.spec.args[1].nsteps; argpos1++) {
                argPosTemp[1] = argpos1;
                if(this.mathZone.fromPath(flatten([0,0,argPosTemp])).isComplete())
                    this.graphicZone.fromPath([0,argpos1]).setSmartNumber(
                        this.mathZone.fromPath(flatten([0,0,argPosTemp])).getSmartNumber()
                    );
                else {
                    this.graphicZone.zones[0].zones[argpos1].erase();
                }
            }
            if(this.graphicZone.infos.show_args) { // this is the code for updating the left argument part in the case of 2-args table
                if(this.mathZone.fromPath(flatten([1,0,argPosTemp])).isComplete())
                    this.graphicZone.zones[1].setSmartNumber(
                        this.mathZone.fromPath(flatten([1,0,argPosTemp])).getSmartNumber()
                    );
                else {
                    this.graphicZone.zones[1].erase();
                }
            }
        }
        this.updatedOnce = true;
    }
}
