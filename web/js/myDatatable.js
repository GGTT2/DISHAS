/* jshint esversion: 6 */

/**
 * function fillTable
 *
 * This function generates the data that will be read by dataTable to generate a dynamic table
 * @param objects : array list of object to be displayed in the table (list of field and data per say)
 * @param spec : object specification of the table (name if a column is only displayed in a specific context, name of the table...)
 * @param returnData
 * @return {{c: [], o: *}} object or doesnt't return anything but call generateTable()
 */
function fillTable(objects, spec, returnData = false){
	var listField = [];
	for (let i = 0; i < objects.fieldList.length; i++){
		let thatField = objects.fieldList[i];
		if (isDefined(thatField.properties.class)){
			if (thatField.properties.class.includes('list')){
				listField.push(thatField.name);
			}
		}
	}

	for (let i = 0; i < objects.data.length; i++){
		let thatData = objects.data[i];

		// If the download property is defined, add a download button
		if (thatData.hasOwnProperty("download")){
			const url = Routing.generate('tamas_astro_export_JSON', {"id": thatData.download});
			thatData.download = `<a class="btn btn-sm btn-primary" onclick="downloadJSON('table_content-${thatData.download}', '${url}')">
								 	 <span class="glyphicon glyphicon-save"></span> DIPS
							 	 </a>`;
		}

		for (let j = 0; j < listField.length; j++){
			let thatField = listField[j];
			thatData[thatField] = minimize(thatData[thatField]);
		}
		if (typeof thatData.buttons !== "undefined")
		thatData.buttons = generateButton(thatData.buttons.edit, thatData.buttons.delete, thatData);
	}

	var columns = [];
	for (let i = 0; i < objects.fieldList.length; i++){
		let thatField = objects.fieldList[i];
		if (thatField.ifOnly === null){
			columns.push({data:thatField.name});
		} else {
			var thatSpec = thatField.ifOnly;
			if (spec[thatSpec] === true){
				columns.push({data:thatField.name});
			}
		}
	}
	var tableIdentifier = ".table-browser";
	if (typeof spec.tableId !== "undefined"){
		tableIdentifier = spec.tableId;
	}
	if (! returnData){
		generateTable(objects, columns, tableIdentifier);
	} else {
		return {o: objects, c: columns};
	}
}

function reloadDataTable(dt){
	dt.search("").columns().search('').draw();
}

/**
 * function generateTable
 *
 * This function contains all the specification of the generic dataTable.
 * @param objects : array of data to be displayed
 * @param columns : array of the columns of the table
 * @param tableIdentifier : identifier of the html table
 * @returns nothing : generate the actual dataTable
 */
function generateTable(objects, columns, tableIdentifier){
	var data = isDefined(objects.data) ? objects.data : objects;
	if (data.length !== 0){
		var table = $(tableIdentifier).DataTable({
				//	preDrawCallback: function (settings){
				//		 $(".dataTables_scrollFoot").appendTo($(".dataTables_scrollHead")) ;
				//	},
			data: data,
			columns: columns,
			deferRender: true,
			scrollY: 400,
			scrollX: true,
			scrollCollapse: true,
					//	scroller: true, //scroller only works if all the row have the same height
			select: {
				style: "os"},
			columnDefs: [
				{targets: 'no-visible',
					visible: false
				}],
			dom: 'lBfrtip', // This code indicates that the button B goes after the "length" element (pick up the # of line per vue)
			buttons: {
				buttons:[{ // the first button is custom, and allow reloading the table after a search
					text:'Reload',
					action: function (e, dt, node, config){
						reloadDataTable(dt)},
					class: 'reload'},
					'colvis'],
			},
			initComplete: function () {
				this.api().columns(':not(.no-sort)').every(function () { //this method allows drop-down fields by column. For more details: https://www.datatables.net/examples/api/multi_filter_select.html
					var column = this;
					var fields = []; // we create an array that will be filled with already created fields. That way, we prevent duplication of the fields.
					var select = $('<select><option value=""></option></select>')
						.appendTo($(column.footer()).empty())
						.on('change', function () {
							var val = $.fn.dataTable.util.escapeRegex($(this).val());
							column
								.search(val ? String(jQuery.fn.DataTable.ext.type.search.string(val)) : '', true, false) //in the quote mark ''+val+'', it is possible to add regex; if we want only trimmed result: '^\\s*'+val+'\\s*$'; Because of multi-entry fields, we need to search for every fields that contians the string.
								.draw();
						});
					column.data().unique().sort().each(function (d, j) {// treatment on the cell.
						if (String(d).indexOf("<ul>") >= 0) { // if a cell contains a multiple content value, we need to split the values.
							var values = d.split("<li>");
							for (let i = 0; i < values.length; i++) {
								createSelectOption(values[i], select, fields);
							}
						} else {
							createSelectOption(d, select, fields);
						}
					});
				});

			},

		});
		// Generate an additional group of button for the export and situate it at the .export-table div.

		new $.fn.dataTable.Buttons( table, {
			buttons: ['copy','csv','excel','pdf']
		} );
		var exportTable = $(tableIdentifier).closest(".dataList").find(".export-table")[0];
		table.buttons( 1, null ).container().appendTo(exportTable);

		readAccent(table);
		return table;
	}
}

