/* global RootZone */
/* global SmartNumber */
/* global NView */
/* global nameToBase */
/* global Cell */
/* global posInBase */
/* global Zone */
/* global forWrapper */


class SuperCell extends Zone {
    /**
     * A \ :js:class:`SuperCell`\  is a group of \ :js:class:`MetaCell`\  representing the same historical value.
     * 
     * e.g. in the sexagesimal number 01, 12 ; 01, 14  there are 4 \ :js:class:`MetaCell`\ s with the following values: '01', '12', '01' and '14'.
     * These 4 \ :js:class:`MetaCell`\ s are the four children of one \ :js:class:`SuperCell`\ .
     *
     * @param  {Object} spec       Template informations at the \ :js:class:`MetaCell`\  and \ :js:class:`SuperCell`\  level
     * @param  {Array}  positions  List of the relative cartesian coordinates of the \ :js:class:`MetaCell`\ s within this \ :js:class:`SuperCell`\ 
     * @param  {Object} infos      Dictionary of informations
     * @return {SuperCell}
     */
    constructor(spec, positions, infos) {
        super(infos);
        /**
         * Infos contains informations about the zone ancestors.
         * Not very useful for \ :js:class:`MetaZone`\ s, but useful for
         * \ :js:class:`SuperCell`\ s and \ :js:class:`MetaCell`\ s.
         * @example
         * {
         *     hot: ...,    // The table containing this SuperCell (and its parents)
         *     root: ...,   // The root in the Zone hierarchy
         *     index: ...,  // 0 for arg1 and entry, 1 for arg2 -> cannot remember why ??
         *     type: ...,   // 0 for entry, 1 for argument, 2 for information (2 is used only for SuperCels in verticalInformationTables)
         *     argPos: ..., // in the case of an entry SuperCell, index(es) of the corresponding arguments
         * }
         * 
         * @type {Object}
         */
        this.infos;
        /**
         * spec is the sub-template of this \ :js:class:`SuperCell`\ , stored as a dictionnary
         * @example
         * {
         *     decpos: 1,           // number of integer places in this SuperCell
         *     name: "Entry",       // name of the corresponding argument or "Entry"
         *     ncells: 4,           // total number of places in this SuperCell
         *     type: "sexagesimal"  // TypeOfNumber used by this SuperCell
         * }
         * 
         * @type {Object}
         */
        this.spec = spec;
        /**
         * positions is the list of the relative cartesian coordinates of the \ :js:class:`MetaCell`\ s within this \ :js:class:`SuperCell`\ .
         * For standard layouts (where we read \ :js:class:`SuperCell`\ s from left to right), 
         * this list will typically be [(0,0), (0,1), (0,2), ..., (0,N)]
         * 
         * @type {Array}
         */
        this.positions = positions;
        for(var i=0; i<this.positions.length; i++) {
            this.addZone(new MetaCell(this.spec, {"position": i}), this.positions[i]);
        }
        /**
         * props contains all the properties at the \ :js:class:`SuperCell`\  level
         * @example
         * {
         *     suggestion_target: null,
         *     suggestion_source: [],
         *     user_comment : ''
         * }
         * 
         * @type {Object}
         */
        this.props = {
            suggestion_target: null,
            suggestion_source: [],
            user_comment : "",
            symmetry_source : undefined
        };
        /**
         * Array of couple (\ :js:class:`SuperCell`\ , transformation). These are 
         * \ :js:class:`SuperCell`\ s that will be modified accordingly when this one is changed.
         * @example
         * >> this.linkedSuperCells.push({
         *        'target': this.infos.root.superGrid[1][1],
         *        'transform': x => x
         *    })
         * @type {Array}
         */
        this.linkedSuperCells = [];
    }
    /**
     * Link a \ :js:class:`SuperCell`\  to this one. The value of the \ :js:class:`SuperCell`\  will be changed
     * thanks to the specified transform. If a reverse function is specfied, a link is created the other way 
     * (so that when modifying the other \ :js:class:`SuperCell`\ , this one is changed).
     * @param  {SuperCell}  supercell            the linked SuperCell
     * @param  {function}   transform            the modificaton
     * @return {undefined}
     */
    linkSuperCell(supercell, transform) {
        if(supercell === this)
            return;
        if(supercell.props.symmetry_source !== undefined)
            return;
        this.linkedSuperCells.push({
            "target": supercell,
            "transform": transform
        });
        var visited = [];
        function testCycle(sc) {
            if(visited.indexOf(sc) !== -1)
                return true;
            visited.push(sc);
            var res = false;
            for(var i=0; i<sc.linkedSuperCells.length; i++) {
                res = res || testCycle(sc.linkedSuperCells[i].target);
            }
            return res;
        }
        if(testCycle(this)) {
            this.linkedSuperCells.pop();
            return ;
        }
        supercell.props.symmetry_source = {
            "source": [this.zones[0].row, this.zones[0].col],
            "transform": transform
        };
    }

