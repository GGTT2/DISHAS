/*jshint esversion: 6 */

/* global $ */
/* global Table1ArgZone */
/* global Table2ArgZone */

/*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////*/

/*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////*/
class PublicTable {
    /**
     * Class defining HTML tables for the front office interface
     *
     * tableContent = {
     *     original: {                                                      /// TABLE VALUES (value_original field in the database)
     *         args: { argument1: […], argument2: […] },                    // all the argument values of the table
     *         entry: [ […], […], […], … ],                                 // all the entry values of the table
     *         symmetries: [],                                              // symmetry stuffs
     *         (edition_tables : { … })                                     // if critical edition: info about the base editions
     *      },
     *      differenceOriginal: [(…)]                                       // data to generate a difference table in case there is one
     *      astronomical_parameter_sets: […],                               // ids of the astronomical parameter sets used in the table ?
     *      edited_text: 57                                                 // edited text id
     *      table_type: 6                                                   // table type id
     *
     *      template: {                                                     /// TEMPLATE SPECIFICATION : TABLE DIMENSIONS (i.e. the grid)
     *                                                                      /// TABLE CONTENT ENTITY : getTemplate() method
     *            "table_type": "23",                                       // table type associated with the table content
     *            "readonly": true,                                         // is the table editable? Always true in this Table Edition record page
     *            "args": [
     *              {                                                       /// INFORMATION ABOUT THE FIRST ARGUMENT
     *                "name": "Moon argument of anomaly",                   // name of the first argument
     *                "type": "integer_and_sexagesimal",                    // type of number used in the argument
     *                "nsteps": 180,                                        // number of rows
     *                "ncells": 2,                                          // number of cells to write the argument
     *                "decpos": 1                                           // number of cells before fractional part (position of the beginning of the decimals)
     *              },
     *              ({                                                      /// INFORMATION ABOUT THE SECOND ARGUMENT (in case there is one)
     *                 "name": "a1",                                        // name of the second argument
     *                 "type": "integer_and_sexagesimal",                   // type of number used in the argument
     *                 "nsteps": 90,                                        // number of columns
     *                 "ncells": 2,                                         // number of cells to write the argument
     *                 "decpos": 1                                          // number of cells before fractional part (position of the beginning of the decimals)
     *               })
     *            ],("cwidth" : 3)                                          // width (number of cells) of a superColumn (in case there is a second argument)
     *            "entries": [
     *              {                                                       /// INFORMATION ABOUT THE ENTRIES
     *                "name": "Entry",                                      // entry type
     *                "type": "sexagesimal",                                // type of number used in the entries
     *                "ncells": 3,                                          // number of cells occupied by an entry value
     *                "decpos": 1                                           // position of the beginning of the decimals (decimal position)
     *              }
     *              (,{                                                     /// INFORMATION ABOUT THE DIFFERENCE TABLE (in case there is one)
     *                "name": "Difference",                                 // difference type
     *                "type": "sexagesimal",                                // type of number used in the entries of the difference table
     *                "ncells": 3,                                          // number of cells occupied by an entry value
     *                "decpos": 1                                           // position of the beginning of the decimals (decimal position)
     *              })
     *            ]
     *          }
     * }
     *
     * @param  {Object}   publicTableContainer   Instance containing every table contents associated with an edition
     * @param  {Integer}  tableNumber            Number of the table in the edition
     * @return {PublicTable}
     */
    constructor(publicTableContainer, tableNumber) {
        /**
         * Instance of the class PublicTableContainer, containing all PublicTables in its "tables" property
         * @type {Object}
         */
        this.tableContainer = publicTableContainer;

        /**
         * Id of the HTML table containing the table values
         * @type {string}
         */
        this.containerId = `table-${tableNumber}`;

        /**
         * Number of the table in the edition
         * @type {Integer}
         */
        this.tableNumber = tableNumber;

        /**
         * Metadata concerning the edition
         * @type {Object}
         */
        this.metadata = {
            "edited_text": null,
            "table_type": null,
            "mathematical_parameter_set": null,
            "astronomical_parameter_sets": null,
            "table_content": null,
            "siglum": null
        };

        /**
         * Template defining the grid and unit used in the table
         * @type {null|object}
         */
        this.template = null;

        /**
         * CSV string of the table
         * @type {string}
         */
        this.csv = "";

        /**
         * List of zones for this table (can be multiple, for example in the case of difference tables)
         * @type {Array}
         */
        this.zones = [];

        /**
         * Number of rows there is in the table
         * @type {int}
         */
        this.rowNumber = 0;

        /**
         * Number of columns there is in the table
         * @type {int}
         */
        this.columnNumber = 0;

        /**
         * Object containing all the cells that contain critical apparatus :
         *
         * {
         *      "tableNb-rowNb-colNb": nbOfVariantsInThisCell,
         *      "tableNb-rowNb-colNb": nbOfVariantsInThisOtherCell,
         * }
         *
         * @type {{}}
         */
        this.critAppCells = {};

        /**
         * Object containg all the cells that have a comment associated with them
         *
         * {
         *     "tableNb-rowNb-colNb": "idOfTheHTMLComment",
         * }
         * @type {{}}
         */
        this.commentCells = {};

        /**
         * Currently selected zone
         * @type {MetaZone}
         */
        this.selectedMetaZone = undefined;

        /**
         * Indicate if the critical apparatus was generated automatically with Cate
         * @type {boolean}
         */
        this.isCate = false;

        /**
         * This method takes an a row and a column number and return the corresponding MetaCell
         * @param row
         * @param col
         * @return {*}
         */
        this.coordinatesToMetaCell = (row, col) => {
            return this.zones[0].grid[row][col];
        };

        /**
         * Print the table on screen. To be called when a modification is made
         * and the graphic table is not up to date.
         * Useful for the "fromOriginalJSON" method of the MetaZone Class
         * @return {undefined}
         */
        this.render = () => {};

        /**
         * Refocus the table (by clicking on it)
         * @return {undefined}
         */
        this.refocus = () => {
            $(this.containerId).click();
        };

        /**
         * This function generates all the classes names corresponding to all the column covers by a superCell
         * in order to add the to the metadata title corresponding to a comment associated to a superCell.
         * That allows this title to be highlighted when clicking on a column header
         * or to select the superCell when clicking on a comment in the sidebar
         *
         * @param colMin {int} : col number of the cell the more on the left
         * @param colMax {int} : col number of the cell the more on the right
         * @return {array}
         */
        this.getSuperCellCol = (colMin, colMax) => {
            let colClasses = [];
            for (let i = (colMax - colMin) - 1; i >= 0; i--) {
                colClasses.push(`${this.tableNumber}-col-${colMin+i}`);
            }
            return colClasses;
        };

        /**
         * This method returns the column number of the last cell of a SuperCell
         * when giving a metaCell and its col number
         *
         * @param {object} metaCell
         * @return {int}
         */
        this.getLastColOfSuperCell = (metaCell) => {
            let nbOfCellsInSuperCell = metaCell.parent.positions.length;
            let colNumber = metaCell.coordinates[1];
            return colNumber + (nbOfCellsInSuperCell-metaCell.indexInParent);
        };
    }

