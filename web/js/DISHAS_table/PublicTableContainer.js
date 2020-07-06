/*jshint esversion: 6 */

/* global $ */

/*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////*/

/*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////*/
class PublicTableContainer {
    /**
     * Class defining HTML tables for the front office interface : parent of the PublicTable objects associated with a single edition
     *
     * The argument "tableContents" is an array of tableContent structured like so
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
     * @return {PublicTableContainer}
     */
    constructor() {
        /**
         * This array contains all the PublicTables
         * @type {Array}
         */
        this.tables = [];

        /**
         * Edited Text title
         * @type {string}
         */
        this.edition = "";

        /**
         * If mean motion : there is multiple tables associated with one edition
         * @type {boolean}
         */
        this.isMultiTable = false;

        /**
         * DOM id of the div that will contain the table
         * @type {String}
         */
        this.containerId = 'content-tabl';

        /**
         * Container
         * @type {HTMLElement} HTML container of the table
         */
        this.container = document.getElementById('content-tabl');

        /**
         * Is there comment attached to any of the supercell of the table
         * (used to determines if a sidebar for metadata must be displayed)
         * @type {boolean}
         */
        this.isCellComment = false;

        /**
         * Is there critical apparatus attached to any of the cell of the table
         * (used to determines if a sidebar for metadata must be displayed)
         * @type {boolean}
         */
        this.isCellCritApp = false;

        /**
         * Object containing information about the selection
         * @type {Object}
         */
        this.selection = {
            selectedTable: null, // index of the table that is currently selected (corresponds to PublicTable.tableNumber)
            selectedCell: null, // html element that is selected (element that have the "selected-cell" class)
            selectedCellCoordinates: [0,0,0], // The selection coordinates corresponds to [tableNumber, rowNumber, columnNumber]
            previousSelectedCell: null, // html element that was previously clicked on
            selectedLineHeader: null, // html element of the currently selected line header (element that have the "selected-line-header" class)
            selectedRowHeader: null, // html element of the currently highlighted row header (element that have the "selected-header" class)
            selectedColHeader: null, // html element(s) of the currently highlighted column header(s) (element(s) that have the "selected-header" class)
            selectedCritApp: null, // html element containing the critical apparatus that is currently highlighted (element that have the "selected-crit-app" class)
            selectedComment: null, // html element containing the comment that is currently highlighted (element that have the "selected-crit-app" class)
            selectedLine: null, // class name of the elements of the line that is selected => ex : "row-7", "col-2" (elements that have the "selected-line" class)
            selectedSuperCell: null // array of html elements of the cells (composing the superCell) that are currently selected (have the "selected-supercell" class)
        };

        /**
         * Returns the html element matching the cell that is currently selected
         * @return {HTMLElement}
         */
        this.getSelectedCell = () => {
            return this.selection.selectedCell;
        };

        /**
         * Return true if a cell is selected
         * @return {boolean}
         */
        this.isSelectedCell = () => {
            return this.selection.selectedCell !== null;
        };

        /**
         * Return true if a line is selected
         * @return {boolean}
         */
        this.isSelectedLine = () => {
            return this.selection.selectedLine !== null;
        };

        /**
         * This function is used as a callback when the user click on a cell
         * @param event
         */
        this.onClick = (event) => {
            let eventTarget = event.target;

            if (eventTarget.tagName === "TD"){
                if (eventTarget.classList.contains("table-header")){
                    this.selectLine(eventTarget);
                } else if (eventTarget.classList.contains("table-cell")){
                    this.selectCell(eventTarget);
                }

            } else if (eventTarget.classList.contains("metadata")) {
                if (eventTarget.classList.contains("comment")){ // if a comment was clicked on
                    this.selectSuperCell(eventTarget);
                } else { // if a critical apparatus was clicked on
                    this.selectCritApp(eventTarget);
                }

            } else if (eventTarget.classList.contains("detach-button")) {
                this.detachTable();

            } else if (eventTarget.classList.contains("print-button")) {
                this.printTable();

            } else if (eventTarget.classList.contains("dti-button")) {
                this.openDTI();

            } else if (eventTarget.classList.contains("csv-button")) {
                this.downloadCSV();

            } else {
                this.deselectAll();
            }
        };

        /**
         * Deselect all that is currently selected
         */
        this.deselectAll = () => {
            this.deselectSuperCell();
            this.deselectLine();
            this.deselectCell();
        };

        /**
         * This method allows to show all critical apparatus and comment associated with the table currently selected
         * This method is called once when there is only one table to display,
         * or each time a cell/line is clicked on in case of a mean motion
         *
         * @param tableNumber
         */
        this.selectTable = (tableNumber) => {
            if (parseInt(this.selection.selectedTable) === parseInt(tableNumber)){
                return;
            }
            if (this.selection.selectedTable !== null){
                // remove critical apparatus from previously selected table
                this.deselectTable(this.selection.selectedTable);
            }

            this.selection.selectedTable = tableNumber;
            let critApp = document.getElementsByClassName(`side-table-${tableNumber}`);
            for (let i = critApp.length - 1; i >= 0; i--) {
                critApp[i].classList.remove("unfocus");
            }

            if (document.getElementById("cate")) {
                document.getElementById("cate").remove();
                if (document.getElementById("sigla")) {
                    document.getElementById("sigla").remove();
                }
            }
            // in order to show if the critical apparatus was generated automatically with cate
            if (this.tables[tableNumber].isCate){
                $("#crit-app-title").append(`<span id='cate'>Generated with <a href="http://uc.hamsi.org.nz/cate/" title="CATE (Computer Assisted Tables Editor) tool is based on \n HAMSI's project CATE standalone edition tool">CATE</a></span>
                                             ${this.tables[tableNumber].metadata.siglum}`);

            } else if (! $.isEmptyObject(this.tables[tableNumber].critAppCells)){
                $("#crit-app-title").append(`<span id='cate'>More info on editorial rules in the comments</span>`);
            }
        };

        /**
         * This method make all comments and critical apparatus associated with the previously selected table disappear
         * @param tableNumber
         */
        this.deselectTable = (tableNumber) => {
            let critApp = document.getElementsByClassName(`side-table-${tableNumber}`);
            for (let i = critApp.length - 1; i >= 0; i--) {
                critApp[i].classList.add("unfocus");
            }
        };

        /**
         * Select the cell corresponding to the coordinates
         * @param domElement : HTMLElement (<td>) corresponding to the cell to select
         */
        this.selectCell = (domElement) => {
            // if the clicked cell is already selected, deselect it
            if (this.selection.selectedCell){
                if (domElement.id === this.selection.selectedCell.id){
                    this.deselectAll();
                    return;
                }
            }

            this.deselectAll();

            this.selection.selectedCell = domElement;
            // add an outline to the clicked cell
            this.selection.selectedCell.classList.add("selected-cell");
            // make the cell stay in the middle
            this.scrollToElement(this.selection.selectedCell);

            // id => "tableNumber-rowNumber-colNumber"
            // coordinates => ["tableNumber", "rowNumber", "colNumber"]
            let coordinates = domElement.id.split("-");

            // add highlight on table headers corresponding to the cell
            this.selection.selectedRowHeader = document.getElementById(`${coordinates[0]}-row-header-${coordinates[1]}`);
            this.selection.selectedColHeader = document.getElementById(`${coordinates[0]}-col-header-${coordinates[2]}`);
            this.selection.selectedRowHeader.classList.add("selected-header");
            this.selection.selectedColHeader.classList.add("selected-header");

            // set the coordinates of the cell
            this.selection.selectedCellCoordinates = [parseInt(coordinates[0]), parseInt(coordinates[1]), parseInt(coordinates[2])];
            // if there is multiple tables, check if the selected cell isn't in an other table as the one already selected
            if (this.isMultiTable){this.selectTable(parseInt(coordinates[0]));}

            // display the metadata associated with the cell that was clicked
            this.showCellMetadata();
        };

        /**
         * Deselect the currently selected cell
         */
        this.deselectCell = () => {
            // remove table headers highlights
            this.deselectTableHeader();

            if (this.isSelectedCell()){
                // remove critical apparatus and comment highlight
                if (this.selection.selectedCritApp !== null){
                    this.selection.selectedCritApp.classList.remove("selected-crit-app");
                    this.selection.selectedCritApp = null;
                }
                if (this.selection.selectedComment !== null){
                    this.selection.selectedComment.classList.remove("selected-crit-app");
                    this.selection.selectedComment = null;
                }

                // remove the selected-cell class to the corresponding DOM element
                this.getSelectedCell().classList.remove("selected-cell");
                // set the isSelection property to be false
                this.selection.selectedCell = null;
                // empty the property associated with the previously selected cell
                this.selection.previousSelectedCell = null;
                // set the coordinates to 0
                this.selection.selectedCellCoordinates = [0,0,0];
            }
        };

        /**
         * Add the "selected-line" class to all the cells that have the class corresponding
         * to the column header that was clicked on.
         * The table row element (<tr>) has an id "row-id", whereas inner cell have a class "row-id" and "col-id"
         * critical apparatus and comment have as well classes that indicates to which column and row it belongs
         *
         * @param eventTarget {Object} : event target (a table header)
         */
        this.selectLine = (eventTarget) => {
            let previousSelectedLine = this.selection.selectedLine;
            this.deselectAll();

            // the id of the clicked header : "(tableNb)-(col/row)-header-(colNb/rowNb)"
            let tableNb = eventTarget.id.match(/\d+/g)[0];
            let lineType = eventTarget.id.startsWith(`${tableNb}-col`) ? "col" : "row";
            let lineNb = eventTarget.id.match(/\d+/g)[1];

            if (this.isMultiTable){this.selectTable(tableNb);}

            // the cells of the corresponding line all have a class : "(tableNb)-col-(colNb)" or "(tableNb)-row-(rowNb)"
            let selectedLine = `${tableNb}-${lineType}-${lineNb}`;

            // if the user hasn't clicked on the same column header as the one that was previously clicked on
            if (selectedLine !== previousSelectedLine){
                // add highlight the header of the selected line
                eventTarget.classList.add("selected-line-header");
                // assign the html element to the correct property
                this.selection.selectedLineHeader = eventTarget;

                this.selection.selectedLine = selectedLine;
                let lineCells = document.getElementsByClassName(selectedLine);
                for (let i = lineCells.length - 1; i >= 0; i--) {
                    lineCells[i].classList.add("selected-line");

                    // if the element is in the sidebar : display it on the screen
                    if (lineCells[i].tagName === "H4"){
                        this.scrollToElement(lineCells[i]);
                    }
                }
            }
        };

        /**
         * Deselected the currently highlighted cells
         */
        this.deselectLine = () => {
            if (this.isSelectedLine()){
                let lineCells = document.getElementsByClassName(this.selection.selectedLine);
                for (let i = lineCells.length - 1; i >= 0; i--) {
                    lineCells[i].classList.remove("selected-line");
                }
                this.selection.selectedLine = null;
            }
        };

        /**
         * This method add the "selected-supercell" class to the table cell that contain the comment
         * associated with the metadata that was clicked on
         *
         * @param eventTarget {object} : html element that was clicked
         *                               this element is a metadata header attached to a comment
         *                               its class list contains all col and row numbers that are associated with the
         *                               super cell that contains the comment
         */
        this.selectSuperCell = (eventTarget) => {
            if (eventTarget.tagName === "SPAN"){ // if the user has clicked on the span and not on the h4
                eventTarget = eventTarget.parentElement;
            }

            if (this.selection.selectedComment){
                // if the comment that was clicked on corresponds to the previously selected comment
                if (this.selection.selectedComment.id === eventTarget.id){
                    this.deselectAll();
                    return;
                }
            }
            this.deselectAll();

            let classList = Object.values(eventTarget.classList);

            // get the row number of the superCell that contains the comment
            let rowNumber = classList.find((e) => e.startsWith(`${this.selection.selectedTable}-row`)).match(/\d+/g)[1];
            // get the array of col numbers of the superCell that contains the comment
            let colNumbers = classList.filter(e => e.startsWith(`${this.selection.selectedTable}-col`)).map(e => e.match(/\d+/g)[1]);


            // highlight the row header
            let row = document.getElementById(`${this.selection.selectedTable}-row-header-${rowNumber}`);
            this.selection.selectedRowHeader = row;
            row.classList.add("selected-header");

            let superCell = [];
            let cols = [];

            for (let i = colNumbers.length - 1; i >= 0; i--) {
                let cell = document.getElementById(`${this.selection.selectedTable}-${rowNumber}-${colNumbers[i]}`);
                cell.classList.add("selected-supercell");
                superCell.push(cell);

                // highlight columns headers
                let col = document.getElementById(`${this.selection.selectedTable}-col-header-${colNumbers[i]}`);
                cols.push(col);
                col.classList.add("selected-header");

                // if the element that is being treated is located in the sidebar (meaning that is critical apparatus)
                if (i === 0){this.scrollToElement(cell);}
            }
            this.selection.selectedSuperCell = superCell;
            this.selection.selectedColHeader = cols;
            this.selection.selectedComment = eventTarget;
            this.selection.selectedComment.classList.add("selected-crit-app");
        };

        /**
         * Remove the "selected-supercell" class from all html element
         * that currently have it
         */
        this.deselectSuperCell = () => {
            this.deselectTableHeader();

            if (this.selection.selectedComment !== null){
                this.selection.selectedComment.classList.remove("selected-crit-app");
            }

            if (this.selection.selectedSuperCell !== null) {
                for (let i = this.selection.selectedSuperCell.length - 1; i >= 0; i--) {
                    this.selection.selectedSuperCell[i].classList.remove("selected-supercell");
                }
            }
            this.selection.selectedSuperCell = null;
            this.selection.selectedComment = null;
        };

        /**
         * Remove the "selected-header" class to all table headers that are currently selected
         */
        this.deselectTableHeader = () => {
            if (this.selection.selectedLineHeader !== null){
                this.selection.selectedLineHeader.classList.remove("selected-line-header");
                this.selection.selectedLineHeader = null;
            }

            if (this.selection.selectedRowHeader !== null){
                this.selection.selectedRowHeader.classList.remove("selected-header");
                this.selection.selectedRowHeader = null;
            }
            if (this.selection.selectedColHeader !== null){
                if (isArray(this.selection.selectedColHeader)){
                    // if a supercell is selected therefore multiple column headers are highlighted
                    for (let i = this.selection.selectedColHeader.length - 1; i >= 0; i--) {
                        this.selection.selectedColHeader[i].classList.remove("selected-header");
                    }
                } else {
                    this.selection.selectedColHeader.classList.remove("selected-header");
                }
                this.selection.selectedColHeader = null;
            }
        };

        /**
         * This method allows to select the corresponding cell when clicking on a critical apparatus
         * @param eventTarget : HTMLElement (critical apparatus) that was clicked on
         */
        this.selectCritApp = (eventTarget) => {
            if (eventTarget.tagName === "SPAN"){ // if the user has clicked on the span and not on the h3
                eventTarget = eventTarget.parentElement;
            }

            let table = eventTarget.id.match(/\d+/g)[0];
            let classList = Object.values(eventTarget.classList);
            let row = classList.find(e => e.startsWith(`${table}-row`)).match(/\d+/g)[1];
            let col = classList.find(e => e.startsWith(`${table}-col`)).match(/\d+/g)[1];

            this.selectCell(document.getElementById(`${table}-${row}-${col}`));
        };

        /**
         * Select the appropriate tab to display in the console box,
         * base on the selected cells and their properties
         * @return {undefined}
         */
        this.showCellMetadata = () => {
            // take the currently selected cell coordinates
            let table; let row; let col;
            [table, row, col] = this.selection.selectedCellCoordinates;

            if (this.tables[table].critAppCells.hasOwnProperty(`${table}-${row}-${col}`)){
                let critApp = document.getElementById(`${table}-title-${row}-${col}`);

                // set the HTML element as the currently highlighted critical apparatus
                this.selection.selectedCritApp = critApp;
                critApp.classList.add("selected-crit-app");
                this.scrollToElement(critApp);
            }

            if (this.tables[table].commentCells.hasOwnProperty(`${table}-${row}-${col}`)){
                let comment = document.getElementById(`${this.tables[table].commentCells[`${table}-${row}-${col}`]}`);

                // set the HTML element as the currently highlighted comment
                this.selection.selectedComment = comment;
                comment.classList.add("selected-crit-app");

                // scroll to display the comment only if there is no critical apparatus attached to the cell
                if (! this.tables[table].critAppCells.hasOwnProperty(`${table}-${row}-${col}`)){
                    this.scrollToElement(comment);
                }
            }
        };

        /**
         * This object is used to associated a key event when pressing on an arrow
         * and an operation to perform on the coordinates
         * Ex: when the user press arrow down, add one to the row number
         * @type {{}}
         */
        this.positionOperation = {
            "ArrowUp": () => {
                return [this.selection.selectedCellCoordinates[1] - 1, this.selection.selectedCellCoordinates[2]];
            },
            "ArrowDown": () => {
                return [this.selection.selectedCellCoordinates[1] + 1, this.selection.selectedCellCoordinates[2]];
            },
            "ArrowLeft": () => {
                return [this.selection.selectedCellCoordinates[1], this.selection.selectedCellCoordinates[2] - 1];
            },
            "ArrowRight": () => {
                return [this.selection.selectedCellCoordinates[1], this.selection.selectedCellCoordinates[2] + 1];
            }
        };

        /**
         * Callback function to be called on key down (when the key is released)
         * @param  {Object} keyEvent  key event object
         * @return {undefined}
         */
        this.keyDown = (keyEvent) => {
            if (this.isSelectedCell()) {
                keyEvent.preventDefault(); // NOTE : it slows down the navigation with arrow keys

                if (keyEvent.key.startsWith("Arrow")){
                    // if arrowUp were simultaneously pressed with the shift key
                    if (keyEvent.shiftKey) {
                        return;
                    }
                    let row; let column;
                    [row, column] = this.positionOperation[keyEvent.key]();
                    // if the new coordinates do not exceeds the table boundaries
                    if ((row >= 0 && row < this.tables[this.selection.selectedTable].rowNumber)
                        && (column >= 0 && column < this.tables[this.selection.selectedTable].columnNumber)){
                        this.selectCell(document.getElementById(`${this.selection.selectedTable}-${row}-${column}`));
                    }
                }
            }
        };
    }