    /**
     *
     * @param validated
     * @param touched
     */
    negate(validated, touched) {
        if(!this.zones[0].isNumeric() || this.zones[0].val === undefined) {
            if(touched === undefined||touched)
                this.onTouchValue(validated);
            return;
        }
        if(this.zones[0].val[0] === '-')
            this.zones[0].fill(this.zones[0].val.substr(1), validated, false, false);
        else
            this.zones[0].fill('-' + this.zones[0].val, validated, false, false);
        if(touched === undefined||touched)
            this.onTouchValue(validated);
    }

    /**
     *
     * @param supercell
     * @param validated
     * @param touched
     */
    copyToSupercell(supercell, validated, touched) {
        for(var i=0; i<Math.min(this.zones.length, supercell.zones.length); i++) {
            supercell.zones[i].fill(this.zones[i].val, validated, false, false);
        }
        if(touched === undefined||touched)
            supercell.onTouchValue(validated);
    }

    /**
     * Fill the superCell with the value given in jsonValue
     *
     * @param jsonValue : object containing the value to assign to the superCell
     * Ex : {
     *   "value": ["0","42","38"],
     *   "comment": "",
     *   "suggested": false,
     *   "critical_aparatus": ["","",""]
     * }
     * @param spec : object (optional) defining the template for the superCell (?)
     * @param convert : bool value indicating if the value must be converted (?) usage not clear
     */
    fromJSON(jsonValue, spec, convert) {
        // select this superCell
        var sc = this;

        if (spec === undefined || !convert) {
            for (var c=0; c<Math.min(sc.zones.length, jsonValue.value.length); c++) {
                sc.zones[c].fill(jsonValue.value[c]);
            }
        } else {
            var left = JSON.parse(JSON.stringify(jsonValue.value));
            var full = true;
            for (var c=0; c<left.length; c++) {
                if (left[c] == "") {
                    full = false;
                    break;
                }
            }
            if (!full)
                return;
            var sign = 1;

            if (left[0][0] === "-") {
                left[0] = left[0].substr(1);
                sign = -1;
            }

            var right = left.splice(spec.args[i].decpos);
            for (var c=0; c<left.length; c++) {
                left[c] = Number(left[c]);
            }

            for (var c=0; c<right.length; c++) {
                right[c] = Number(right[c]);
            }
            var sn = new SmartNumber(new NView(left.reverse(), right, 0.0, sign, nameToBase[spec.args[i].type]));
            sc.setSmartNumber(sn);
        }
        sc.props["user_comment"] = jsonValue.comment;
        // load the critical apparatus of each cells
        if (jsonValue.critical_apparatus !== undefined) {
            for(var c=0; c<sc.zones.length; c++) {
                sc.zones[c].props.critical_apparatus = jsonValue.critical_apparatus[c];
            }
        }
        /*if(jsonValue.symmetry_source !== undefined) {
            var source = this.infos.root.superGrid[jsonValue.symmetry_source.source[0]][jsonValue.symmetry_source.source[1]];
            var transform = jsonValue.symmetry_source.transform;
            source.linkSuperCell(sc, transform);
        }*/
        if (!jsonValue.suggested) {
            sc.validateNonEmpty();
        }
    }

    /**
     *
     * @return {{suggested: Boolean, critical_apparatus: Array, comment: string, value: Array}}
     */
    asJSON() {
        var sc = this;
        var value = [];
        for (var c=0; c<sc.zones.length; c++) {
            value.push(sc.zones[c].val);
        }
        var critical_apparatus = [];
        for (var c=0; c < sc.zones.length; c++) {
            critical_apparatus.push(sc.zones[c].props.critical_apparatus);
        }
        var res = {
            value: value,
            comment: sc.props.user_comment,
            suggested: sc.testFullProp("suggested", true),
            critical_apparatus: critical_apparatus
        };
        /*if(sc.props.symmetry_source !== undefined)
            res.symmetry_source = sc.props.symmetry_source;*/
        return res;
    }