    /**
     * Create the table from the specified \:js:class:`MetaZone`\
     * @param  {MetaZone} zone    specified \:js:class:`MetaZone`\
     * @return {undefined}
     */
    createFromZone(zone) {
        this.selectedMetaZone = zone;
        this.zones = [zone];
        this.colWidth = [];
        for (let j = 0; j < this.selectedMetaZone.C; j++) {
            let width = 50;
            for (let i = 0; i < this.selectedMetaZone.R; i++) {
                let MetaCell = this.selectedMetaZone.grid[i];
                if (MetaCell === undefined)
                    continue;
                MetaCell = MetaCell[j];
                if (MetaCell === undefined || MetaCell === null)
                    continue;
                if (MetaCell.isFirst() && (MetaCell.infos.position <= MetaCell.spec.decpos - 1)) {
                    width = 100;
                }
            }
            this.colWidth.push(width);
        }
        this.nargs = zone.spec.args.length;
    }

    /**
     * generateHtmlTable
     *
     * This method generates the HTML grid and fills it with the value of the table content
     * It adds the corresponding classes to cells containing critical apparatus and comments
     */
    generateHtmlTable(){
        // Add to the table container a new table in which to display the table content
        $("#tables").append(`<div id="div-table-${this.tableNumber}" class="div-table">
                                 <table><tbody id="table-${this.tableNumber}"></tbody></table>
                             </div>`);

        let htmlTable = $(`#table-${this.tableNumber}`);
        let cssClasses = this.selectedMetaZone.cssGrid;
        this.rowNumber = cssClasses.length; // assign the number of row to the table properties

        // create a column header
        htmlTable.append(`<tr id="${this.tableNumber}-col-header" style="height: 23px;"></tr>`);
        let columnHeaders = $(`#${this.tableNumber}-col-header`);

        // for each array in the css grid, make a row
        for (let i = 0; i < cssClasses.length; i++) {
            // create a new row and put a cell for the row header at the beginning
            htmlTable.append(`<tr id="${this.tableNumber}-row-${i}" style="height: 23px"><td id="${this.tableNumber}-row-header-${i}" class="table-header">${i+1}</td></tr>`);

            let row = cssClasses[i];
            this.columnNumber = row.length; // assign the number of column to the table properties

            // for each element in a row, make a cell and add css classes to it
            for (let j = 0; j < row.length; j++) {

                /* COLUMN HEADER GENERATION */
                if (i === 0){ // Do the following only once (when the first row is currently being generated)
                    // create an empty cell as the header for the column of row headers
                    if (j === 0){columnHeaders.append(`<td id="${this.tableNumber}-table-header-0" class="table-header">╲</td>`);}
                    // if the current column is the first of a super cell, set the cell to have the "isfirst" class
                    let isFirst = row[j].includes("isfirst") ? "isfirst" : "";
                    // append to the column header row a new cell
                    columnHeaders.append(`<td id="${this.tableNumber}-col-header-${j}" class="table-header ${isFirst}">${toColumnName(j+1)}</td>`);
                }

                // select the metaCell matching the coordinates
                let metaCell = this.coordinatesToMetaCell(i, j);
                // create an html element to add to the table
                let htmlCell = document.createElement("td");
                htmlCell.setAttribute("id", `${this.tableNumber}-${i}-${j}`); // add the coordinates as an id to this cell
                // set the array containing all classes to add to the current cell
                let classes = row[j].split(" ");
                // define a variable containing the number of variants in this cell
                let nVariants = 0;

                if (metaCell !== null) {
                    // put the numerical value in it
                    htmlCell.append(metaCell.val);
                    metaCell.coordinates = [i,j]; // add coordinates to metaCell
                    metaCell.htmlPosition = [i+1,toColumnName(j+1)]; // add coordinates of the cell as displayed in the HTML table
                    metaCell.props.comment = false; // add a comment property to the MetaCell

                    let sidebar = $("#side-comment");

                    // if its parent (i.e. the SuperCell) is associated with a comment
                    if (isDef(metaCell.parent.props.user_comment) && metaCell.parent.props.user_comment !== "") {
                        metaCell.props.comment = true;
                        classes.push("commentary_present"); // add the "commentary_present" class to the classList

                        let lastCol = this.getLastColOfSuperCell(metaCell);
                        // associate the id of the comment in the metadata to the cell;
                        this.commentCells[`${this.tableNumber}-${i}-${j}`] = `${this.tableNumber}-title-${i}_${lastCol}`;

                        // if the current cell is the first of its superCell
                        if (classes.includes("isfirst")){
                            // The id of the cell comment is structured like this : rowNumber_lastColumnOfTheSuperCellNumber
                            let colClasses = this.getSuperCellCol(j, lastCol);
                            sidebar.append(`<table id="${this.tableNumber}-table-${i}_${lastCol}" class="sidebar-metadata unfocus side-table-${this.tableNumber}">
                                                <tbody>
                                                    <th colspan="5"></th>
                                                    <tr>
                                                        <th scope="row" colspan="2">
                                                            <h4 id="${this.tableNumber}-title-${i}_${lastCol}" class="metadata comment ${this.tableNumber}-row-${i} ${colClasses.join(' ')}">
                                                                <span class="metadata comment row-${i} ${colClasses.join(' ')}">
                                                                    ${toColumnName(j+1)}${i+1} : ${toColumnName(lastCol)}${i+1}
                                                                </span>
                                                            </h4>
                                                        </th>
                                                        <td class="dataCell" colspan="3">${metaCell.parent.props.user_comment}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <hr class="unfocus side-table-${this.tableNumber}"/>`);
                        }
                    }

                    sidebar = $("#side-crit-app");

                    // if there is a critical apparatus associated with the cell
                    if (isDef(metaCell.props.critical_apparatus) && metaCell.props.critical_apparatus !== "") {
                        classes.push("crit-ap"); // add the "crit-ap" class to the classList

                        // count the number of variants in the apparatus by counting the line breaks (add one to count the first line)
                        nVariants = occurrences(metaCell.props.critical_apparatus, "\n") + 1;
                        this.critAppCells[`${this.tableNumber}-${i}-${j}`] = nVariants;

                        // create a table to store metadata
                        sidebar.append(`<table id="${this.tableNumber}-table-${i}-${j}" class="sidebar-metadata unfocus side-table-${this.tableNumber}">
                                            <tbody>
                                                <th colspan="5"></th>
                                                <tr>
                                                    <th scope="row" colspan="1">
                                                        <h4 id="${this.tableNumber}-title-${i}-${j}" class="metadata ${this.tableNumber}-row-${i} ${this.tableNumber}-col-${j}">
                                                            <span class="metadata row-${i} col-${j}">
                                                                ${toColumnName(j + 1)}${i + 1}
                                                            </span>
                                                        </h4>
                                                    </th>
                                                    <td colspan="4" class="dataCell">${metaCell.props.critical_apparatus.replace(/\n/g, "<br/>")}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <hr class="unfocus side-table-${this.tableNumber}"/>`);
                    }
                }
                htmlCell.setAttribute("data-nVariants", nVariants); // put the number of variant in the element attributes

                // add all the classes to the DOM element (and remove the empty element in it in order not to throw an error)
                htmlCell.classList.add(... classes.filter(Boolean));
                // add row coordinates to the classes of the DOM element and a general class for all table cells
                htmlCell.classList.add(... [`${this.tableNumber}-row-${i}`,`${this.tableNumber}-col-${j}`, "table-cell"]);
                // add the cell to the table row
                document.getElementById(`${this.tableNumber}-row-${i}`).appendChild(htmlCell);
            }
        }

        if (this.tableContainer.isCellCritApp){
            this.generateHeatMap();
        }

        // define argument names to display in the upper left corner of the table
        this.displayArgumentName();
    }

