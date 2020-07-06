/* jshint esversion: 6 */

/* global RootZone */
/* global SmartNumber */
/* global NView */
/* global nameToBase */
/* global Cell */
/* global posInBase */
/* global Zone */
/* global forWrapper */

function binarySearch(arr, target, nearest, comparisonEq, comparisonLe) {
    if (comparisonEq === undefined) {
        var comparisonEq = ((x, y) => x === y);
    }
    if (comparisonLe === undefined) {
        var comparisonLe = ((x, y) => x < y);
    }
    let left = 0;
    let right = arr.length - 1;
    while (left <= right) {
        var mid = left + Math.floor((right - left) / 2);
        if (comparisonEq(arr[mid],target)) {
            return mid;
        }
        if (comparisonLe(arr[mid],target)) {
            left = mid + 1;
        } else {
            right = mid - 1;
        }
    }
    if (nearest === undefined || !nearest)
        return -1;
    else
        return mid;
}

function floatIndexOf(arr, value, epsilon=1e-10) {
    for (var i=0; i<arr.length; i++) {
        if (Math.abs(arr[i] - value) < epsilon) {
            return i;
        }
    }
    return -1;
}

class MetaZone extends RootZone {
    /**
     * This class extends the concept of \ :js:class:`RootZone`\  for our needs in the DISHAS interface.
     * It's role is to understand \ :js:class:`SuperCell`\ s as historical values.
     * 
     * @param  {Object} infos   Dictionary of informations. Shared with subzones.
     * @return {MetaZone}
     */
    constructor(infos) {
        super(infos);
        /**
         * Infos contains informations about the zone ancestors.
         * Not very useful for \ :js:class:`MetaZone`\ s, but useful for
         * \ :js:class:`SuperCell`\ s and \ :js:class:`MetaCell`\ s.
         * @example
         * {
         *     hot: ...,    // The table containing this \ :js:class:`MetaZone`\ .
         *     root: ...,   // The root in the Zone hierarchy: in this case self
         * }
         * 
         * @type {Object}
         */
        this.infos;
        /**
         * List of column tools (i.e. vertical tools). Needs to be updated each time a value is changed.
         * Their values will be displayed in the right part of the interface.
         * TODO this.vertical_info_tools
         * TODO mettre reference vers description d'un autotool
         * @type {Array}
         */
        this.verticalInformationTables = [];
        /**
         * List of line tools (i.e. horizontal tools). Needs to be updated each time a value is changed.
         * Their values will be displayed in the bottom part of the interface.
         * Only for 2-arguments tables.
         * TODO this.horizontal_info_tools
         * @type {Array}
         */
        this.horizontalInformationTables = [];
        /**
         * List of quantities that are computed in real time each time the table is changed
         * For exemple 1st or 2nd differences (in both directions for 2-arguments tables)
         * @type {Array}
         */
        this.mathZones = [];
        /**
         * History of modifications. It is used for the "undo" action.
         * @example
         * // an element of histroy can either be null, to denote a block limitation
         * >> this.history.push(null):
         * // or a modification of the table with the old and new state
         * >> this.history.push({
         *        cell: {
         *            row: 5,
         *            col: 10
         *        },
         *        oldState: {
         *            val: ""
         *        },
         *        newState: {
         *            val: "14"
         *        }
         *    });
         * >> this.history.push({
         *        cell: {
         *            row: 5,
         *            col: 10
         *        },
         *        oldState: {
         *            props: {suggested: true}
         *        },
         *        newState: {
         *            props: {suggested: false}
         *        }
         *    })
         * @type {Array}
         */
        this.history = [];
        /**
         * List of cancelled modifications. It is used to perform the "redo" action.
         * @type {Array}
         */
        this.redoList = [];
        /**
         * Flag for avoiding cycling when computing values of linked \ :js:class:`SuperCell`\ .
         * @type {Boolean}
         */
        this.transformingLinked = false;

        this.functionSuperCells = undefined;

        this.supercellList = undefined;

        this.symmetries = [];
    }
    /**
     * Method to call right after creation, in order to compute grids and initialize infos in subzones.
     * 
     * @return {undefined}
     */
    setup() {
        super.setup();
        /**
         * This double entry array contains the static CSS properties of each cells (based on the template of the table).
         * @type {Array}
         */
        this.cssGrid = [];
        for (var i=0; i<this.R; i++) {
            this.cssGrid.push([]);
            for (var j=0; j<this.C; j++) {
                this.cssGrid[i].push(this.gridXYCSS(i,j,this));
            }
        }
    }
    /**
     * Helper function to fill in the memory array \ :js:attr:`cssGrid`\ .
     * Return the list of CSS classes to be applied to a given \ :js:class:`Cell`\ .
     * 
     * @param  {Number}     line    first coordinate of the cell
     * @param  {Number}     col     second coordinate of the cell
     * @return {Array}              list of CSS classes to be applied to this cell
     */
    gridXYCSS(line, col) {
        var MetaCell = this.grid[line];
        var res = "";

        if (MetaCell !== undefined) {

            MetaCell = MetaCell[col];

            if (MetaCell === null) {
                res+=(" no-border");
                return res;
            }

            if (MetaCell.isDec())
                res+=(" isdec");
            if (MetaCell.isFirst())
                res+=(" isfirst");
            if (MetaCell.isLast())
                res+=(" islast");

            if (MetaCell.isEntry()) {
                res+=(" entry");
            }
            if (MetaCell.infos.argPos0 % 2 === 0)
                res+=(" even0");
            else
                res+=(" odd0");
            if (MetaCell.isArgument()) {
                res+=(" arg");
            }
            if (MetaCell.isInfo()) {
                res+=(" info");
            }
            if (MetaCell.infos.type === 3) {
                res+=(" arg arg_special");
            }
            if (MetaCell.infos.secondary === true) {
                res+=(" secondary");
            }
            res+=(" index" + MetaCell.infos.index);

            res+=(" cell");
        }
        return res;
    }
    /**
     * This method applies the CSS properties of the cell whose cartesian coordinates are specified, to the given jquery
     * selector.
     * It will fetch the static CSS properties from the memory array \ :js:attr:`cssGrid`\, and compute the dynamic CSS
     * properties thanks to the \:js:attr:`props`\ dictionary of the considered \:js:class:`MetaCell`\ (and \:js:class:`SuperCell`\).
     * 
     * @param  {Number} line        first coordinate
     * @param  {Number} col         second coordinate
     * @param  {Object} selector    jquery selector of the element on which style will be applied
     * @return {undefined}
     */
    applyCSS(line, col, selector) {
        var MetaCell = this.grid[line];
        if (MetaCell !== undefined && MetaCell !== null) {
            MetaCell = MetaCell[col];
            var classes = this.cssGrid[line][col];  // static CSS
            if (MetaCell !== null) { // dynamic CSS (depending on cell's usage)
                if (MetaCell.props.error)
                    classes += " error";
                if (MetaCell.props.suggested)
                    classes += " suggested";
                if (MetaCell.props.source)
                    classes += " source";
                if (MetaCell.props.target)
                    classes += " target";
                if (MetaCell.props.apparatus_zone)
                    classes += " commentary_zone";
                if (MetaCell.props.critical_apparatus !== '' && MetaCell.props.critical_apparatus !== undefined)
                    classes += ' crit-ap';
                if (MetaCell.props.highlight_zone)
                    classes += ' highlight_zone';
                if (MetaCell.props.commentary_zone)
                    classes += " commentary_zone";
                if (MetaCell.isLast() && MetaCell.parent.props.user_comment !== "")
                    classes += " commentary_present";
                if (MetaCell.parent.props.symmetry_source !== undefined)
                    classes += " symmetry";
            }
            selector.addClass(classes);
        }
    }
    /**
     * Convert the table in decimal and export it as a JSON string.
     * The export format is as following:
     *
     * @example
     * //Example for a 1-argument table
     * {
     *      args: {
     *          argument1: [1.0, 2.0, ..., 90.0]
     *      },
     *      entry: [0.01745, 0.03490, ..., 1.0]
     * }
     *
     * @example
     * // Example for a 2-arguments table
     * {
     *      args: {
     *          argument1: [1.0, 2.0, ..., 90.0],
     *          argument2: [1.0, 2.0, ..., 90.0]
     *      },
     *      entry: [
     *          [0.02, 0.03, ..., 1.0],
     *          [0.04, 0.05, ..., 0.9],
     *          ...
     *          [1.0, 0.9, ..., 0.0]
     *      ]
     * }
     * 
     * @return {string}     JSON string representing the table
     */
    asDecimalJSON() {
        var args = {};
        for (var i=0; i<this.spec.args.length; i++) {
            args["argument" + String(i+1)] = [];
            for (var k=0; k<this.spec.args[i].nsteps; k++) {
                args["argument" + String(i+1)].push(this.fromPath([1,i]).zones[k].getSmartNumber().computeDecimal());
            }
        }
        var entry = [];
        for (var i=0; i<this.spec.args[0].nsteps; i++) {
            if (this.spec.args.length === 1) {
                entry.push(this.fromPath([0,0]).zones[i].getSmartNumber().computeDecimal());
            }
            else if (this.spec.args.length === 2) {
                entry.push([]);
                for (var j=0; j<this.spec.args[1].nsteps; j++) {
                    entry[i].push(this.fromPath([0,0]).zones[i].zones[j].getSmartNumber().computeDecimal());
                }
            }
        }
        var res = {
            args : args,
            entry : entry
        };
        res.template = JSON.parse(JSON.stringify(this.spec));
        return JSON.stringify(res);
    }
    /**
     * Export the table as a JSON string.
     * A \ :js:class:`SuperCell`\  is represented in the following format:
     * @example
     * // The representation of a SuperCell is as following:
     * {
     *      value: [0, 12, ..., 49],    // the list of the values of the child Cells of the SuperCell
     *      comment: "Hello there!",
     *      suggested: false
     * }
     *
     * @example
     * // Example for a 1-argument table export
     * {
     *      args: {
     *          argument1: [{}, {}, ..., {}]
     *      },
     *      entry: [{}, {}, ..., {}]
     * }
     * // where {} is the representation of a SuperCell
     *
     * @example
     * // Example for a 2-argument table export
     * {
     *      args: {
     *          argument1: [{}, {}, ..., {}],
     *          argument2: [{}, {}, ..., {}]
     *      },
     *      entry: [
     *          [{}, {}, ..., {}],
     *          [{}, {}, ..., {}],
     *          ...
     *          [{}, {}, ..., {}]
     *      ]
     * }
     * // where {} is the representation of a SuperCell
     * 
     * @return {string}     JSON string representing the table
     */
    asOriginalJSON() {
        var args = {};
        for (var i=0; i<this.spec.args.length; i++) {
            args["argument" + String(i+1)] = [];
            for (var k=0; k<this.spec.args[i].nsteps; k++) {
                var sc = this.fromPath([1,i]).zones[k];
                args["argument" + String(i+1)].push(sc.asJSON());
            }
        }
        var entry = [];
        for (var i=0; i<this.spec.args[0].nsteps; i++) {
            if (this.spec.args.length === 1) {
                var sc = this.fromPath([0,0]).zones[i];
                entry.push(sc.asJSON());
            }
            else if (this.spec.args.length === 2) {
                entry.push([]);
                for (var j=0; j<this.spec.args[1].nsteps; j++) {
                    var sc = this.fromPath([0,0]).zones[i].zones[j];
                    entry[i].push(sc.asJSON());
                }
            }
        }
        var res = {
            args : args,
            entry : entry
        };
        if (typeof this.edition_tables !== "undefined") {
            res.edition_tables = this.edition_tables;
        }
        res.symmetries = JSON.parse(JSON.stringify(this.symmetries));
        res.symmetries.effective_symmetry = undefined;
        res.template = JSON.parse(JSON.stringify(this.spec));
        return JSON.stringify(res);
    }
    /**
     * This method fills the table's values with the content of a JSONized table.
     * The string JSON used for the import must have the same format as the one return by \ :js:attr:`asOriginalJSON`\ .
     * The original specification of the import table must be given as well.
     *
     * If the template of the import table and the current one are different, a conversion will be performed.
     *
     * The import JSON string must have the following format:
     * A \ :js:class:`SuperCell`\  is represented in the following format:
     * @example
     * // The representation of a SuperCell is as following:
     * {
     *      value: [0, 12, ..., 49],    // the list of the values of the child Cells of the SuperCell
     *      comment: "Hello there!",
     *      suggested: false
     * }
     *
     * @example
     * // Example for a 1-argument table
     * {
     *      args: {
     *          argument1: [{}, {}, ..., {}]
     *      },
     *      entry: [{}, {}, ..., {}]
     * }
     * // where {} is the representation of a SuperCell
     *
     * @example
     * // Example for a 2-argument table
     * {
     *      args: {
     *          argument1: [{}, {}, ..., {}],
     *          argument2: [{}, {}, ..., {}]
     *      },
     *      entry: [
     *          [{}, {}, ..., {}],
     *          [{}, {}, ..., {}],
     *          ...
     *          [{}, {}, ..., {}]
     *      ]
     * }
     * // where {} is the representation of a SuperCell
     * 
     * @param  {string}   originalJSON    a JSON string representing the table to import
     * @param  {object}   spec            the template of the table to import
     * @param  {boolean}  convert         whether to convert or copy cell by cell
     * @return {undefined}
     */
    fromOriginalJSON(originalJSON, spec, convert=false) {
        // apparently trying to make a copy of originalJSON
        // but in fact assigning two names to the same variable
        var original = originalJSON;

        if (typeof original === "string"){
            // parse the string into JSON
            original = JSON.parse(original);
        }

        if (spec === undefined) {
            // set the specification to be the template property (definition of the table grid)
            spec = original.template;
        }

        var readonly = this.spec.readonly;
        // allow modification in the table
        this.spec.readonly = false;

        /* FILL THE ARGUMENTS */
        // for each table argument (one or two)
        for (let i = 0; i < this.spec.args.length; i++) {
            // take the values of the corresponding argument
            var originalArg = original.args["argument" + String(i+1)];
            // for each steps for this argument (columns or rows)
            for (var k = 0; k < this.spec.args[i].nsteps; k++) {
                // if there are no value associated in the array of arguments to the current index
                if (originalArg[k] === undefined)
                    break;
                // select the superCell corresponding to the current argument value
                let sc = this.fromPath([1,i]).zones[k];
                // fill the current argument superCell with the current value
                sc.fromJSON(originalArg[k], spec, convert);
            }
        }

        /* FILL THE ENTRIES */
        // for each number of steps of the first argument (i.e. table row)
        for (let i = 0; i < this.spec.args[0].nsteps; i++) {
            // if there are no value associated in the array of entries to the current index
            if (original.entry[i] === undefined)
                break;
            // if there is only one argument
            if (this.spec.args.length === 1) {
                // select the superCell corresponding and fill it with the value
                let sc = this.fromPath([0,0]).zones[i];
                sc.fromJSON(original.entry[i], spec, convert);
            // if it is a double entry table
            } else if (this.spec.args.length === 2) {
                // for each number of steps of the second argument (i.e. table columns)
                for (var j=0; j<this.spec.args[1].nsteps; j++) {
                    if (original.entry[i][j] === undefined)
                        break;
                    // fill the corresponding superCell with the current value
                    var sc = this.fromPath([0,0]).zones[i].zones[j];
                    sc.fromJSON(original.entry[i][j], spec, convert);
                }
            }
        }

        // if there is a symmetry table
        if (original.symmetries !== undefined) {
            for (var i=0; i<original.symmetries.length; i++) {
                this.addSymmetry(
                    original.symmetries[i].type, 
                    original.symmetries[i].parameter,
                    original.symmetries[i].sign,
                    original.symmetries[i].displacement,
                    original.symmetries[i].source,
                    original.symmetries[i].target,
                    original.symmetries[i].direction
                );
            }
        }

        // if it is a critical edition
        if (typeof original.edition_tables !== "undefined") {
            // add the metadata related to the table editions to the metaZone "edition_tables" property
            this.edition_tables = original.edition_tables;
        }

        // if there is already a hands on table defined
        if (this.infos.hot !== undefined)
            // update the values (?)
            this.infos.hot.render();
        this.history = [];
        this.redoList = [];
        this.undoing = false;
        this.redoing = false;
        this.spec.readonly = readonly;
    }