    /**
     *
     * @param target
     * @param method
     * @param validated
     */
    transferValue(target, method, validated) {
        if(typeof method !== "function") {
            if(method[0] === "identity") {
                this.copyToSupercell(target, validated);
            }
            else if(method[0] === "opposite") {
                this.copyToSupercell(target, validated, false);
                target.negate(validated);
            }
            else if(method[0] === "addition") {
                var offset = method[1];
                var sn = this.getSmartNumber();
                var integerIndex = sn.value().leftList.length;
                target.setSmartNumber(sn.addOne(integerIndex, offset), validated);
            }
            else if(method[0] === "substraction") {
                var offset = method[1];
                var nv = this.getSmartNumber().value();
                var integerIndex = nv.leftList.length;
                nv.sign *= 1;
                sn = new SmartNumber(nv);
                target.setSmartNumber(sn.addOne(integerIndex, offset), validated);
            }
        }
        else if(this.isComplete()) {
            target.setSmartNumber(new SmartNumber(method(this.getSmartNumber().computeDecimal())), validated);
        }
    }
    /**
     * Compute and fill the value of the linked \ :js:class:`SuperCell`\ s.
     * @param  {Boolean} validated
     * @return {undefined}
     */
    transformLinkedSuperCells(validated) {
        if(typeof debug !== "undefined")
            console.log("transform");
        for(var i=0; i<this.linkedSuperCells.length; i++) {
            var link = this.linkedSuperCells[i];
            this.infos.root.transformingLinked = true;
            // link.target.erase();
            if(typeof link.transform !== "function") {
                if(link.transform[0] === "identity") {
                    this.copyToSupercell(link.target, validated);
                }
                else if(link.transform[0] === "opposite") {
                    this.copyToSupercell(link.target, validated, false);
                    link.target.negate(validated);
                }
                else if(link.transform[0] === "addition") {
                    var offset = link.transform[1];
                    var sn = this.getSmartNumber();
                    var integerIndex = sn.value().leftList.length;
                    link.target.setSmartNumber(sn.addOne(integerIndex, offset), validated);
                }
                else if(link.transform[0] === "substraction") {
                    var offset = link.transform[1];
                    var nv = this.getSmartNumber().value();
                    var integerIndex = nv.leftList.length;
                    nv.sign *= 1;
                    sn = new SmartNumber(nv);
                    link.target.setSmartNumber(sn.addOne(integerIndex, offset), validated);
                }
            }
            else if(this.isComplete()) {
                link.target.setSmartNumber(new SmartNumber(link.transform(this.getSmartNumber().computeDecimal())), validated);
            }
            this.infos.root.transformingLinked = false;
        }
    }
    /**
     * Copy the property 'suggested' form this \ :js:class:`SuperCell`\  to its linked ones.
     * @return {undefined}
     */
    copyPropsToLinkedSuperCells() {
        for(var i=0; i<this.linkedSuperCells.length; i++) {
            var link = this.linkedSuperCells[i];
            this.infos.root.transformingLinked = true;
            if(this.testAtLeastOneProp("suggested", false)) {
                link.target.validateNonEmpty();
            }
            else {
                link.target.unvalidate();
            }
            this.infos.root.transformingLinked = false;
        }
    }
    /**
     * Fills the empty children \ :js:class:`MetaCell`\  with symbols '*'
     * @param  {Boolean} render DEPRECATED: this parameter is to be removed
     * @return {undefined}
     */
    fillStarsIfEmpty(render) {
        for(var i=0; i<this.zones.length; i++) {
            this.zones[i].fillStarsIfEmpty(render);
        }
    }
    // TODO same modif as over.
    /**
     * Return the nth neighbour \ :js:class:`SuperCell`\  along the specified argument.
     * This method requires the parent Layout class to have implemented its own nextArg method!
     * @param  {Number} arg    Specify the argument on which the search is performed
     * @param  {Number} delta  Specify which neighbour (step)
     * @return {SuperCell}     The nth next \ :js:class:`SuperCell`\  along the specified argument
     */
    nextArg(arg, delta) {
        return this.parent.nextArg(arg, delta, this.indexInParent);
    }
    /**
     * Returns true if this \ :js:class:`SuperCell`\  is an entry
     * @return {Boolean} true if this \ :js:class:`SuperCell`\  is an entry
     */
    isEntry() {
        return this.infos.type === 0;
    }
    /**
     * Returns true if this \ :js:class:`SuperCell`\  is an argument
     * @return {Boolean} true if this \ :js:class:`SuperCell`\  is an argument
     */
    isArgument() {
        return this.infos.type === 1;
    }
    /**
     * Returns true if this \ :js:class:`SuperCell`\  is an info-\ :js:class:`SuperCell`\ 
     * @return {Boolean} true if this \ :js:class:`SuperCell`\  is an info-\ :js:class:`SuperCell`\ 
     */
    isInfo() {
        return this.infos.type === 2;
    }
    /**
     * Return a string representing this \ :js:class:`SuperCell`\ . 
     * Used by the bottom-right console
     * @return {string} Description of this \ :js:class:`SuperCell`\ 
     */
    log() {
        var res = "";
        res += "Cell type: " + this.spec["type"] + "\n";
        
        res += "Cell content: ";
        
        var content = "";
        for(var i=0; i<this.zones.length; i++) {
            content += this.zones[i].log() + " ";
        }
        
        res += content + "\n";
        
        res += "Decimal value: " + this.getSmartNumber().computeDecimal().toFixed(5).toString() + "\n";
        
        var errors = "";
        for(var i=0; i<this.zones.length; i++) {
            if(this.zones[i].props.error) {
                //errors += "    " + this.zones[i].val + " too big for cell radix (" + this.zones[i].radix + ")" + "\n";
                errors += this.zones[i].props.error_str + "\n";
            }
        }
        
        res += errors;

        res += '\n';

        res += "Line: " + this.zones[0].row + "\n";
        res += "Col : " + this.zones[0].col + "-" + this.zones[this.zones.length - 1].col + "\n";
        
        return res;
    }
    /**
     * Validate all the children \ :js:class:`MetaCell`\ s which are not empty.
     * @return {undefined}
     */
    validateNonEmpty() {
        for(var i=0;i<this.zones.length; i++)
            if(this.infos.root.data[this.zones[i].row][this.zones[i].col] !== "")
                this.zones[i].validate();
    }
    /**
     * Unvalidate all the children \ :js:class:`MetaCell`\ s.
     * @return {undefined}
     */
    unvalidate() {
        for(var i=0;i<this.zones.length; i++)
            this.zones[i].unvalidate();
    }
    /**
     * Validate or unvalidate all the children \ :js:class:`MetaCell`\ s. Will not validate an empty cell.
     * @param  {Boolean} suggested  whether to validate or unvalidate
     * @return {undefined}
     */
    suggest(suggested) {
        if(!suggested)
            this.validateNonEmpty();
        else
            this.unvalidate();
    }
    /**
     * Tests if every \ :js:class:`MetaCell`\  child has the specified value for the sepcified key in their props attribute.
     * // TODO exmaple
     * @param  {string} key    key for the props attribute of each \ :js:class:`MetaCell`\  child
     * @param  {Object} value  Assumed value for props[key] in each \ :js:class:`MetaCell`\  child
     * @return {Boolean}       true if props[key] === value in each \ :js:class:`MetaCell`\  child
     */
    testFullProp(key, value) {
        for(var i=0; i<this.zones.length; i++) {
            if(this.zones[i].props[key] !== value)
                return false;
        }
        return true;
    }
    //TODO rename ?
    /**
     * Checks if every child cell is non-empty, and without any '*' symbol.
     * Usefull in order to know if this \ :js:class:`SuperCell`\ 's value can be used for computation.
     * @return {Boolean} true if every child cell is non-empty, and without any '*' symbol.
     */
    isComplete() {
        var cells = this.zones;
        for(var i=0; i<cells.length; i++) {
            if(!cells[i].isNumeric())
                return false;
        }
        return true;
    }
    /**
     * Checks if every child cell is non-empty, and without any '*' symbol.
     * Also checks if every child cell is validated
     * Usefull in order to know if this \ :js:class:`SuperCell`\ 's value can be used for computation.
     * @return {Boolean} true if every child cell is non-empty, without any '*' symbol, and validated.
     */
    isCompleteAndValidated() {
        return this.isComplete() && this.testFullProp('suggested', false);
    }