    /**
     * This method generates a pop up window with a duplicate of the table in it
     * @return {Window}
     */
    detachTable(){
        this.deselectAll();
        let table = document.getElementById("tables");
        // table.removeChild(document.getElementById("detach")); NOTE : when clicking twice on the button the table is appended to the pop window
        return createPopUp(table.innerHTML, document.title);
    }

    /**
     * This method prints the table
     */
    printTable(){
        let table = this.detachTable();
        table.print();
        table.close();
    }

    /**
     * This method fills the modal with information associated with the currently selected table
     */
    openDTI(){
        let tableInfo = this.tables[this.selection.selectedTable].metadata.table_content;
        $("#modal-dti-title").html(`Open table content « ${tableInfo.title} » in DISHAS Table Interface?`);

        let url = Routing.generate("tamas_astro_publicTableContent", {tableTypeId: 0, comparedTableContent: `[${tableInfo.id}]`});
        document.getElementById("redirect-dti").setAttribute("href", url);
    }

    /**
     * This method is called when the user click on the download button
     * it generates a csv string and allows the user to download it in a file
     */
    downloadCSV(){
        let tableInfo = this.tables[this.selection.selectedTable].metadata.table_content;
        $("#modal-csv-title").html(`Download table content « ${tableInfo.title} » in CSV?`);

        let csvObject = new Blob([this.tables[this.selection.selectedTable].csv]);
        let csvURL = window.URL.createObjectURL(csvObject);
        let downloadButton = document.getElementById("download-csv");
        downloadButton.setAttribute("href", csvURL);
        downloadButton.setAttribute("download", `${tableInfo.title.replace(/ /g, "_")}.csv`);
    }

