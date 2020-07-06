/* global RootZone */
/* global SmartNumber */
/* global NView */
/* global nameToBase */
/* global Cell */
/* global posInBase */
/* global Zone */
/* global forWrapper */


class MetaCell extends Cell {
    /**
     * This class extends the concept of \ :js:class:`Cell`\  for our needs in the DISHAS interface.
     * It's role is to add properties (meta-data) and operations at the cell level.
     *
     * @param  {Object} spec    Template information at the \ :js:class:`MetaCell`\  and \ :js:class:`SuperCell`\  level
     * @param  {Object} infos   Dictionary of information. Shared with subzones.
     * @return {MetaCell}
     */
    constructor(spec, infos) {
        super(infos);
        /**
         * Infos contains information about the zone ancestors.
         * Not very useful for \ :js:class:`MetaZone`\ s, but useful for
         * \ :js:class:`SuperCell`\ s and \ :js:class:`MetaCell`\ s.
         * @example
         * {
         *     hot: ...,      // The table containing this SuperCell (and its parents)
         *     root: ...,     // The root in the Zone hierarchy
         *     index: ...,    // 0 for arg1 and entry, 1 for arg2 -> cannot remember why ??
         *     type: ...,     // 0 for entry, 1 for argument, 2 for information (2 is used only for SuperCels in informationTables)
         *     argPos: ...,   // In the case of an entry SuperCell, index(es) of the corresponding arguments
         *     position: ..., // Position (order) of this Cell in its SuperCell. this.infos.position == this.positionInParent
         * }
         *
         * @type {Object}
         */
        this.infos;
        /**
         * spec is the sub-template of the parent \ :js:class:`SuperCell`\ , stored as a dictionary.
         * @example
         * {
         *     decpos: 1,           // number of integer places in the parent SuperCell
         *     name: "Entry",       // name of the corresponding argument or "Entry"
         *     ncells: 4,           // total number of places in the parent SuperCell
         *     type: "sexagesimal"  // TypeOfNumber used by the parent SuperCell
         * }
         *
         * @type {Object}
         */
        this.spec = spec;
        /**
         * props contains all the properties at the cell level
         * @example
         * {
         *     suggested: true,
         *     error : false,
         *     error_str : '',
         *     source : false,
         *     target : false,
         *     commentary_zone : false
         * }
         *
         * @type {Object}
         */
        this.props = {
            suggested: true,
            error : false,
            error_str : "",
            source : false,
            target : false,
            commentary_zone : false,
            highlight_zone : false,
            critical_apparatus : "",
            edition_value : {}
        };
        /**
         * val contains the value stored in this cell.
         * It is stored as a string. It must be synchronized with this.infos.root.data
         * @type {String}
         */
        this.val = "";

        var indexInBase = posInBase(nameToBase[this.spec.type], this.infos.position, this.spec.decpos);
        if (indexInBase[1] < 0)
            indexInBase[1] = 0;
        if (indexInBase[0] === 1 && indexInBase[1] >= nameToBase[this.spec.type][1].length)
            indexInBase[1] = nameToBase[this.spec.type][1].length - 1;

        /**
         * radix indicates the maximum number one can put in this cell, based on the \ :js:attr:`TypeOfNumber`\  used.
         * @type {Number}
         */
        this.radix = nameToBase[this.spec.type][indexInBase[0]][indexInBase[1]];
        if(this.radix === undefined) {
            var clist = nameToBase[this.spec.type][indexInBase[0]];
            this.radix = clist[clist.length - 1];
        }
        /**
         * Compute from \ :js:attr:`radix`\  the maximum length of the string one can store in this cell.
         * @type {Number}
         */
        this.ndigit = Math.ceil(Math.log10(this.radix));
        /**
         * filler is the string that will be displayed in the console when the cell is left empty.
         * @type {String}
         */
        this.filler = "";
        for(var i=0; i<this.ndigit; i++) {
            this.filler += "-";
        }
    }
    /**
     * Return the nth sibling \ :js:class:`MetaCell`\  along the specified argument (i.e. along the first argument direction or second
     * argument direction).
     *
     * @param  {Number} arg   Specify the argument on which the search is performed
     * @param  {Number} delta Specify which sibbling (step)
     * @return {MetaCell}     The nth next MetaCell along the specified argument
     */
    nextArg(arg, delta) { // rename: getSiblingAlongArg()
        return this.parent.nextArg(arg, delta).zones[this.indexInParent];
    }
    /**
     * Return true if this \ :js:class:`MetaCell`\  is an entry
     * @return {Boolean} true if this \ :js:class:`MetaCell`\  is an entry
     */
    isEntry() {
        return this.infos.type === 0;
    }
    /**
     * Return true if this \ :js:class:`MetaCell`\  is an argument
     * @return {Boolean} true if this \ :js:class:`MetaCell`\  is an argument
     */
    isArgument() {
        return this.infos.type === 1;
    }
    /**
     * Return true if this \ :js:class:`MetaCell`\  is an info cell //TODO à préciser
     * @return {Boolean} true if this \ :js:class:`MetaCell`\  is an info cell
     */
    isInfo() {
        return this.infos.type === 2;
    }
    /**
     * Returns true if this \ :js:class:`MetaCell`\  is the first cell of the corresponding \ :js:class:`SuperCell`\ .
     * e.g. in the sexagesimal number 01, 12 ; 01, 14  the first cell holds the value 01.
     * @return {Boolean} true if this \ :js:class:`MetaCell`\  is the first cell of the corresponding \ :js:class:`SuperCell`\
     */
    isFirst() {
        return this.infos.position === 0;
    }
    /**
     * Returns true if this \ :js:class:`MetaCell`\  is the one corresponding to the first integer place.
     * e.g. in the sexagesimal number 01, 12 ; 01, 14  the first integer place holds the value 12.
     * @return {Boolean} true if this \ :js:class:`MetaCell`\  is the one corresponding to the first integer place.
     */
    isDec() {
        return this.infos.position === this.spec.decpos - 1;
    }
    /**
     * Returns true if this \ :js:class:`MetaCell`\  is the last cell of the corresponding \ :js:class:`SuperCell`\ .
     * e.g. in the sexagesimal number 01, 12 ; 01, 14  the last cell holds the value 14.
     * @return {Boolean} true if this \ :js:class:`MetaCell`\  is the last cell of the corresponding \ :js:class:`SuperCell`\
     */
    isLast() {
        return this.infos.position === this.spec.ncells - 1;
    }
    /**
     * Return a description of this \ :js:class:`MetaCell`\  as a string.
     * Used by the right-bottom console.
     * @return {string} description of this \ :js:class:`MetaCell`\  as a string.
     */
    log() { // rename: getLogString
        return ((this.val === "")?this.filler:this.val) + (this.isDec()?";":"");
    }
    /**
     * Function to input a value in this cell.
     * This is the only way to input properly a value in the table, as it will perform the necessary operations on the new value.
     * If using handsontable, the manual input of a cell must be overriden (thanks to the 'afterChange' event callback) and use this method.
     * This function is a wrapper around the applyFill method.
     * @param  {string}   content    new content of the cell, as a string
     * @param  {Boolean}  validated  true if the new value is validated (entered by hand for example), false if it is suggested
     * @param  {Boolean}  render     DEPRECATED: this parameter is to be removed
     * @param  {Boolean}  touched    true if you want the change of this cell to be propagated (for example to recompute the first differences)
     * @return {undefined}
     */
    fill(content, validated, render, touched) {
        for(var i=0; i<content.length; i++) {
            if(content[i] !== "0")
                break;
        }
        if(i === content.length && i > 0)
            i--;
        if((typeof content)==="string")
            content = content.substr(i);
        if(content !== "" && content !== "*")
            content = this.addZeros(content);
        if(content === "*")
            content = this.addStars(content);
        if(this.isArgument() && this.infos.root.linkedArgumentZones !== undefined) {
            for(var i=0; i<this.infos.root.linkedArgumentZones.length; i++) {
                if(this.infos.root.linkedArgumentZones[i] !== this.infos.root) {
                    this.infos.root.linkedArgumentZones[i].grid[this.row][this.col].applyFill(content, validated, render, touched);
                }
            }
        }
        this.applyFill(content, validated, render, touched);
    }
    /**
     * //TODO : essayer de mettre cette méthode dans fill() (idem pour prop)
     * Function to input a value in this cell.
     * @param  {string}   content    new content of the cell, as a string
     * @param  {Boolean}  validated  true if the new value is validated (entered by hand for example), false if it is suggested
     * @param  {Boolean}  render     DEPRECATED: this parameter is to be removed
     * @param  {Boolean}  touched    true if you want the change of this cell to be propagated (for example to recompute the first differences)
     * @return {undefined}
     */
    applyFill(content, validated, render, touched) {
        if(typeof debug !== "undefined") {
            debug += 1;
        }
        var old = this.infos.root.grid[this.row][this.col].val;
        var oldSuggested = this.infos.root.grid[this.row][this.col].props.suggested;
        if(this.infos.root.data !== undefined)
            this.infos.root.data[this.row][this.col] = content;
        this.setProp("error", false);
        this.setProp("error_str", "");
        this.val = content;
        if(content === "")
            this.setProp("suggested", true);
        if(touched === undefined || touched)
            this.parent.onTouchValue(validated);
        if(render)
            this.infos.hot.render();
        if(old !== this.val && !this.infos.root.undoing) {
            this.infos.root.history.push({
                cell: {
                    row: this.row,
                    col: this.col
                },
                oldState: {
                    val: old,
                    suggested: oldSuggested
                },
                newState: {
                    val: this.val,
                    suggested: this.props.suggested
                }
            });
        }
        this.updateCriticalApparatus();
    }
    updateCriticalApparatus() {
        if(Object.keys(this.props.edition_value).length > 0) {
            this.props.critical_apparatus = "";
            if(typeof this.infos.root.edition_tables === "undefined")
                return;
            for(var edition in this.props.edition_value) {
                if(this.val !== this.props.edition_value[edition]) {
                    if(this.infos.root.edition_tables.tables[edition] === undefined)
                        continue;
                    if(this.infos.root.edition_tables.tables[edition].siglum) {
                        var title = this.infos.root.edition_tables.tables[edition].siglum;
                    }
                    else {
                        var title = this.infos.root.edition_tables.tables[edition].editedTextTitle;
                    }
                    this.props.critical_apparatus += (title + ": " + this.props.edition_value[edition] + '\n');
                }
            }
        }
    }
    /**
     * Fills this cell with as many symbols '*' as its maximum string length authorizes.
     * @param  {Boolean} render DEPRECATED: this parameter is to be removed
     * @return {undefined}
     */
    fillStars(render) {
        var stars = "";
        for(var i=0; i<this.ndigit; i++) {
            stars += "*";
        }
        this.fill(stars, true, render);
    }
    /**
     * If this cell is empty, fills it with as many symbols '*' as its maximum string length authorizes.
     * @param  {Boolean} render DEPRECATED: this parameter is to be removed
     * @return {undefined}
     */
    fillStarsIfEmpty(render) {
        if(this.val === "") {
            this.fillStars(render);
        }
    }
    /**
     * Returns the value of the cell, extracted from this.infos.root.data.
     * Usefull to check if \ :js:attr:`val`\  is correctly synchronized with this.infos.root.data.
     * Not used.
     * @return {string} Value of this cell
     */
    value() { ///TODO checkHandsonTable... rename
        return this.data[this.row][this.col];
    }
    isNumeric() {
        if(this.val === "" || this.val.includes("*"))
            return false;
        return true;
    }
    /**
     * Sets the given property of this \ :js:class:`MetaCell`\  to the specified value.
     * This is the only way to alter properties, as it will performs the necessary operations on the new value.
     * This method is a wrapper around the applySetProp method.
     * @param {string}  key    Key for the dictionnary this.props
     * @param {Object}  value  New value for this.props[key]
     * @param {Boolean} render DEPRECATED: this parameter is to be removed
     * @param  {Boolean}  touched    true if you want the change of this cell to be propagated (for example to recompute the first differences)
     */
    setProp(key, value, render, touched) { //TODO renommage 
        if(this.isArgument() && this.infos.root.linkedArgumentZones !== undefined) {
            for(var i=0; i<this.infos.root.linkedArgumentZones.length; i++) {
                if(this.infos.root.linkedArgumentZones[i] !== this.infos.root) {
                    this.infos.root.linkedArgumentZones[i].grid[this.row][this.col].applySetProp(key, value, render);
                }
            }
        }
        this.applySetProp(key, value, render, touched);
    }
    /**
     * Sets the given property of this \ :js:class:`MetaCell`\  to the specified value.
     * @param {string}  key    Key for the dictionnary this.props
     * @param {Object}  value  New value for this.props[key]
     * @param {Boolean} render DEPRECATED: this parameter is to be removed
     * @param  {Boolean}  touched    true if you want the change of this cell to be propagated (for example to recompute the first differences)
     */
    applySetProp(key, value, render, touched) {
        var oldValue = this.props[key];
        this.props[key] = value;
        if(touched)
            this.parent.onTouchValue();
        if(render)
            this.infos.hot.render();
        if(value !== oldValue && !this.infos.root.undoing) {
            if(key !== "suggested")
                return;
            var olds = {};
            olds[key] = oldValue;
            var news = {};
            news[key] = value;
            this.infos.root.history.push({
                cell: {
                    row: this.row,
                    col: this.col
                },
                oldState: olds,
                newState: news
            });
        }
        if(this.parent.linkedSuperCells.length !== 0) {
            if(!this.infos.root.undoing && !this.infos.root.redoing) {
                //!this.infos.root.transformingLinked && 
                this.parent.copyPropsToLinkedSuperCells();
            }
        }
    }
    /**
     * Appends the specified value to the given property of this \ :js:class:`MetaCell`\ .
     * This is the only way to alter properties, as it will performs the necessary operations on the new value.
     * This method is a wrapper around the applyAppendProp method.
     * @param {string}  key    Key for the dictionnary this.props
     * @param {Object}  value  New value to append to this.props[key]
     * @param {Boolean} render DEPRECATED: this parameter is to be removed
     */
    appendProp(key, value, render) {
        if(this.isArgument() && this.infos.root.linkedArgumentZones !== undefined) {
            for(var i=0; i<this.infos.root.linkedArgumentZones.length; i++) {
                if(this.infos.root.linkedArgumentZones[i] !== this.infos.root) {
                    this.infos.root.linkedArgumentZones[i].grid[this.row][this.col].applyAppendProp(key, value, render);
                }
            }
        }
        this.applyAppendProp(key, value, render);
    }
    /**
     * Appends the specified value to the given property of this \ :js:class:`MetaCell`\ .
     * @param {string}  key    Key for the dictionnary this.props
     * @param {Object}  value  New value to append to this.props[key]
     * @param {Boolean} render DEPRECATED: this parameter is to be removed
     */
    applyAppendProp(key, value, render) {
        this.props[key].push(value);
        if(render)
            this.infos.hot.render();
    }
    /**
     * Erases the content of the cell.
     * //TODO touched -> updated
     * @param  {Boolean} render  DEPRECATED: this parameter is to be removed
     * @param  {Boolean} touched true if you want the change of this cell to be propagated (for example to recompute the first differences)
     * @return {undefined}
     */
    erase(render, touched) {
        this.fill("", false, render, touched);
    }
    /**
     * Operations to perform at the cell level when this cell is changed.
     * For now, it will only check if there is an error in the value.
     * @return {undefined}
     */
    onTouchValue() {
        this.checkError();
    }
    /**
     * // TODO move in checkError
     * String to be printed when the cell's value is bigger than this.radix
     * @return {string} error string
     */
    tooLargeLog() {
        var errors = "";
        errors += "    " + this.val + " too big for cell radix (" + this.radix + ")" + "\n";
        return errors;
    }
    /**
     * Checks the value of the cell, and updates the flags this.props.error and this.props.error_str if there is
     * an error.
     * //TODO change name or return the result
     * @return {undefined}
     */
    checkError() {
        var newVal = this.val;
        //check du flag error
        if(newVal >= this.radix && (!this.isFirst() || (this.spec.type != "integer_and_sexagesimal"))) {
            this.setProp("error", true);
            this.setProp("error_str", this.tooLargeLog());
        }
        else {
            this.setProp("error", false);
        }
    }
    /**
     * Complete the (left side of the) given value with zeros, until the length of the string is equal to this.ndigits
     * @param {string} val
     * @return {string} Padded value
     */
    addZeros(val) {
        val = val.toString();
        if(val.length < this.ndigit) {
            for(var i=0; i<this.ndigit-val.length; i++)
                val = "0"+val;
        }
        return val;
    }
    /**
     * Complete the (left side of the) given value with '*', until the length of the string is equal to this.ndigits
     * @param {string} val
     * @return {string} Padded value
     */
    addStars(val) {
        val = val.toString();
        if(val.length < this.ndigit) {
            for(var i=0; i<this.ndigit-val.length; i++)
                val = "*"+val;
        }
        return val;
    }
    /**
     * Validate this cell.
     * @return {undefined}
     */
    validate() {
        this.setProp("suggested", false);
    }
    /**
     * Unvalidate this cell.
     * @return {undefined}
     */
    unvalidate() {
        this.setProp("suggested", true);
    }
    /**
     * Validate or unvalidate this cell
     * @param  {Boolean}  suggested  Wether to validate or unvalidate a cell
     * @return {undefined}
     */
    suggest(suggested) {
        if(!suggested)
            this.validate();
        else
            this.unvalidate();
    }
}