    /**
     * Tests if at least one \ :js:class:`MetaCell`\  child has the specified value for the sepcified key in its props attribute.
     * @param  {string} key    key for the props attribute of each \ :js:class:`MetaCell`\  child
     * @param  {Object} value  Assumed value for props[key] in each \ :js:class:`MetaCell`\  child
     * @return {Boolean}       true if props[key] === value for at least one \ :js:class:`MetaCell`\  child
     */
    testAtLeastOneProp(key, value) {
        for(var i=0; i<this.zones.length; i++) {
            if(this.zones[i].props[key] === value)
                return true;
        }
        return false;
    }
    /**
     * Sets the given property of this \ :js:class:`SuperCell`\  to the specified value.
     * If the specified property is at the \ :js:class:`MetaCell`\  level, the value of each \ :js:class:`MetaCell`\  child will be updated.
     * This is the only way to alter properties, as it will performs the necessary operations on the new value.
     * @param {string}  key    key for the props attribute
     * @param {Object}  value  value for props[key]
     * @param {Boolean} render DEPRECATED: this parameter is to be removed
     */
    setProp(key, value, render) {
        if(key in this.props) {
            if(this.isArgument() && this.infos.root.linkedArgumentZones !== undefined) {
                for(var i=0; i<this.infos.root.linkedArgumentZones.length; i++) {
                    if(this.infos.root.linkedArgumentZones[i] !== this.infos.root) {
                        this.infos.root.linkedArgumentZones[i].superGrid[this.zones[0].row][this.zones[0].col].props[key] = value;
                    }
                }
            }
            this.props[key] = value;
        }
        else
            for(var i=0; i<this.zones.length; i++) {
                this.zones[i].setProp(key, value, false);
            }
        if(render)
            this.infos.hot.render();
    }
    /**
     * Appends the specified value to the given property of this \ :js:class:`SuperCell`\ .
     * If the specified property is at the \ :js:class:`MetaCell`\  level, the value of each \ :js:class:`MetaCell`\  child will be updated.
     * This is the only way to alter properties, as it will performs the necessary operations on the new value.
     * @param {string}  key    key for the props attribute
     * @param {Object}  value  value to append to props[key]
     * @param {Boolean} render DEPRECATED: this parameter is to be removed
     */
    appendProp(key, value, render) {
        if(key in this.props) {
            //this.props[key].push(value);
            if(this.isArgument() && this.infos.root.linkedArgumentZones !== undefined) {
                for(var i=0; i<this.infos.root.linkedArgumentZones.length; i++) {
                    if(this.infos.root.linkedArgumentZones[i] !== this.infos.root) {
                        this.infos.root.linkedArgumentZones[i].superGrid[this.zones[0].row][this.zones[0].col].props[key].push(value);
                    }
                }
            }
            this.props[key].push(value);
        }
        else
            for(var i=0; i<this.zones.length; i++) {
                this.zones[i].appendProp(key, value, false);
            }
        if(render)
            this.infos.hot.render();
    }
    /**
     * Erases the value of all \ :js:class:`MetaCell`\  children
     * @param  {Boolean} render   DEPRECATED: this parameter is to be removed
     * @param  {Boolean} touched  true if you want the change of this \ :js:class:`SuperCell`\  to be propagated (for example to recompute the first differences)
     * @return {undefined}
     */
    erase(render, touched) {
        var cells = this.zones;
        for(var i=0; i<cells.length; i++) {
            cells[i].erase(false, touched);
        }
        if(render)
            this.infos.hot.render();
    }
    /**
     * Operations to perform after a \ :js:class:`SuperCell`\  is altered.
     * The code to update first and second differences, or more generally to propagate the change of this \ :js:class:`SuperCell`\  to
     * other cells must be placed here.
     * @param  {Boolean} validated  If specified, will validate or unvalidate the whole \ :js:class:`SuperCell`\ 
     * @return {undefined}
     */
    onTouchValue(validated) {
        if(typeof debug !== "undefined") {
            console.log("onTouchValue");
        }
        if(validated !== undefined) {
            if(validated) {
                this.validateNonEmpty();
            }
            else {
                this.setProp("suggested", true);
            }
        }
        
        if(!this.testFullProp("suggested", false)) {
            this.setProp("suggestion_source", []);
        }
        
        for(var i=0; i<this.zones.length; i++) {
            this.zones[i].onTouchValue();
        }
        // When a super-cell is modified, all informations about the potential
        // input tool that helped creating it must be droped
        // disabled for now
        if(this.props.suggestion_target !== null) {
            var area = this.props.suggestion_target;
            for(var i=0; i<area.sourceList.length; i++) {
                var cellProps = area.sourceList[i].props;
                for (var k=cellProps.suggestion_source.length-1; k>=0; k--) { // OPTIMIZE BY STORING THE INDEX
                    var targetList = cellProps.suggestion_source[k].area.targetList;
                    if(targetList.includes(this)) {
                        cellProps.suggestion_source.splice(k, 1);
                    }
                }
            }
        }
        // When a super-cell is modified, we must propagate all its modifications to
        // the values it helped to compute
        // disabled for now
        if(this.props.suggestion_source.length !== 0) {
            var that = this;
            var initialLength = that.props.suggestion_source.length;
            forWrapper(that.props.suggestion_source.length, 0, 1000,
                function(i) {
                    var k = initialLength-1-i;
                    if(that.props.suggestion_source[k].area.targetList[0].testAtLeastOneProp("suggested", false) ||
                            !that.props.suggestion_source[k].area.targetList[0].isComplete()) {
                        that.props.suggestion_source.splice(k,1);
                    }
                    else {
                        that.props.suggestion_source[k].tool.activateToolFromArea(that.props.suggestion_source[k].area, false);
                    }
                },
                function(){/*that.infos.hot.render()*/}, 1000, 1);
        }

        if(this.linkedSuperCells.length !== 0) {
            if(!this.infos.root.undoing && !this.infos.root.redoing) {
                //if(!this.infos.root.transformingLinked)
                //if(!this.parent.linkPropagated)
                this.transformLinkedSuperCells(validated);
            }
        }
        
        this.setProp("suggestion_target", null);
        
        if(this.isEntry()) { // A discuter ?
            if(this.infos.root.verticalInformationTables !== undefined) {
                var verticalInformationTables = this.infos.root.verticalInformationTables;
                for(var i=0; i<verticalInformationTables.length; i++) {
                    var infoTable = verticalInformationTables[i];
                    if(infoTable === undefined) continue;
                    if(infoTable.mathZone === undefined) continue;
                    if(infoTable.mathZone.tool === undefined) continue;
                    infoTable.mathZone.tool.activateTool([this.infos.root, infoTable.mathZone], [[this.zones[0].row, this.zones[0].col, this.zones[0].row, this.zones[0].col]]);
                }
            }
            if(this.infos.root.horizontalInformationTables !== undefined) {
                var horizontalInformationTables = this.infos.root.horizontalInformationTables;
                for(var i=0; i<horizontalInformationTables.length; i++) {
                    var infoTable = horizontalInformationTables[i];
                    if(infoTable === undefined) continue;
                    if(infoTable.mathZone === undefined) continue;
                    if(infoTable.mathZone.tool === undefined) continue;
                    infoTable.mathZone.tool.activateTool([this.infos.root, infoTable.mathZone], [[this.zones[0].row, this.zones[0].col, this.zones[0].row, this.zones[0].col]]);
                }
            }
            if(this.infos.root.mathZones !== undefined) {
                var mathZones = this.infos.root.mathZones;
                for(var key in mathZones) {
                    var mathZone = mathZones[key];
                    if(mathZone === undefined) continue;
                    if(mathZone.tool === undefined) continue;
                    mathZone.tool.activateTool([this.infos.root, mathZone], [this], true);
                }
            }
            
            //check differences
            /*if(this.infos.hot.diff1Warning) {
                this.check_diff1(this.infos.hot.diff1_median,this.infos.hot.diff1_mad);
                var others = this.nextNArgs(0,1,1);
                if(this.infos.root.spec.args.length === 2) {
                    others = others.concat(this.nextNArgs(1,1,1));
                }
                for(var i=0; i<others.length; i++) {
                    others[i].setProp('error', false);
                    others[i].setProp('error_str', '');
                    others[i].checkError();
                    others[i].check_diff1(this.infos.hot.diff1_median,this.infos.hot.diff1_mad);
                }
            }
            if(this.infos.hot.diff2Warning) {
                this.check_diff2(this.infos.hot.diff2_median,this.infos.hot.diff2_mad);
                var others = this.nextNArgs(0,1,2);
                if(this.infos.root.spec.args.length === 2) {
                    others = others.concat(this.nextNArgs(1,1,2));
                }
                for(var i=0; i<others.length; i++) {
                    others[i].setProp('error', false);
                    others[i].setProp('error_str', '');
                    others[i].checkError();
                    others[i].check_diff2(this.infos.hot.diff2_median,this.infos.hot.diff2_mad);
                }
            }*/
        }
        
    }
    /**
     * Checks the value of the \ :js:class:`SuperCell`\  and its children, and updates the error flags.
     * @return {undefined}
     */
    checkError() {
        for(var i=0; i<this.zones.length; i++) {
            this.zones[i].checkError();
        }
    }
    