    /**
     * This method creates either two panels to display the table and the critical apparatus (in case there is one)
     * or one panel to display only the table
     */
    generateHtmlStructure(){
        const container = $(`#${this.containerId}`);

        if (this.isCellComment && this.isCellCritApp){
            // if there is metadata (critical apparatus or comment) associated with any of the cell, display a sidebar
            container.append(`<div class="row">
                                 <div class="col-md-11" id="table-panel">
                                     <div id="table" class="content-panel">
                                         <div class="row">
                                             <div id="edition-title" class="col-md-9"><h3>${this.edition.title}</h3></div>
                                             <button id="dti" data-toggle="modal" data-target="#modal-dti" type="button" title="Open table in DTI" class="btn dti-button"><span class="glyphicon glyphicon-flash dti-button"></span></button>
                                             <button id="csv" data-toggle="modal" data-target="#modal-csv" type="button" title="Download table in CSV" class="btn csv-button"><span class="glyphicon glyphicon-save csv-button"></span></button>
                                             <button id="detach" type="button" title="Detach table in an other window" class="btn detach-button"><span class="glyphicon glyphicon-new-window detach-button"></span></button>
                                             <button id="print" type="button" title="Print table" class="btn print-button"><span class="glyphicon glyphicon glyphicon-print print-button"></span></button>
                                         </div>
                                         <div id="tables" class="row"></div>
                                     </div>
                                 </div>

                                 <div class="col-md-1" id="crit-app-panel">
                                     <div id="content-side-crit-app" class="contentSidebar">
                                         <div id="side-crit-app" class="content-panel" style="height: 365px; margin-bottom: -25px;">
                                             <div id="crit-app-title" class="sidebar-title">
                                                 <h3>
                                                     <span class="glyphicon glyphicon-bookmark"></span>
                                                     Critical apparatus
                                                 </h3>
                                             </div>
                                         </div>
                                     </div>
                                     <div id="content-side-comment" class="contentSidebar">
                                         <div id="side-comment" class="content-panel" style="height: 300px; margin-top: -25px;">
                                             <div id="comment-title" class="sidebar-title">
                                                 <h3>
                                                     <span class="glyphicon glyphicon-comment"></span>
                                                     Comment
                                                 </h3>
                                             </div>
                                         </div>
                                     </div>
                                 </div>
                             </div>`);
        } else if (this.isCellComment || this.isCellCritApp) {
            let type = this.isCellCritApp ? "crit-app" : "comment";
            container.append(`<div class="row">
                                 <div class="col-md-11" id="table-panel">
                                     <div id="table" class="content-panel">
                                         <div class="row">
                                             <div id="edition-title" class="col-md-9"><h3>${this.edition.title}</h3></div>
                                             <button id="dti" data-toggle="modal" data-target="#modal-dti" type="button" title="Open table in DTI" class="btn dti-button"><span class="glyphicon glyphicon-flash dti-button"></span></button>
                                             <button id="csv" data-toggle="modal" data-target="#modal-csv" type="button" title="Download table in CSV" class="btn csv-button"><span class="glyphicon glyphicon-save csv-button"></span></button>
                                             <button id="detach" type="button" title="Detach table in an other window" class="btn detach-button"><span class="glyphicon glyphicon-new-window detach-button"></span></button>
                                             <button id="print" type="button" title="Print table" class="btn print-button"><span class="glyphicon glyphicon glyphicon-print print-button"></span></button>
                                         </div>

                                         <div id="tables"></div>
                                     </div>
                                 </div>

                                 <div class="col-md-1" id="crit-app-panel">
                                     <div id="content-side-${type}" class="contentSidebar">
                                         <div id="side-${type}" style="height: 700px;" class="content-panel">
                                             <div id="${type}-title" class="sidebar-title">
                                                 <h3>
                                                     <span class="glyphicon glyphicon-${this.isCellCritApp ? "bookmark" : "comment"}"></span>
                                                     ${this.isCellCritApp ? "Critical apparatus" : "Comment"}
                                                 </h3>
                                             </div>
                                         </div>
                                     </div>
                                 </div>
                             </div>`);
        } else { // else display only the table
            container.append(`<div class="row">
                                 <div class="col-md-12" id="table-container">
                                     <div id="table" class="content-panel" style="margin:0;">
                                         <div class="row">
                                             <div id="edition-title" class="col-md-9"><h3>${this.edition.title}</h3></div>
                                             <button id="dti" data-toggle="modal" data-target="#modal-dti" type="button" title="Open table in DTI" class="btn dti-button"><span class="glyphicon glyphicon-flash dti-button"></span></button>
                                             <button id="csv" data-toggle="modal" data-target="#modal-csv" type="button" title="Download table in CSV" class="btn csv-button"><span class="glyphicon glyphicon-save csv-button"></span></button>
                                             <button id="detach" type="button" title="Detach table in an other window" class="btn detach-button"><span class="glyphicon glyphicon-new-window detach-button"></span></button>
                                             <button id="print" type="button" title="Print table" class="btn print-button"><span class="glyphicon glyphicon glyphicon-print print-button"></span></button>
                                         </div>
                                         <div id="tables"></div>
                                     </div>
                                 </div>
                             </div>`);
        }

        const multiTable = this.isMultiTable ? "<div class='modal-body'>The selected table corresponds to the last you clicked on</div>" : "";

        // modal window to open selected table in dti
        container.append(`<div class="modal fade" id="modal-dti" tabindex="-1" role="dialog" aria-labelledby="modal-dti-title" aria-hidden="true">
                             <div class="modal-dialog" role="document">
                                 <div class="modal-content">
                                     <div class="modal-header">
                                         <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                             <span aria-hidden="true">&times;</span>
                                         </button>
                                         <h4 class="modal-title" id="modal-dti-title"></h4>
                                     </div>
                                     ${multiTable}
                                     <div class="modal-footer" style="text-align: center">
                                         <a id="redirect-dti" class="btn btn-primary" href="" target="_blank">Yes</a>
                                         <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                                     </div>
                                 </div>
                             </div>
                         </div>`);
        // modal window to download selected table in csv
        container.append(`<div class="modal fade" id="modal-csv" tabindex="-1" role="dialog" aria-labelledby="modal-csv-title" aria-hidden="true">
                             <div class="modal-dialog" role="document">
                                 <div class="modal-content">
                                     <div class="modal-header">
                                         <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                             <span aria-hidden="true">&times;</span>
                                         </button>
                                         <h4 class="modal-title" id="modal-csv-title"></h4>
                                     </div>
                                     ${multiTable}
                                     <div class="modal-footer" style="text-align: center">
                                         <a id="download-csv" class="btn btn-primary" href="">Yes</a>
                                         <button id="close-csv" type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                                     </div>
                                 </div>
                             </div>
                          </div>`);
    }