    /**
     * this method generate an heatmap on the table cells that contain critical apparatus
     * the more variants there is in a cell, the more saturated the color
     */
    generateHeatMap(){
        let cellValues = this.critAppCells;
        let minVariants = Math.min(...Object.values(cellValues)); // get the minimum number of variants on a same cell
        let maxVariants = Math.max(...Object.values(cellValues)); // get the maximum number of variants on a same cell

        // if all cells have the same number of variants
        if (minVariants === maxVariants){
            // select all cells that have the "crit-ap" class
            let appCritCells = document.getElementsByClassName("crit-ap");
            for (let i = appCritCells.length - 1; i >= 0; i--) {
                let cell = appCritCells[i];
                // and add a background color to all of them
                cell.style.backgroundImage = "linear-gradient(0, hsl(190, 70%, 65%), hsl(190, 70%, 65%))";
            }
        } else { // if there is variation in the number of variants attached to a cell
            // compute the lightness delta between each number of variants
            let colorVariation = 30/(maxVariants-minVariants);
            // for each cell containing a critical apparatus
            for (let i = Object.keys(cellValues).length - 1; i >= 0; i--) {
                let cell = document.getElementById(Object.keys(cellValues)[i]);
                // get the number of variants attached to this cell
                let nVariants = cell.getAttribute("data-nVariants");
                // compute the color value corresponding to this number of variants
                let colorPercent = 85-(colorVariation*(nVariants-minVariants));
                cell.style.backgroundImage = `linear-gradient(0, hsl(190, 70%, ${colorPercent}%), hsl(190, 70%, ${colorPercent}%))`;
            }
        }
    }