/**
 * This function creates a dropdown of options in the select field
 * for each searchable column in the footer of the dataTable
 *
 * @param {string} htmlText
 * @param {object} select (html object)
 * @param {array} fields
 *
 */
function createSelectOption(htmlText, select, fields) {
	if (typeof htmlText === "string" && htmlText.includes("<details>")){ // if the text contains a summary
		var options = [];
		options.push(trimHtmlTags(htmlText.split('<details>')[0])); // take the value before details (the first value is outside the tag)
		var otherOptions = htmlText.split('</summary>')[1];
		otherOptions = otherOptions.split("<br/>").slice(0,-1); // array of value contained in <details>
		$.each(otherOptions, function (index, value) {options.push(trimHtmlTags(value));});

		options = options.filter(unique).sort();
		$.each(options, function (index, value) {
			var val = _.escape(value);
			value = truncateString($.trim(value), 25);
			if (! select.get(0).outerHTML.includes(`value="${val}"`)){
				select.append(`<option value="${val}">${value}</option>`);
			}
			fields.push(val);
		});
	} else {
		var val = $.trim($('<div/>').html(htmlText).text()); // we remove the html tags off the content.
		var value = truncateString($.trim(val), 15);
		val = _.escape(val);

		// We trim the value and cut it for more visibility.
		if (value !== "" && $.inArray(val, fields) < 0) { // We check that the value in not empty - which happens often because of the splitting of the html lists- and that the field is not already registered.
			select.append('<option value ="' + val + '">' + value + '</option>'); // We create a new field containing the value.
			fields.push(val);
		}
	}
}

/**
 * This function deals with array of value to be displayed in the dataTable in one field.
 * In order to ease the reading of the table, we generate an html list ;
 * we shrink it to 3 values with a read more option in case of long list.
 * @param list : array of data to be displayed
 * @returns String : the data stringified as an html list
 */
function minimize(list){
	var htmlString = "";
	htmlString += (list.length > 3 ? "<details><summary><ul>" : "<ul>");
	for (var i = 0; i < list.length; i++){
		if (i === 3 && list.length > 3){
			htmlString += "</summary></ul><ul>";
		}
		htmlString+= '<li> &nbsp;'+list[i]+'</li>';
	}
	htmlString += (list.length > 3 ? "</details></ul>" : "</ul>");
	return htmlString;
}


/**
 * This function removes accentuated character from search input -- see DataTablesPlugIn/accent-neutralise.js
 *
 * @param table
 * @returns nothing : the search input changes its behaviour
 */
function readAccent(table){
	var thisInput = "div .dataTables_wrapper input";
	$(document).on('keyup', thisInput, function () {
		table
			.search(jQuery.fn.DataTable.ext.type.search.string(this.value))
			.draw();
	});
}

/**
 * Generates from an json encoded array of TAMASListTableTemplate objects
 * an array detailing the structure of how the data is nested inside the result object
 *
 * EXAMPLE :
 * columns = { "data": "id" },
 *			 { "data": "default_title" },
 *			 { "data": "tpq" },
 *			 { "data": "taq" },
 *			 { "data": "library.library_name" },
 *			 { "data": "work.default_title" }
 *
 * @param fieldList
 * @returns {Array}
 */