    // TODO fromDecimalJSON -> but is it really useful ?
    /**
     * Return all the \ :js:class:`SuperCell`\ s of the table, in a format allowing
     * to interpret the table as a function easily.
     * @example
     * >> {
     *     args: [
     *         [arg1_sc1, arg1_sc2, ...],
     *         [arg2_sc1, arg2_sc2, ...]
     *     ],
     *     entry: [
     *         [sc1_1, sc1_2, ...],
     *         [sc2_1, sc2_2, ...],
     *         ...
     *         [scn_1, scn_2, ...]
     *     ]
     * }
     * @return {Object}
     */
    asFunctionSuperCells() {
        var res = {
            args: []
        };
        for (var i=0; i<this.spec.args.length; i++) {
            res.args.push([]);
            //fill with arguments
            for (var k=0; k<this.spec.args[i].nsteps; k++) {
                res.args[i].push(this.zones[i+1].zones[k]);
            }
        }
        if (this.spec.args.length === 1) {
            res.entry = [];
            for (var i=0; i<this.spec.args[0].nsteps; i++) {
                res.entry.push(this.zones[0].zones[i]);
            }
        }
        else {
            res.entry = [];
            for (var i=0; i<this.spec.args[0].nsteps; i++) {
                res.entry.push([]);
                for (var j=0; j<this.spec.args[1].nsteps; j++) {
                    res.entry[i].push(this.zones[0].zones[i].zones[j]);
                }
            }
        }
        this.functionSuperCells = res;
        return res;
    }
    /**
     * Return a decimal version of the table, in a format allowing
     * to interpret the table as a function easily.
     * @example
     * >> {
     *     args: [
     *         [arg1_val1, arg1_val2, ...],
     *         [arg2_val1, arg2_val2, ...]
     *     ],
     *     entry: [
     *         [val1_1, val1_2, ...],
     *         [val2_1, val2_2, ...],
     *         ...
     *         [valn_1, valn_2, ...]
     *     ]
     * }
     * @return {Object}
     */
    asFunction() {
        var res = {
            args: []
        };
        for (var i=0; i<this.spec.args.length; i++) {
            res.args.push([]);
            //fill with arguments
            for (var k=0; k<this.spec.args[i].nsteps; k++) {
                res.args[i].push(this.zones[i+1].zones[k].getSmartNumber().computeDecimal());
            }
        }
        if (this.spec.args.length === 1) {
            res.entry = [];
            for (var i=0; i<this.spec.args[0].nsteps; i++) {
                res.entry.push(this.zones[0].zones[i].getSmartNumber().computeDecimal());
            }
        }
        else {
            res.entry = [];
            for (var i=0; i<this.spec.args[0].nsteps; i++) {
                res.entry.push([]);
                for (var j=0; j<this.spec.args[1].nsteps; j++) {
                    res.entry[i].push(this.zones[0].zones[i].zones[j].getSmartNumber().computeDecimal());
                }
            }
        }
        this.functionSuperCells = res;
        return res;
    }
    /**
     * Return the argument \ :js:class:`SuperCell`\  corresponding to the specified value.
     * @param  {Number} value          Value of the argument
     * @param  {Number} argumentIndex  Index of the argument
     * @param  {Number} precision      Precision of the comparison
     * @return {SuperCell}             SuperCell containing the desired argument
     */
    findArgument(value, argumentIndex=0, precision=1e-10) {
        if (this.functionSuperCells === undefined)
            this.asFunctionSuperCells();
        if (this.spec.args.length === 1) {
            var index = binarySearch(
                this.functionSuperCells.args[argumentIndex],
                value,
                false, 
                (sc, value) => (Math.abs(sc.getSmartNumber().computeDecimal() - value) < precision),
                (sc, value) => (sc.getSmartNumber().computeDecimal() < value)
            );
            return this.functionSuperCells.args[argumentIndex][index];
        }
        else { // same stuff as 1arg ?
            var index = binarySearch(
                this.functionSuperCells.args[argumentIndex],
                value,
                false, 
                (sc, value) => (Math.abs(sc.getSmartNumber().computeDecimal() - value) < precision),
                (sc, value) => (sc.getSmartNumber().computeDecimal() < value)
            );
            return this.functionSuperCells.args[argumentIndex][index];
        }
    }
    findArgumentRange(value_left, value_right, argumentIndex=0, precision=1e-10) {
        var arg1 = this.findArgument(value_left, argumentIndex, precision);
        var arg2 = this.findArgument(value_right, argumentIndex, precision);
        if (arg1 === undefined || arg2 === undefined) {
            return undefined;
        }
        var res = [];
        var parent = arg1.parent;
        for (var i=arg1.indexInParent; i<=arg2.indexInParent; i++) {
            res.push(parent.zones[i]);
        }
        return res;
    }
    /**
     * Retrieve the entries \ :js:class:`SuperCell`\  corresponding to the specified argument(s) \ :js:class:`SuperCell`\ (s).
     * @param  {SuperCell} args  The argument(s)
     * @return {SuperCell}       The corresponding entry
     */
    getEntriesByArguments(args) {
        if (args.length === 1) {
            if (this.spec.args.length === 1)
                return [this.fromPath([0, 0, args[0].indexInParent])];
            else {
                throw "In getEntriesByArguments: Unvalid arguments specified for accessing entry";
            }
        }
        else if (args.length === 2) {
            if (this.spec.args.length === 1)
                throw "In getEntriesByArguments: Unvalid arguments specified for accessing entry";
            if (args[0] !== undefined && args[1] !== undefined) {
                return [this.fromPath([0, 0, args[0].indexInParent, args[1].indexInParent])];
            }
            var scs = [];
            if (args[0] === undefined) {
                for (var i=0; i<this.spec.args[0].nsteps; i++) {
                    scs.push(this.fromPath([0, 0, i, args[1].indexInParent]));
                }
            }
            else if (args[1] === undefined) {
                for (var i=0; i<this.spec.args[1].nsteps; i++) {
                    scs.push(this.fromPath([0, 0, args[0].indexInParent, i]));
                }
            }
            return scs;
        }
    }
    /**
     * Retrieve the entry \ :js:class:`SuperCell`\  corresponding to the specified argument(s) \ :js:class:`SuperCell`\ (s).
     * @param  {SuperCell} args  The argument(s)
     * @return {SuperCell}       The corresponding entry
     */
    getEntryByArguments(args) {
        if (args.length === 1) {
            if (this.spec.args.length === 1)
                return this.fromPath([0, 0, args[0].indexInParent]);
            else {
                throw "In getEntryByArguments: Unvalid arguments specified for accessing entry";
            }
        }
        else if (args.length === 2) {
            if (this.spec.args.length === 1)
                throw "In getEntryByArguments: Unvalid arguments specified for accessing entry";
            else {
                if (args[0] !== undefined && args[1] !== undefined) {
                    return this.fromPath([0, 0, args[0].indexInParent, args[1].indexInParent]);
                }
                throw "In getEntryByArguments: Unvalid arguments specified for accessing entry";
            }
        }
        else
            throw "In getEntryByArguments: Unvalid arguments specified for accessing entry";
    }
    getArgumentRange(left, right, argumentIndex=0) {
        return this.findArgumentRange(left, right, argumentIndex);
    }
    addSymmetry(type, parameter, sign, displacement, sources, targets, direction, verbose) {
        var sym = {
            type: type,
            parameter: parameter,
            sign: sign,
            displacement: displacement,
            source: sources,
            target: targets,
            direction: direction
        };
        var argumentIndex = Number(sym.direction) - 1;
        var argumentList = this.getArgumentRange(sources[0], sources[1], argumentIndex);
        var targetArguments = undefined;
        if (targets) {
            targetArguments = this.getArgumentRange(targets[0], targets[1], argumentIndex);
        }
        this.symmetries.push(sym);
        var symmetry = AstronomicalSymmetry(type, parameter, sign, displacement);
        sym.effective_symmetry = symmetry;
        this.linkBySymmetrie(symmetry, argumentList, targetArguments, argumentIndex, verbose);
        // TODO permettre de faire Ctl+Z sur une symetrie
        this.infos.root.history = [];
    }
    applySymmetry(symmetry, sources, targets, argumentIndex) {
        var argumentList = this.getArgumentRange(sources[0], sources[1], argumentIndex);
        var targetArguments = undefined;
        if (targets) {
            targetArguments = this.getArgumentRange(targets[0], targets[1], argumentIndex);
        }
        // for 1-arg only
        var targetValues = undefined;
        if (targetArguments !== undefined) {
            targetValues = [];
            for (var i=0; i<targetArguments.length; i++) {
                targetValues.push(targetArguments[i].getSmartNumber().computeDecimal());
            }
        }
        if (argumentList === undefined) {
            if (verbose) {
                alert('Could not apply symmetry');
            }
            return ;
        }
        for (var i=0; i<argumentList.length; i++) {
            var link = symmetry.imputeNewLink(argumentList[i].getSmartNumber().computeDecimal());
            if (this.spec.args.length === 1) {
                var source = this.getEntryByArguments([argumentList[i]]);
                for (var j=0; j<link[1].length; j++) {
                    if (targetValues !== undefined && floatIndexOf(targetValues, link[1][j]) === -1)
                        continue;
                    var target_argument = this.findArgument(link[1][j]);
                    if (target_argument === undefined)
                        continue;
                    var target = this.getEntryByArguments([target_argument]);
                    if (!target.testAtLeastOneProp('suggested', false)) {
                        source.transferValue(target, link[2], false);
                    }
                }
            }
            else {
                // depending on argumentIndex ->
                if (argumentIndex === 0)
                    var sources = this.getEntriesByArguments([argumentList[i], undefined]);
                else
                    var sources = this.getEntriesByArguments([undefined, argumentList[i]]);
                for (var j=0; j<link[1].length; j++) {
                    if (targetValues !== undefined && floatIndexOf(targetValues, link[1][j]) === -1)
                        continue;
                    var target_argument = this.findArgument(link[1][j], argumentIndex);
                    if (target_argument === undefined)
                        continue;
                    // idem
                    if (argumentIndex === 0)
                        var targets = this.getEntriesByArguments([target_argument, undefined]);
                    else
                        var targets = this.getEntriesByArguments([undefined, target_argument]);
                    
                    for (var source of sources)
                        for (var target of targets) {
                            if (source.infos.argPos[1-argumentIndex] === target.infos.argPos[1-argumentIndex]) {
                                if (!target.testAtLeastOneProp('suggested', false)) {
                                    source.transferValue(target, link[2], false);
                                }
                            }
                        }
                    
                }
            }
        }
    }
    linkBySymmetrie(symmetry, argumentList, targetArguments, argumentIndex, verbose) {
        // for 1-arg only
        var targetValues = undefined;
        if (targetArguments !== undefined) {
            targetValues = [];
            for (var i=0; i<targetArguments.length; i++) {
                targetValues.push(targetArguments[i].getSmartNumber().computeDecimal());
            }
        }
        if (argumentList === undefined) {
            if (verbose) {
                alert('Could not apply symmetry');
            }
            return ;
        }
        var targetDone = [];
        for (var i=0; i<argumentList.length; i++) {
            var link = symmetry.imputeNewLink(argumentList[i].getSmartNumber().computeDecimal());
            
            if (this.spec.args.length === 1) {
                var source = this.getEntryByArguments([argumentList[i]]);
                for (var j=0; j<link[1].length; j++) {
                    if (targetValues !== undefined && floatIndexOf(targetValues, link[1][j]) === -1)
                        continue;
                    var target_argument = this.findArgument(link[1][j]);
                    if (target_argument === undefined)
                        continue;
                    var target = this.getEntryByArguments([target_argument]);
                    source.linkSuperCell(target, link[2]);
                    targetDone.push(link[1][j]);
                }
                source.transformLinkedSuperCells(source.isComplete());
            }
            else {
                // depending on argumentIndex ->
                if (argumentIndex === 0)
                    var sources = this.getEntriesByArguments([argumentList[i], undefined]);
                else
                    var sources = this.getEntriesByArguments([undefined, argumentList[i]]);
                for (var j=0; j<link[1].length; j++) {
                    if (targetValues !== undefined && floatIndexOf(targetValues, link[1][j]) === -1) {
                        console.log(link[1][j]);
                        continue;
                    }
                    var target_argument = this.findArgument(link[1][j], argumentIndex);
                    if (target_argument === undefined) {
                        continue;
                    }
                    // idem
                    if (argumentIndex === 0)
                        var targets = this.getEntriesByArguments([target_argument, undefined]);
                    else
                        var targets = this.getEntriesByArguments([undefined, target_argument]);
                    for (var source of sources) {
                        for (var target of targets) {
                            if (source.infos.argPos[1-argumentIndex] === target.infos.argPos[1-argumentIndex]) {
                                source.linkSuperCell(target, link[2]);
                                source.transformLinkedSuperCells(source.isComplete());
                            }
                        }
                    }
                    targetDone.push(link[1][j]);
                }
            }
        }
        if (verbose) {
            if (targetDone.length < targetArguments.length) {
                alert('Some targets values could not be imputed from symmetries');
            }
        }
    }
    /**
     * Returns a list of all the \ :js:class:`SuperCell`\ s in this \ :js:class:`MetaZone`\ 
     * @return {Array} The list of all \ :js:class:`SuperCell`\ s in this \ :js:class:`MetaZone`\ 
     */
    getAllSuperCells() {//TODO : rename
        if (this.supercellList !== undefined)
            return this.supercellList;
        var res = [];
        for (var i=0; i<this.superGrid.length; i++) {
            var line = this.superGrid[i];
            for (var j=0; j<line.length; j++) {
                if (line[j] === null)
                    continue;
                res.push(line[j]);
            }
        }
        this.supercellList = res;
        return this.supercellList;
        //return this.selectionToSuperCells([[0,0,this.R-1,this.C-1]]);
    }
    /**
     * Returns a list of all the \ :js:class:`MetaCell`\ s in this \ :js:class:`MetaZone`\ 
     * @return {Array} The list of all \ :js:class:`MetaCell`\ s in this \ :js:class:`MetaZone`\ 
     */
    getAllMetaCells() {//TODO : rename
        if (this.metacellList !== undefined)
            return this.metacellList;
        var res = [];
        for (var i=0; i<this.grid.length; i++) {
            var line = this.grid[i];
            for (var j=0; j<line.length; j++) {
                if (line[j] === null)
                    continue;
                res.push(line[j]);
            }
        }
        this.metacellList = res;
        return this.metacellList;
        //return this.selectionToSuperCells([[0,0,this.R-1,this.C-1]]);
    }
    clearAllSymmetries() {
        this.symmetries = [];
        this.clearAllLinks();
    }
    clearAllLinks() {
        var scs = this.getAllSuperCells()
        for (var i=0; i<scs.length; i++) {
            scs[i].props.symmetry_source = undefined;
            scs[i].linkedSuperCells = [];
        }
    }
    /**
     * Fill all the empty cells of the zone with the symbol '*'
     * Should be used before submitting (making public) the table
     * @return {undefined}
     */
    fillStarsIfEmpty() {
        var sc = this.getAllSuperCells();
        for (var i=0; i<sc.length; i++) {
            sc[i].fillStarsIfEmpty();
        }
        if (this.infos.hot !== undefined)
            this.infos.hot.render();
    }
    /**
     * Checks if this \ :js:class:`MetaZone`\  has empty cells.
     * @return {Boolean} true if the zone has empty cells
     */
    hasEmptyCells() {
        var sc = this.getAllSuperCells();
        for (var i=0; i<sc.length; i++) {
            for (var c=0; c<sc[i].zones.length; c++)
                if (sc[i].zones[c].val === "")
                    return true;
        }
        return false;
    }
    /**
     * Validate the whole zone
     * @return {undefined}
     */
    validateAll() {
        var sc = this.getAllSuperCells();
        for (var i=0; i<sc.length; i++) {
            sc[i].validateNonEmpty();
        }
        if (this.infos.hot !== undefined)
            this.infos.hot.render();
    }
    /**
     * Checks if the zone has unvalidated cells
     * @return {Boolean} true if the zone has unvalidated cells
     */
    hasUnvalidatedCells() {
        var sc = this.getAllSuperCells();
        for (var i=0; i<sc.length; i++) {
            for (var c=0; c<sc[i].zones.length; c++)
                if (sc[i].zones[c].props.suggested)
                    return true;
        }
        return false;
    }
    /**
     * Checks if the zone has non-empty unvalidated cells
     * @return {Boolean} true if the zone has non-empty unvalidated cells
     */
    hasNoneEmptyUnvalidatedCells() {
        var sc = this.getAllSuperCells();
        for (var i=0; i<sc.length; i++) {
            for (var c=0; c<sc[i].zones.length; c++)
                if (sc[i].zones[c].props.suggested && sc[i].isComplete())
                    return true;
        }
        return false;
    }
    /**
     * Returns true if the content has been altered since its creation (checking the history attribute)
     * @return {Boolean} true if the table has been altered
     */
    hasChanged() {
        for (var i=0; i < this.history.length; i++) {
            if (this.history[i] !== null) {
                return true;
            }
        }
        return false;
    }
    /**
     * Undo the last block of history (i.e. until the value null is encountered)
     * @return {undefined}
     */
    undo() {
        if (this.history.length === 0)
            return;
        while(!this.undoStep()) {
            if (this.history.length === 0)
                return;
        }
        while(this.undoStep()) {
            if (this.history.length === 0)
                return;
        }
    }
    /**
     * Redo the last cancelled block of history (i.e. until the value null is encountered)
     * @return {undefined}
     */
    redo() {
        if (this.redoList.length === 0)
            return;
        while(!this.redoStep()) {
            if (this.redoList.length === 0)
                return;
        }
        while(this.redoStep()) {
            if (this.redoList.length === 0)
                return;
        }
    }
    /**
     * Pop and revert one operation from this.history. 
     * Returns true if the state was not null
     * @return {Boolean}
     */
    undoStep() {
        this.undoing = true;
        if (this.history.length === 0) {
            this.undoing = false;
            return false;
        }
        var state = this.history.pop();
        this.redoList.push(state);
        if (state === null) {
            this.undoing = false;
            return false;
        }
        var cell = this.grid[state.cell.row][state.cell.col];
        var oldState = state.oldState;
        if ("val" in oldState) {
            cell.fill(oldState.val, undefined, false, true);
        }
        if ("suggested" in oldState) {
            cell.setProp("suggested", oldState.suggested, false, true);
        }
        this.undoing = false;
        return true;
    }
    /**
     * Redo one operation from this.redoList. 
     * Returns true if the state was not null
     * @return {Boolean}
     */
    redoStep() {
        this.redoing = true;
        if (this.redoList.length === 0) {
            this.redoing = false;
            return false;
        }
        var state = this.redoList.pop();
        if (state === null) {
            this.redoing = false;
            return false;
        }
        var cell = this.grid[state.cell.row][state.cell.col];
        var newState = state.newState;
        if ("val" in newState) {
            cell.fill(newState.val, undefined, false, true);
        }
        if ("suggested" in newState) {
            cell.setProp("suggested", newState.suggested, false, true);
        }
        this.redoing = false;
        return true;
    }