    /**
     * This function takes the argument names of a table and defines the string that will be displayed
     * in the upper left corner to indicate the argument names
     */
    displayArgumentName(){
        let tableTemplate = this.template;
        let width = 50 * (tableTemplate.args[0].ncells); // width of the argument column (50px * nb of cells for the argument)

        // define the number of characters visible in the table
        let nchar = Math.floor(width / 6);

        // select the upper left cell in the table
        let topLeftCorner = $(`#${this.tableNumber}-0-0`);
        let argNames;

        if (tableTemplate.mean_motion){
            let units = `${tableTemplate.args[0].unitName.capitalize()} (${tableTemplate.args[0].subUnitName})`;
            /*if (units.length > nchar) {
                units = `${units.slice(0, nchar)}…`;
            }*/
            topLeftCorner.html('<div class="argument-names"><b>' + units + '</b></div>');
            return;
        }

        // if it is a double argument table
        if (tableTemplate.args.length === 2) {
            // take name of both arguments
            let argName1 = tableTemplate.args[0].name;
            let argName2 = tableTemplate.args[1].name;
            argNames = `${argName1} ╲ ${argName2}`;

            // if argNames exceeds the number of visible characters, truncate it
            if (argNames.length > nchar) {
                argNames = `${argName1.slice(0, Math.floor(nchar / 2))}… ╲ ${argName2.slice(0, Math.floor(nchar / 2))}…`;
            }
        } else {
            argNames = tableTemplate.args[0].name;
            if (argNames.length > nchar) {
                argNames = `${argNames.slice(0, nchar)}…`;
            }
        }
        topLeftCorner.html('<div class="argument-names"><b>' + argNames + '</b></div>');
    }