    /*
    //to be re-written with a smarter way of detecting outliers
    check_diff2(diff_mean, diff_delta) {
        if(diff_mean === undefined) {
            return;
        }
        if(diff_delta === undefined) {
            return;
        }
        
        if(this.infos.root.mathZones['diff2'].mathZone.fromPath([0,0]).fromPath(this.infos.argPos).isComplete() && Math.abs(this.infos.root.mathZones['diff2'].mathZone.fromPath([0,0]).fromPath(this.infos.argPos).getSmartNumber().computeDecimal()-diff_mean) > diff_delta) {
            this.setProp("error", true);
            this.zones[0].setProp("error_str", "    Second differences too large!");
        }
        if(this.infos.root.spec.args.length === 2) {
            if(this.infos.root.mathZones['diff2_horizontal'].mathZone.fromPath([0,0]).fromPath(this.infos.argPos).isComplete() && Math.abs(this.infos.root.mathZones['diff2_horizontal'].mathZone.fromPath([0,0]).fromPath(this.infos.argPos).getSmartNumber().computeDecimal()-diff_mean) > diff_delta) {
                this.setProp("error", true);
                this.zones[0].setProp("error_str", "    Second differences too large!");
            }
        }
        
    }
    
    //to be re-written with a smarter way of detecting outliers
    check_diff1(diff_mean, diff_delta) {
        if(diff_mean === undefined) {
            return;
        }
        if(diff_delta === undefined) {
            return;
        }

        if(this.infos.root.mathZones['diff1'].mathZone.fromPath([0,0]).fromPath(this.infos.argPos).isComplete() && Math.abs(this.infos.root.mathZones['diff1'].mathZone.fromPath([0,0]).fromPath(this.infos.argPos).getSmartNumber().computeDecimal()-diff_mean) > diff_delta) {
            this.setProp("error", true);
            this.zones[0].setProp("error_str", "    First differences too large!");
        }
        if(this.infos.root.spec.args.length === 2) {
            if(this.infos.root.mathZones['diff1_horizontal'].mathZone.fromPath([0,0]).fromPath(this.infos.argPos).isComplete() && Math.abs(this.infos.root.mathZones['diff1_horizontal'].mathZone.fromPath([0,0]).fromPath(this.infos.argPos).getSmartNumber().computeDecimal()-diff_mean) > diff_delta) {
                this.setProp("error", true);
                this.zones[0].setProp("error_str", "    First differences too large!");
            }
        }
        
    }*/
    // TODO toSmartNumber
    /**
     * Returns the historical value held by this \ :js:class:`SuperCell`\  as a \ :js:class:`SmartNumber`\ 
     * @return {SmartNumber} The historical value held by this \ :js:class:`SuperCell`\ 
     */
    getSmartNumber() {
        if(!this.isComplete()) {
            //return undefined;
        }
        var ndec = this.spec.decpos;
        var leftList = [];
        var rightList = [];
        var count = 0;
        var cells = this.zones;
        for(count=0;count<ndec;count++) {
            var val = this.infos.root.grid[cells[ndec-count-1].row][cells[ndec-count-1].col].val;
            leftList.push(Number(val>=0?val:-val));
        }
        for(var i=count; i<cells.length;i++) {
            var val = Number(this.infos.root.grid[cells[i].row][cells[i].col].val);
            rightList.push(Number(val>=0?val:-val));
        }
        var name = this.spec.type;
        var sign = 1;
        var firstcell = this.infos.root.grid[cells[0].row][cells[0].col].val;
        if((typeof firstcell === "string" && firstcell.charAt(0) === "-") || (typeof firstcell === "number" && firstcell < 0)) {
            sign = -1;
            // leftList[0] = Math.abs(Number(leftList[0]));
        }
        return new SmartNumber(leftList, rightList, 0.0, sign, name);
    }
    