function generateDataStructure(fieldList){
	var columns = [];
	for (let i = 0; i < fieldList.length; i++) {
		if (fieldList[i].displayInTable === true){
			columns.push({data : fieldList[i].name});
		}
	}
	return columns;
}

/**
 * Generates from a json encoded array of TAMASListTableTemplate objects
 * an array of column labels that can be used in a table
 *
 * @param fieldList
 * @returns {Array}
 */
function generateColumnLabels(fieldList) {
	var columnLabels = [];
	for (let i = 0; i < fieldList.length; i++) {
		if (fieldList[i].displayInTable === true){
			columnLabels.push(fieldList[i].title);
		}
	}
	return columnLabels;
}

/**
 * When given a result object from an elasticsearch query and a list of nodes in the object
 * this function returns the values corresponding to the specified nodes in the result object
 *
 * @example :
 * results = {field0: {field1: [{field2: "value0"},{field2: "value1"},{field2: "value2"}]}};
 * fields = ["field0", "field1", "field2"];
 * getValuesFromNodes(results, fields) => ["value0", "value1", "value2"];
 *
 * @param results : object containing the response to the elasticsearch query
 * @param fields : array containing all the field names defining where can be found the value being search
 * @param values : array containing the string values to be displayed in the DataTable
 * @return {Array}
 */
function getValuesFromNodes(results, fields, values = []) {
	if (! isDefined(results)){
		return values;
	}

	// if the desired field is present in the result object
	if (results.hasOwnProperty(fields[0])){
		values = getValuesFromNodes(results[fields[0]], fields.slice(1), values);
	// if the result object object is an array, meaning that it is a list of values
	} else if (isArray(results)) {
		for (let i = 0; i < results.length; i++) {
			values = getValuesFromNodes(results[i], fields, values);
		}
	// if the result object given as parameter is not an object, meaning it contains the value to be displayed
	} else {
		if (! isObject(results) && typeof results !== "undefined"){
			values.push(results);
		}
	}
	return values;
}

/**
 * This function takes an string detailing which node must be selected in the object given as second parameter
 * and returns the right node
 *
 * Example : nodePath : "primary_source.title+primary_source.id"
 * 			 return [object.primary_source.title, object.primary_source.id]
 *
 * @param nodePath string
 * @param object object of result fields in
 * @returns array : value for the current field in an array OR an array of values
 */
function selectNodeOfObject(nodePath, object){
	const arrayOfPaths = nodePath.split("+"); // if there is multiple paths provided, divide them
	const arrayOfNodes = arrayOfPaths[0].split("."); // only the first path is going to contain the text to display

	const text = getValuesFromNodes(object, arrayOfNodes).filter(unique);

	// if multiple fields are needed to display info and the last field is an id
	if (arrayOfPaths.length > 1 && arrayOfPaths[arrayOfPaths.length - 1].endsWith("id")){
		// if the text is supposed to appear as a link, therefore needs an id
		const id = getValuesFromNodes(object, arrayOfPaths[arrayOfPaths.length - 1].split(".")).filter(unique);
		let values = [];
		text.map((txt, i) => {
			if (typeof id[i] !== "undefined"){
				// remove all non-numerical character in the id (to manage kibanaId as well)
				values.push(txt, id[i].replace(/\D/g,''));
			} else {
				/*const id2 = txt ? txt.replace(/\D/g,'') : 0;
				values.push(txt, id2);*/
				console.log(`No identifier retrieved for "${txt}"`);
			}
		});
		// return an array alternating text value and corresponding id
		return values;
	}

	return text;
}

/**
 * to either return the information associated with a field
 * or return a tag indicating missing information
 * The properties will defined how the cell content is formatted :
 * 		- class (will surround the text in a <span class="__"></span>) :
 * 			* list : to indicate that multiple values can be displayed in the same cell
 * 			* number : in order to align the text to the right
 * 			* title-italic : to style the text of the cell in italic
 * 		- path + id :
 * 			* Path = routing path to generate a link
 * 			* Id = location of the id in the result object
 * 		- unknown : text to display if there is no information provided in the results)
 *
 * @param text : array of result values to display (even if there is only 1 value)
 * @param properties : object defined in the template object list (getPublicObjectList)
 * @param result : object containing the results send by Elasticsearch
 * @returns string
 */