    /**
     * This method fills the table's values with the content of a JSONized table.
     * The string JSON used for the import must have the same format as the one return by \:js:attr:`asOriginalJSON`\.
     * The original specification of the import table must be given as well.
     *
     * If the template of the import table and the current one are different, a conversion will be performed.
     *
     * The import JSON (tableValues) string must have the following format:
     *
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
     * @param  {string}   tableValues    a JSON string representing the table to import
     * @return {undefined}
     */
    fillZones(tableValues) {
        if (typeof tableValues === "string"){
            // parse the string into JSON
            tableValues = JSON.parse(tableValues);
        }

        // set the specification to be the template property (definition of the table grid)
        let spec = this.template;

        /* FILL THE ARGUMENTS */
        // for each table argument (one or two)
        for (let i = 0; i < spec.args.length; i++) {
            // take the values of the corresponding argument
            let argValues = tableValues.args[`argument${i+1}`];
            // for each steps for this argument (columns or rows)
            for (let k = 0; k < spec.args[i].nsteps; k++) {
                // if there are no value associated in the array of arguments to the current index
                if (argValues[k] === undefined)
                    break;
                // select the superCell corresponding to the current argument value
                let sc = this.zones[0].fromPath([1,i]).zones[k];
                // fill the current argument superCell with the current value
                sc.fromJSON(argValues[k], spec, false);
            }
        }

        /* FILL THE ENTRIES */
        // for each number of steps of the first argument (i.e. table row)
        for (let i = 0; i < spec.args[0].nsteps; i++) {
            // if there are no value associated in the array of entries to the current index
            if (tableValues.entry[i] === undefined)
                break;
            // if there is only one argument
            if (spec.args.length === 1) {
                // select the superCell corresponding and fill it with the value
                let sc = this.zones[0].fromPath([0,0]).zones[i];

                sc.fromJSON(tableValues.entry[i], spec, false);

                // check if there is metadata attached to the cell
                if (this.tableContainer.isCellComment === false){
                    if (tableValues.entry[i].hasOwnProperty("comment")){
                        if (tableValues.entry[i].comment !== ""){
                            this.tableContainer.isCellComment = true;
                        }
                    }
                }
                if (this.tableContainer.isCellCritApp === false){
                    if (tableValues.entry[i].hasOwnProperty("critical_apparatus")){
                        if (tableValues.entry[i].critical_apparatus.filter(Boolean).length !== 0){
                            this.tableContainer.isCellCritApp = true;
                        }
                    }
                }
            } // if it is a double entry table
            else if (spec.args.length === 2) {
                // for each number of steps of the second argument (i.e. table columns)
                for (let j = 0; j < spec.args[1].nsteps; j++) {
                    if (tableValues.entry[i][j] === undefined)
                        break;
                    // fill the corresponding superCell with the current value
                    let sc = this.zones[0].fromPath([0,0]).zones[i].zones[j];
                    sc.fromJSON(tableValues.entry[i][j], spec, false);

                    // check if there is metadata attached to the cell
                    if (this.tableContainer.isCellComment === false){
                        if (tableValues.entry[i][j].hasOwnProperty("comment")){
                            if (tableValues.entry[i][j].comment !== ""){
                                this.tableContainer.isCellComment = true;
                            }
                        }
                    }
                    if (this.tableContainer.isCellCritApp === false){
                        if (typeof tableValues.entry[i][j].critical_apparatus !== "undefined"){
                            if (tableValues.entry[i][j].critical_apparatus.filter(Boolean).length !== 0){
                                this.tableContainer.isCellCritApp = true;
                            }
                        }
                    }
                }
            }
        }

        // if it is a critical edition
        if (typeof tableValues.edition_tables !== "undefined") {
            // add the metadata related to the table editions to the metaZone "edition_tables" property
            this.zones[0].edition_tables = tableValues.edition_tables;
        }
    }

