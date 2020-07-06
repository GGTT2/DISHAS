/* jshint esversion: 6 */

/**
 * Fields used when doing a multi-match query to avoid performing the query on all fields (usage being deprecated)
 * @type {{primary_source: string[], work: string[], original_text: string[]}}
 */
const multiFields = {
    "work": [
        "id",
        "tpq", // keyword because it is a integer field that cannot be queried as a string
        "taq",
        "kibana_name",
        "historical_actor.kibana_name",
        "original_texts.primary_source.kibana_name",
    ],
    "original_text": [
        "id",
        "primary_source.kibana_name",
        "tpq",
        "taq",
        "kibana_name",
        "table_type.kibana_name",
        "historical_actor.kibana_name",
        "work.kibana_name",
    ],
    "primary_source": [
        "id",
        "kibana_name",
        "tpq",
        "taq",
        "prim_type",
        "original_texts.work.kibana_name",
    ],
    "edited_text": [
        "id",
        "kibana_name",
        "date",
        "historian.kibana_name",
        "secondary_source.historian.kibana_name",
        "table_type.kibana_name"
    ],
    "parameter_set": [
        "id",
        "table_type.kibana_name",
        "default_title",
        "parameter_values.default_title",
        "table_contents.kibana_name",
        "table_type.astro_definition",
        "parameter_values.default_title"
    ],
    "formula_definition": [
        "id",
        "name",
        "table_type.kibana_name",
        "author.kibana_name",
        "explanation"
    ]
};

/**
 * This function takes a response from an ajax call to elasticsearch
 * and returns an array containing all the results of this query
 *
 * @param queryResponse : object
 * @return {Array|Object}
 */
function retrieveResults(queryResponse) {
    if (queryResponse.hasOwnProperty("hits") && queryResponse.hits.hasOwnProperty("hits")) {
        const data = queryResponse.hits.hits; // data is an array of results, but only the value of the key "_source" is needed
        let results = [];

        $.each(data, function (key, value) {
            results.push(value._source);
        });

        return results;
    } else {
        console.log('%c Elasticsearch response:', 'font-size: 18px; color: #12ada4; font-weight: bold');
        console.log(queryResponse);
        window.alert("Malformed query.\nPlease contact the digital team to warn about this bug.");
        return queryResponse;
    }
}

/**
 * Generates from a json encoded array of TAMASListTableTemplate objects
 * an array of field names that can be used as sources parameter in a elasticsearch query
 *
 * EXAMPLE
 * [ "id", "original_text_title", "tpq", "taq", "primary_source.shelfmark", "primary_source.library.library_name", "work.default_title" ]
 *
 * @param fieldList : object, template for the lists of a certain entity
 * @param isSourcesConcatPerField : boolean, determines if sources need to be split or not when concatened
 * @returns {Array}
 */
function generateSources(fieldList, isSourcesConcatPerField=false){
    let sources = [];
    for (let thatField of fieldList){
        if (thatField.source !== ""){ // if the field "source" is filled in
            if (thatField.source.includes("+") && isSourcesConcatPerField === false){
                // some value may includes a "+" which means that multiple fields were concatenate
                const arrayOfSources = thatField.source.split("+");
                for (let src of arrayOfSources){
                    sources.push(src); // then, push the fields separately the source array
                }
            } else {
                sources.push(thatField.source);
            }
        } else { // else takes the value of the field "name"
            sources.push(thatField.name);
        }
    }
    return sources;
}

/**
 * Returns an URL that can be queried in AJAX
 *
 * @param query object : the query object where the filters are specified
 * @param index string : the entity name on which the query is performed
 * @param sources array : list of field that are going to appear in the results
 * @param from int : the item number on which the query begins (0 to perform the query beginning from the first record of the database)
 * @param size int : the maximum number of results we want to appear in the response
 * @returns string url
 */
function generateUrl(query="", index="", sources = [], from = 0, size = 10000) {
    return `${elasticServer}?${encodeQueryData({"index": index, "source" : JSON.stringify(sources), "query" : JSON.stringify(query), "from" : from, "size" : size, "origin": "intern"})}`;
}

/*____________________________________________DATATABLES WITH ELASTICSEARCH____________________________________________*/

/**
 * Generates a header corresponding to the query that was made to elasticsearch
 *
 * @param entity : string, EX => "original_text"
 * @param queryTitle : string, EX => "All original items kept in the British Library"
 * @param queryTerms : string, i.e. terms used to filter the results
 * @returns {Array}
 */