function textDisplay (text, properties=[], result={}){
	let classes = "";
	let i;
	if (isDefined(properties.class)){ // putting all classes in the same string
		for (i = 0; i < properties.class.length; i++){
			classes = classes + properties.class[i];
			classes = i < properties.class.length ? classes + " " : "";
		}
	}

	if (isDefined(text)){
		if (isDefined(properties.path)){ // if the cell is supposed to contain a link
			let id;
			if (text.length > 1){
				// if the text contains already the text and the link
				// the "text" array should look like : ["text", "id" (, "text", "id", ...)]
				let texts = [];
				id = [];
				for (let j = 0; j < text.length; j += 2){
					texts.push(text[j]);
					id.push(text[j+1]);
				}
				text = texts; // now it looks like ["text","text", ...]
			} else {
				id = [selectNodeOfObject(properties.id, result)]; // if there is only one id, put it in the variable "id"
			}
			let linkText = [];
			for (i = 0; i < text.length; i++){

				if (typeof id[i] !== "undefined"){
					linkText.push(`<a href="${generateRoute(properties.path, id[i])}">${text[i]}</a>`);
				}
			}
			text = linkText;
		}

		let cellContent = "";
		let cellSummary = "";
		for (i = 0; i < text.length; i++){
			if (classes.includes("ucfirst")){
				text[i] = typeof text[i] === "string" ? text[i].capitalize() : text[i];
			}

			if (i === 0){
				if (classes.includes("truncate")){
					text[i] = trimHtmlTags(text[i]).truncate(120);
				}
				cellContent = classes !== "" ? `<span class="${classes}">${text[i]}</span>` : `${text[i]}`;
			} else {
				cellSummary = cellSummary + `<span class="${classes}">${text[i]}</span>`;
				cellSummary = i < text.length ? cellSummary + "<br/>" : "";
			}
		}
		if (text.length > 1){ // if there is multiple data to display, put it in a summary
			const summary = `<summary class="mainContent">
							   ${text.length-1} <span style="font-variant: small-caps">more record${text.length > 2 ? "s" : ""} </span>
							   <span style="font-size: 10px">▼</span>
						   </summary>`;
			cellContent = `${cellContent}<details>${summary}${cellSummary}</details>`;
		}
		return cellContent;
	} else {
		return noInfo(properties.unknown);
	}
}

const noInfo = (unknownText) => {
	if (typeof unknownText === "undefined"){
		unknownText = "No information provided";
	}
	return `<span class='noInfo'>${unknownText}</span>`;
};

/**
 * This function generates a table header filled with columns labels,
 * as well as a table body and footer
 *
 * @param tagId : string of the id of the html element in which the table is needed
 * @param fieldList : array of properties associated with each field of a datatable
 */
function generateColumnHeader(tagId, fieldList) {
	const table = $(`#${tagId}`);
	table.empty();
	table.html(`<thead class="thead-inverse">
					<tr id=head${tagId}></tr>
				 </thead>
				 <tbody></tbody>
				 <tfoot>
					 <tr id=foot${tagId}></tr>
				 </tfoot>`);
	for (var field of fieldList){
		if (field.displayInTable === true){
			$(`#head${tagId}`).append(`<th scope="col" class="${field.name}">${field.title}</th>`);
			$(`#foot${tagId}`).append(`<th scope="col"></th>`);
		}
	}
}

/**
 * Generate the template of a Datatable according to an entity (index) in order to put query results in it
 * @param index string : label of an entity (ex : "primary_source")
 * @param queryTerm string : terms used to filter the results
 * @param queryTitle string : title detailing the query in natural language (EX => "All original items kept in the British Library")
 * @param generateHeader bool : defines if the header is generated or not
 */