    /**
     * This method allows to scroll to the element by scroll only in its parent
     * (without jiggling to whole page) NOTE : not working for now !
     *
     * @param domElement
     */
    scrollToElement(domElement){
        domElement.scrollIntoView({block: "center", behavior: "smooth"});
    }

    /**
     * This method is called to generate all HTML tables for each table contents given as parameter
     * @param tableContents
     */
    createPublicTable(tableContents){
        if (typeof tableContents[0] === "undefined"){
            $(`#${this.containerId}`).append(`<div class="row">
                                          <div class="col-md-12" id="table-container">
                                              <div id="table" class=" content-panel">
                                                  <h3 class="noInfo" style="text-align: center; margin-top: 300px;">
                                                      The table content associated with this edition is not available yet
                                                  </h3>
                                              </div>
                                          </div>
                                       </div>`);
            return;
        }

        if (tableContents[0].template === null){
            $(`#${this.containerId}`).append(`<div class="row">
                                          <div class="col-md-12" id="table-container">
                                              <div id="table" class=" content-panel">
                                                  <h3 class="noInfo" style="text-align: center; margin-top: 300px;">
                                                      The table content associated with this edition is empty
                                                  </h3>
                                              </div>
                                          </div>
                                       </div>`);
            return;
        }

        // set the table specifications
        if (tableContents === null){
            window.alert("Missing specifications");
            return;
        }

        // Check if the table template specification are correct JSON
        try {
            if (typeof tableContents === "string"){
                tableContents = JSON.parse(tableContents);
            }
        } catch (err) {
            console.log(err);
            window.alert(`${err}\nCould not parse "template" field as a JSON object.
				      Please check that "template" field is present, and is a string`);
            return;
        }

        if (tableContents.length > 1){this.isMultiTable = true;}

        for (let i = 0; i < tableContents.length; i++) {
            this.tables.push(new PublicTable(this, i));
            this.tables[i].createPublicTable(tableContents[i]);
        }
        this.generateHtmlStructure();

        let numberOfColumns = [];

        for (let i = 0; i < this.tables.length; i++) {
            this.tables[i].createHtmlTable();
            numberOfColumns.push(this.tables[i].columnNumber+1); // we add one to count the row header column
        }

        // compute the width of the gaps between columns
        let gapWidth = (this.tables.length-1)*20;
        // compute the width of all tables added together
        let tableWidth = numberOfColumns.reduce((a, b) => a + b)*50;

        // Define the layout of columns for all tables
        document.getElementById("tables").style.width = `${gapWidth+tableWidth}px`;
        // we add a first column with 0fr to avoid errors in the layout only when crit app (because css is a little scoundrel <:°] )
        let firstCol = this.isCellComment || this.isCellCritApp ? "0fr " : "";
        document.getElementById("tables").style.gridTemplateColumns = `${firstCol}${numberOfColumns.join("fr ")}fr`;

        // add an event listener on click and on keyDown
        document.documentElement.addEventListener("click", e => this.onClick(e));
        document.addEventListener("keydown", e => this.keyDown(e));

        // make the critical apparatus of the first table appear
        this.selectTable(0);
    }
}