function generateSearchHeader(entity, queryTitle="", queryTerms=""){
    const objectName = objects[toCamel(entity)].objectUserInterfaceName.capitalize(); // (name of the entity as displayed in the user interface), EX => "Original item"
    const picto = imgPath.replace("IMG", `pictograms/${toCamel(entity)}.png`); // (path of the entity pictogramm), EX => "img/pictograms/originalText.png"

    queryTitle = queryTitle !== "" ? queryTitle : queryTerms === "" ? "All records" : `All records that match « ${queryTerms} »`;

    $("#result-title").html(`<div class="col-md-1">
                                  <div class="picto-color ${toCamel(entity)}">
                                      <img id="picto-size"
                                           src="${picto}"
                                           alt="${objectName} pictogram">
                                  </div>
                             </div>
                             <div class="col-md-11">
                                  <h2 class="capitalize">${objectName}</h2>
                                  <div class="row" style="margin-top: -1em">
                                      <div class="col-md-10">
                                          <h4>${queryTitle}</h4>
                                      </div>
                                  </div>
                             </div>`);
    $("#copy-query").html(`<a href="" id="copy" class="badge badge-info search-element">Copy query</a>`);

    $("#parameter-search").empty();
    if (entity === "parameter_set"){
        generateParameterDisclaimer();
    }
}

function generateParameterDisclaimer() {
    $("#parameter-search").append(`<div id="parameter-search-disclaimer" class="col-md-offset-1 col-md-10">
                                <span class="main-info">NOTE</span>: For an advanced search among parameter set values, please use the dedicated search interface:
                                <br>
                                <span class="main-info" style="padding-top: 7px;">
                                    <a href="${Routing.generate("tamas_astro_simpleSearchParameterSet")}">Advanced parameter search</a>
                                </span>
                            </div>`);
}

/**
 * This function takes the TAMASListTableTemplate of a field associated with an entity
 * (ex : the specifications for the field "Title" of a Primary source which looks like
 * {
 *      "name":"title",
 *      "title":"Source",
 *      "source":"primary_source.default_title+primary_source.id+primary_source.tpq+primary_source.taq"
 * })
 * and returns the name of the array of nodes (i.e. of keys) in which the info for this particular field
 * are contained in the object containing a result.
 * In this case, the key "primary_source" (i.e. the first node for each source field) contains in the result,
 * the value of the title, the id, the tpq and taq of the primary source.
 *
 * return : ["primary_source", "default_title"]
 */
function getArrayOfNodes(fieldTemplate) {
    const sources = fieldTemplate.source !== "" ? fieldTemplate.source : fieldTemplate.name;
    const arrayOfPaths = sources.split("+");
    return arrayOfPaths[0].split(".");
}

/**
 * This function takes the TAMASListTableTemplate of a field associated with an entity
 * retrieve for it a string detailing which node must be selected in the object given as second parameter
 * and returns the right node
 *
 * Example : fieldTemplate : "primary_source.title+primary_source.id"
 * 			 return object["primary_source"]["title"]
 *
 * @param fieldTemplate : object containing the properties of the field being currently treated
 * @param object object
 * @returns array : value of the node in the object
 */
function retrieveNodeValue(fieldTemplate, object){
    const arrayOfNodes = getArrayOfNodes(fieldTemplate);
    for (let node of arrayOfNodes){
        if (isDefined(object[node])){
            object = object[node];
        } else {
            return null; // if the node doesn't exist in the object
        }
    }
    return object;
}

/**
 * This function generates a DataTable when given an entity name in snake_case and a query term
 * The query is done with a default fuzziness of 1, but turns to exact match if the query term is surrounded in quotes
 * The operator and allows to filter the query with every word in the search terms (and not with any of them)
 * The fields allows the specify to not execute the query on every fields of an entity
 * (which is deprecated by Elasticsearch, creates noise in the results and takes a lot more time)
 *
 * @param entity string EX : "original_text"
 * @param term string EX : "natonal" (which will match national)
 * @return {string} URL of the ajax query that is made to Elasticsearch
 */
function generateMatchTable(entity, term=""){
    // destroy the current table if one is already existing
    if ($.fn.DataTable.isDataTable('#results')){
        $('#results').DataTable().destroy();
    }

    let filter = "";

    if (term){
        // if the search string contains wildcards
        if (term.startsWith(elasticServer)){
            const regex = new RegExp(`${elasticServer}\/([a-z_]*)\/`);
            const index = term.match(regex)[1];
            generateTableLayout(index, "", "Results generated from URL query");
            return fillResultTable(term, index);
        } else {
            filter = generateFilterFromTerm(term, entity);
        }
    }
    generateTableLayout(entity, term);
    return fillResultTable(generateUrl(filter, entity, generateSources(fieldLists[entity])), entity);
}

function generateFilterFromTerm(term, entity){
    term = term.replace(/;/gi, ""); // semi colon causes bugs
    if (term.includes("*")){
        return {
            "query_string": {
                "query": term,
                "fields": multiFields[entity],
                "default_operator": "AND",
                "analyze_wildcard": true
            }
        };
    } else {
        return {
            "multi_match": {
                "query": term,
                "fuzziness": term.startsWith('"') && term.endsWith('"') ? 0 : "auto", // if the search terms are surrounded with quotes, no fuzziness
                "operator": "and",
                "fields": multiFields[entity]
            }
        };
    }
}