    /**
     * Return the un-ordered list of the decimal values of all entry-cells
     * @return {Array} un-ordered list of the decimal values of all entry-cells
     */
    entryPoints() { //rename + get
        var res = [];
        var SuperCells = this.selectionToSuperCells([[0,0,this.R-1,this.C-1]]);
        for (var i=0; i<SuperCells.length; i++) {
            if (SuperCells[i].isComplete() && SuperCells[i].isEntry())
                res.push(SuperCells[i].getSmartNumber().computeDecimal());
        }
        return res;
    }
    /**
     * Compute the mean of the entry
     * @return {Number} mean of the entry
     */
    mean() { //compute+
        // TODO
        return 0.0;
    }
    /**
     * Compute the median of the selected entry cells
     * @param  {Array} entryPoints  Array of entry cells
     * @return {Number}              median of the decimal values of entryPoints
     */
    median(entryPoints) {//compute+
        if (entryPoints === undefined) {
            var values = this.entryPoints();
        }
        else {
            var values = entryPoints;
        }
        
        values.sort( function(a,b) {return a - b;} );
        var half = Math.floor(values.length/2);
        if (values.length % 2)
            return values[half];
        else
            return (values[half-1] + values[half]) / 2.0;
    }
    /**
     * Compute the variance of the decimal values of all entry-cells
     * @return {Number} variance of the decimal values of all entry-cells
     */
    variance() {//compute+
        // TODO
        return 0.0;
    }
    /**
     * Compute the median absolute deviation (MAD) of the decimal values of the selected cells
     * @param  {Number} median       The median, if previously computed (for optimization)
     * @param  {Array} entryPoints  Array of entry cells
     * @return {Number}              median absolute deviation (MAD) of the decimal values of the selected cells
     */
    medianAbsoluteDeviation(median, entryPoints) { //rename
        if (entryPoints === undefined) {
            var values = this.entryPoints();
        }
        else {
            var values = entryPoints;
        }
        if (median === undefined)
            var median = this.median();
        for (var i=0; i<values.length; i++) {
            values[i] = Math.abs(values[i] - median);
        }
        values.sort( function(a,b) {return a - b;} );
        var half = Math.floor(values.length/2);
        if (values.length % 2)
            return values[half];
        else
            return (values[half-1] + values[half]) / 2.0;
    }
    /*  =============================================================================================
     *  =========  The following block of methods defines operations for selecting subzones =========
     *  =========================================================================================== */
    
    
    /**
     * Returns the list of \ :js:class:`MetaCell`\ s corresponding to the given selection
     * @param  {Array} selection Selection of cells, in the \ :js:class:`BoundingBoxes`\  format [ [x_0, y_0, x_1, y_1], ..., ]
     * @return {Array}           corresponding \ :js:class:`MetaCell`\ s
     */
    selectionToMetaCells(selection) {
        var res = [];
        var zone = this;
        for (var ind=0; ind<selection.length; ind++) {
            var toprow = Math.min(selection[ind][0], selection[ind][2]);
            var botrow = Math.max(selection[ind][0], selection[ind][2]);
            var leftcol = Math.min(selection[ind][1], selection[ind][3]);
            var rightcol = Math.max(selection[ind][1], selection[ind][3]);
            for (var row=toprow; row<=botrow; row++) {
                for (var col=leftcol; col<=rightcol; col++) {
                    if (zone.grid[row] === undefined)
                        continue;
                    if (zone.grid[row][col] !== null)
                        res.push(zone.grid[row][col]);
                }
            }
        }
        return res;
    }
    /**
     * Returns the list of \ :js:class:`SuperCell`\ s corresponding to the given selection
     * @param  {Array} selection Selection of cells, in the \ :js:class:`BoundingBoxes`\  format [ [x_0, y_0, x_1, y_1], ..., ]
     * @return {Array}           corresponding \ :js:class:`SuperCell`\ s
     */
    selectionToSuperCells(selection) {
        var res = [];
        var MetaCells = this.selectionToMetaCells(selection);
        for (var i=0; i<MetaCells.length; i++) {
            if (MetaCells[i] === null || MetaCells[i] === undefined)
                continue;
            var currentSuperCell = MetaCells[i].parent;
            if (i === 0 || res[res.length-1] !== currentSuperCell)
                res.push(currentSuperCell);
        }
        return res;
    }
    /**
     * Checks whether a selection corresponds entirely to a "super selection" or not
     * @param  {Array}  selections  Selection of cells, in the \ :js:class:`BoundingBoxes`\  format [ [x_0, y_0, x_1, y_1], ..., ]
     * @return {Boolean}            true if no \ :js:class:`SuperCell`\ s are only partially selected in selection
     */
    isSelectionSuperSelect(selections) {
        var onlySingleColumns = true;
        var zone = this;
        for (var i=0; i<selections.length; i++) {
            if (zone.grid[selections[i][0]][selections[i][1]] !== null && !zone.grid[selections[i][0]][selections[i][1]].isFirst())
                return false;
            if (zone.grid[selections[i][2]][selections[i][3]] !== null && !zone.grid[selections[i][2]][selections[i][3]].isLast())
                return false;
            if (selections[i][1] !== selections[i][3])
                onlySingleColumns = false;
        }
        //return true if there is at least one non read only cell
        if (this.selectionToSuperCells(selections).length === 0)
            return false;
        if (onlySingleColumns)
            return false;
        return true;
    }
    /**
     * Translates a selection vertically with the given step.
     * The selection is modified in-place.
     * @param  {Array}  selections Selection of cells, in the \ :js:class:`BoundingBoxes`\  format [ [x_0, y_0, x_1, y_1], ..., ]
     * @param  {Number} delta      Step of the translation. The sign specifies the direction
     * @return {undefined}
     */
    moveSelectionsVertically(selections, delta) {
        for (var i=0; i<selections.length; i++) {
            if (selections[i][0]+delta>=0 && selections[i][2]+delta>=0) {
                selections[i][0] += delta;
                selections[i][2] += delta;
            }
        }
    }
    /**
     * Translates a selection vertically with the given step.
     * The selection is modified in-place.
     * @param  {Array}  selections Selection of cells, in the \ :js:class:`BoundingBoxes`\  format [ [x_0, y_0, x_1, y_1], ..., ]
     * @param  {Number} delta      Step of the translation. The sign specifies the direction
     * @return {undefined}
     */
    moveSelectionsHorizontally(selections, delta) {
        for (var i=0; i<selections.length; i++) {
            if (selections[i][1]+delta>=0 && selections[i][3]+delta>=0) {
                selections[i][1] += delta;
                selections[i][3] += delta;
            }
        }
    }
    /**
     * Translates a selection horizontally along the second argument.
     * The selection is modified in-place.
     * @param  {Array}  selections Selection of cells, in the \ :js:class:`BoundingBoxes`\  format [ [x_0, y_0, x_1, y_1], ..., ]
     * @param  {Number} delta      Step of the translation. The sign specifies the direction
     * @return {undefined}
     */
    moveSelectionsNextSuperCell(selections, delta) {
        // TODO proprifier
        var zone = this;
        for (var i=0; i<selections.length; i++) {
            var scs = this.selectionToSuperCells([selections[i]]);
            var sc1 = scs[0];
            var sc2 = scs[scs.length - 1];
            if (delta < 0) {
                var nSc1 = null;
                for (var counter=1; counter<10; counter++) {
                    nSc1 = zone.superGrid[sc1.zones[0].row][sc1.zones[0].col-counter];
                    if (nSc1 !== null)
                        break;
                }
                var nSc2 = null;
                for (var counter=1; counter<10; counter++) {
                    nSc2 = zone.superGrid[sc2.zones[0].row][sc2.zones[0].col-counter];
                    if (nSc2 !== null)
                        break;
                }
            }
            else {
                var nSc1 = null;
                for (var counter=1; counter<10; counter++) {
                    nSc1 = zone.superGrid[sc1.zones[sc1.zones.length-1].row][sc1.zones[sc1.zones.length-1].col+counter];
                    if (nSc1 !== null)
                        break;
                }
                var nSc2 = null;
                for (var counter=1; counter<10; counter++) {
                    nSc2 = zone.superGrid[sc2.zones[sc2.zones.length-1].row][sc2.zones[sc2.zones.length-1].col+counter];
                    if (nSc2 !== null)
                        break;
                }
            }
            selections[i] = [nSc1.zones[0].row, nSc1.zones[0].col, nSc2.zones[0].row, nSc2.zones[0].col];
        }
    }
    /**
     * Broaden the selection at \ :js:class:`SuperCell`\  level.
     * The selection is modified in-place.
     * @param  {Array} selectionsOriginal Selection of cells, in the \ :js:class:`BoundingBoxes`\  format [ [x_0, y_0, x_1, y_1], ..., ]
     * @return {undefined}
     */
    superSelect(selectionsOriginal) { //extendToSuper
        var selections = [];
        var zone = this;
        if (this.isSelectionSuperSelect(selectionsOriginal)) {
            for (var i=0; i<selectionsOriginal.length; i++) {
                //
                var scs = this.selectionToSuperCells([selectionsOriginal[i]]);
                
                var sc1 = scs[0];
                var sc2 = scs[scs.length-1];
                var cell1 = sc1.zones[0];
                var cell2 = sc2.zones[0];
                selections.push([cell1.row, cell1.col, cell2.row, cell2.col]);
            }
            return selections;
        }
        for (var ind=0; ind<selectionsOriginal.length; ind++) {
            var mfirst = zone.grid[selectionsOriginal[ind][0]][selectionsOriginal[ind][1]];
            var msecond = zone.grid[selectionsOriginal[ind][2]][selectionsOriginal[ind][3]];
            var changed = false;
            if (mfirst.col <= msecond.col) {
                var m1 = mfirst;
                var m2 = msecond;
            }
            else {
                changed = true;
                var m1 = msecond;
                var m2 = mfirst;
            }
            var selection = [mfirst.row, mfirst.col, msecond.row, msecond.col];

            var left = m1.parent.zones[0].col;
            var right = m2.parent.zones[m2.parent.zones.length-1].col;

            if (changed)
                selections.push([selection[0], right, selection[2], left]);
            else
                selections.push([selection[0], left, selection[2], right]);
        }
        return selections;
    }
    /**
     * Expands the selection to the whole column (strictly speaking along the second argument arg1).
     * The selection is **NOT** modified in-place, but a new selection is returned.
     * @param  {Array} selectionsOriginal Selection of cells, in the \ :js:class:`BoundingBoxes`\  format [ [x_0, y_0, x_1, y_1], ..., ]
     * @return {Array}                     Result of the expansion, in the \ :js:class:`BoundingBoxes`\  format [ [x_0, y_0, x_1, y_1], ..., ]
     */
    columnSelect(selectionsOriginal) { // understand as all arg1 select ------- rename extendToColumn
        var selections = [];
        var zone = this;
        for (var ind=0; ind<selectionsOriginal.length; ind++) {
            var mfirst = zone.grid[selectionsOriginal[ind][0]][selectionsOriginal[ind][1]];
            var msecond = zone.grid[selectionsOriginal[ind][2]][selectionsOriginal[ind][3]];
            var changed = false;
            if (mfirst.row <= msecond.row) {
                var m1 = mfirst;
                var m2 = msecond;
            }
            else {
                changed = true;
                var m1 = msecond;
                var m2 = mfirst;
            }
            var selection = [mfirst.row, mfirst.col, msecond.row, msecond.col];
            
            if (zone.spec.args.length === 2) {
                var up = zone.fromPath([m1.infos.type, m1.infos.index, 0, m1.infos.argPos1, 0]).row;
                var down = zone.fromPath([m2.infos.type, m2.infos.index, zone.spec.args[0].nsteps-1, m2.infos.argPos1, 0]).row;
            }
            else {
                var up = zone.fromPath([m1.infos.type, m1.infos.index, 0, 0]).row;
                var down = zone.fromPath([m2.infos.type, m2.infos.index, zone.spec.args[0].nsteps-1, 0]).row;
            }
            if (changed)
                selections.push([down, selection[1], up, selection[3]]);
            else
                selections.push([up, selection[1], down, selection[3]]);
        }
        return selections;
    }
    /**
     * Expands the selection to the whole column (strictly speaking along the first argument arg0).
     * The selection is **NOT** modified in-place, but a new selection is returned.
     * @param  {Array} selectionsOriginal Selection of cells, in the \ :js:class:`BoundingBoxes`\  format [ [x_0, y_0, x_1, y_1], ..., ]
     * @return {Array}                     Result of the expansion, in the \ :js:class:`BoundingBoxes`\  format [ [x_0, y_0, x_1, y_1], ..., ]
     */
    lineSelect(selectionsOriginal) { //rename extendToLine : selections -> extended_selections (idem pour les autres au-dessus)
        var selections = [];
        var zone = this;
        for (var ind=0; ind<selectionsOriginal.length; ind++) {
            var mfirst = zone.grid[selectionsOriginal[ind][0]][selectionsOriginal[ind][1]];
            var msecond = zone.grid[selectionsOriginal[ind][2]][selectionsOriginal[ind][3]];
            var changed = false;
            if (mfirst.col <= msecond.col) {
                var m1 = mfirst;
                var m2 = msecond;
            }
            else {
                changed = true;
                var m1 = msecond;
                var m2 = mfirst;
            }
            
            var selection = [mfirst.row, mfirst.col, msecond.row, msecond.col];
            
            if (m1.isArgument() && m2.isArgument() && m1.zoneCoordinates[0] === 0 && m2.zoneCoordinates[0] === 0) {
                selections.push(this.superSelect([selection])[0]);
                continue;
            }
            if (m1.isArgument() && m2.isArgument() && zone.spec.args.length === 1) {
                selections.push(this.superSelect([selection])[0]);
                continue;
            }

            if (zone.spec.args.length === 2) {
                var left = zone.fromPath([m1.infos.type, m1.infos.index, m1.infos.argPos0, 0, 0]).col;
                var right = zone.fromPath([m2.infos.type, m2.infos.index, m2.infos.argPos0, zone.spec.args[1].nsteps-1, 0]).col + Math.max(zone.spec.args[1].ncells, zone.spec.entries[0].ncells)-1;
            }
            else {
                var left = zone.fromPath([m1.infos.type, m1.infos.index, m1.infos.argPos0, 0]).col;
                var right = zone.fromPath([m2.infos.type, m2.infos.index, m2.infos.argPos0, zone.spec.entries[0].ncells-1]).col;
            }
            if (changed)
                selections.push([selection[0], right, selection[2], left]);
            else
                selections.push([selection[0], left, selection[2], right]);
        }
        return selections;
    }
}