function generateTableLayout(index, queryTerm="", queryTitle="", generateHeader = true){
	const fieldList = fieldLists[index];
	const sources = generateSources(fieldList);
	const resultTable = $("#results");

	// generates columns labels
	resultTable.empty();
	resultTable.html(`<thead class="thead-inverse"><tr id="field-names"></tr></thead>
					  <tbody></tbody>
					  <tfoot><tr id="field-foot"></tr></tfoot>`);
	var i = 0;
	for (var label of generateColumnLabels(fieldList)){
		$("#field-names").append(`<th scope="col" id="${sources[i]}">${label}</th>`);
		$("#field-foot").append(`<th scope="col"></th>`);
		i++;
	}

	if (generateHeader){
		// generates a header for the current query
		generateSearchHeader(index, queryTitle, queryTerm);
	}
}

/**
 * Fill table with elasticsearch results
 * @param url string containing the request to elasticsearch
 * @param index string : label of an entity (ex : "primary_source")
 * @return string url : url of the query
 */
function fillResultTable(url, index="") {
	// Remove the entity definitions from the page
	if (document.getElementById('entity-def')){
		document.getElementById('entity-def').remove();
	}

	const fieldList = fieldLists[index];
	const dataStructure = generateDataStructure(fieldList);
	const sourceFields = generateSources(fieldList, true);

	/**
	 * Function formatting the response of an elasticsearch ajax call into an array of results
	 * @param queryResponse
	 * @return {[]}
	 */
	const formatData = (queryResponse) => {
		const results = retrieveResults(queryResponse);
		let formattedResults = [];

		for (let i = 0; i < results.length; i++) {
			const result = results[i];
			let formattedResult = {};
			for (let i = 0; i < fieldList.length; i++) {
				const field = fieldList[i];
				// field.name is the key where DataTable is going to search for the value to fill the cells of the same column
				if (field.name === "prim_type"){
					formattedResult[field.name] = result[field.name] === "ep" ? "Early printed" : "Manuscript";
				} else {
					// textDisplay() returns the value that is going to be displayed in the cell :
					// 		- the text correctly formatted (according to the field.properties)
					// 		- OR "no information provided" (if there is no value in the result array)
					// selectNodeOfObject() returns the value of the node in the array of result
					// corresponding to string given in sourceFields[i]
					formattedResult[field.name] = textDisplay(selectNodeOfObject(sourceFields[i], result), field.properties, result);
				}
			}
			formattedResults.push(formattedResult);
		}
		return formattedResults;
	};

	let table = $("#results").DataTable({
		"ajax": {
			"url": url, // url on which the query is done
			cache: true, // mandatory in order to send the query without wildly added parameters
			"dataSrc": formatData // variable containing the function formatting the results in order to fill the DataTable
		},
		"columns": dataStructure, // specify where DataTable will find information associated with each fields
		"deferRender": true, // defer the rendering for additional speed of initialisation
		"deferLoading": results.length, // defer the loading + specify the number of result in the table (to allow pagination to be displayed correctly)
		dom: 'lBfrtip', // This code allows to display export buttons on the upper left corner of the table
		initComplete: function () {
			this.api().columns(':not(.no-sort)').every(function () { // this method allows drop-down fields by column. For more details: https://www.datatables.net/examples/api/multi_filter_select.html
				let column = this;
				let fields = []; // we create an array that will be filled with already created fields. That way, we prevent duplication of the fields.
				let select = $('<select><option value=""></option></select>')
					.appendTo($(column.footer()).empty())
					.on('change', function () {
						const val = $.fn.dataTable.util.escapeRegex($(this).val());
						column
							.search(val ? String(jQuery.fn.DataTable.ext.type.search.string(val)) : '', true, false) // in the quote mark ''+val+'', it is possible to add regex; if we want only trimmed result: '^\\s*'+val+'\\s*$'; Because of multi-entry fields, we need to search for every fields that contians the string.
							.draw();
					});
				column.data().unique().sort().each(function (d, j) {// treatment on the cell.
					if (String(d).indexOf("<ul>") >= 0) { // if a cell contains a multiple content value, we need to split the values.
						const values = d.split("<li>");
						for (let i = 0; i < values.length; i++) {
							createSelectOption(values[i], select, fields);
						}
					} else {
						createSelectOption(d, select, fields);
					}
				});
			});
		}
	});
	readAccent(table);
	return url;
}