    // TODO rename fromSmartNumber
    /**
     * Sets the values of this \ :js:class:`SuperCell`\ 's children so that it corresponds to the specified historical number
     * @param {SmartNumber} value      The historical number to be held by this \ :js:class:`SuperCell`\ 
     * @param {Boolean}     validated  Wether this value is to be suggested or validated
     * @param {Boolean}     render     DEPRECATED: this parameter is to be removed
     */
    setSmartNumber(value, validated, render) {
        if(this.infos.root.spec.readonly)
            return ;
        var name = this.spec.type;
        var ndec = this.spec.decpos;
        var nsig = this.spec.ncells - this.spec.decpos;
        
        if(name !== "none") {
            var num = value.toBase(name,nsig+1);
            num.round(nsig);
            var leftl = num.computeBase(name,nsig).leftList.length;
            if(leftl > ndec) {
                //remove the [0,..,ndec-1] part (keep the exceding part)
                var tmp = JSON.parse(JSON.stringify(num.computeBase(name,nsig).leftList));
                for(var i=0; i<ndec; i++) {
                    tmp[i] = 0;
                }
                var sn = new SmartNumber(tmp, [], 0.0, 1, name);
                var factor = 1;
                for(var i=0;i<ndec-1;i++) {
                    factor *= nameToBase[name][0][i];
                }
                num.computeBase(name,nsig).leftList[ndec-1] += sn.computeDecimal() / factor;
            }
            var cells = this.zones;
            for(var i=0; i<ndec; i++) {
                if(num.computeBase(name,nsig).leftList[ndec-1-i] === undefined) {
                    cells[i].fill(0, false, false, false);
                }
                else
                    cells[i].fill(num.computeBase(name,nsig).leftList[ndec-1-i], false, false, false);
            }
            for(var i=0;i<nsig; i++) {
                cells[i+ndec].fill(num.computeBase(name,nsig).rightList[i], false, false, false);
            }
            if(num.computeBase(name,nsig).sign < 0) {
                cells[0].fill("-"+this.infos.root.grid[cells[0].row][cells[0].col].val, false, false, false);
            }
        }
        else {
            var cells = this.zones;
            cells[0].fill(value.computeDecimal().toExponential(5), false, false, false);
        }
        if(render)
            this.infos.hot.render();
        if(validated)
            this.validateNonEmpty();
        this.onTouchValue();
    }

    // TODO rename getNSiblingsAlongArg
    /**
     * Return the N first neighbours \ :js:class:`SuperCell`\  along the specified argument.
     * This method requires the parent Layout class to have implemented its own nextArg method!
     * @param  {Number}  arg             Specify the argument on which the search is performed
     * @param  {Number}  direction       direction of the search (+/-1)
     * @param  {Number}  nb              number of \ :js:class:`SuperCell`\ s to be returned
     * @param  {Boolean} testSuggested  if true, returns only complete \ :js:class:`SuperCell`\ s (i.e. suitable for computations)
     * @return {Array}  The  N first neighbours \ :js:class:`SuperCell`\  along the specified argument
     */
    nextNArgs(arg, direction, nb, testSuggested) {
        if(testSuggested === undefined)
            testSuggested = true;
        if(nb === undefined)
            var nb = 1;
        var cells = [];
        var counter = 0;
        var current = this;
        while(counter < nb) {
            var next = current.nextArg(arg, direction);
            if(next === null || next === undefined)
                break;
            if(!testSuggested || (next.isComplete() && next.testFullProp("suggested", false))) {
                cells.push(next);
                counter ++;
            }
            current = next;
        }
        return cells;
    }
}