    generateSiglum(editionTables){
        let siglumDesc = "<span style='font-variant: small-caps;'>Edition sigla:</span><br>";
        for (let i = Object.values(editionTables.tables).length - 1; i >= 0; i--) {
            const info = Object.values(editionTables.tables)[i];
            const editionlink = `<a href="${Routing.generate("tamas_astro_viewTableEdition", {'id': info.editedTextId})}">${truncateWithHtml(info.editedTextTitle, 20)}</a>`;
            siglumDesc += `<span class="siglum">▶&nbsp;<b>${info.siglum ? info.siglum+":" : ""}</b> ${editionlink}<br></span>`;
        }
        return `<div id="sigla">${siglumDesc}</div>`;
    }

    /**
     * This method allows to create a table for one table content associated with a table edition
     * @param tableContent
     */
    createPublicTable(tableContent){
        try {
            // create the table grid template
            // if the table is a 2-argument table, we must compute the width of the
            // super-columns (max between the second argument and the entry)
            if (tableContent.template.args.length === 2)
                tableContent.template.cwidth = Math.max(tableContent.template.args[1].ncells, tableContent.template.entries[0].ncells);

            // actually instantiate the table

            this.template = tableContent.template;

            this.csv = tableContent.csv;

            this.metadata = {
                "edited_text": tableContent.edited_text,
                "table_type": tableContent.table_type,
                "mathematical_parameter_set": tableContent.mathematical_parameter_set,
                "astronomical_parameter_sets": tableContent.astronomical_parameter_sets,
                "table_content": tableContent.table_content,
            };

            // check if there is an "edition_tables" property, meaning that the critical apparatus was automatically generated
            if (typeof tableContent.original.edition_tables !== "undefined"){
                this.metadata.siglum = this.generateSiglum(tableContent.original.edition_tables);
                this.isCate = true;
            }

            this.tableContainer.edition = tableContent.edited_text;

            // create the main zone from a standard layout
            let selectedMetaZone;
            if (tableContent.template.args.length === 1)
                selectedMetaZone = new Table1ArgZone(tableContent.template, {hot: this});
            else
                selectedMetaZone = new Table2ArgZone(tableContent.template, {hot: this});

            this.createFromZone(selectedMetaZone);
            // fill the zones property with table values
            this.fillZones(tableContent.original);
        } catch (err) {
            errorMessage(err, "Could not create a table from the table content\n(index ${this.tableNumber}) given as argument !");
        }
    }

    /**
     * This method create and fills an HTML table with the values stored in
     * the zones property
     */
    createHtmlTable(){
        try {
            // create the HTML table inside the HTML layout that was created
            this.generateHtmlTable();
        } catch (err) {
            errorMessage(err, "Could not fill the HTML table");
        }
    }
}
