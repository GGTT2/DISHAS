/* jshint esversion: 6 */

/**
 * This method defines a dataset that can be used to generate a DishasTreemapChart
 * A DishasTreemapDataset contains 3 different data sets in order to display 3 different treemap
 * Therefore, the data property contains an object with 3 properties, one for each treemap
 * this.chart = [{
 *          type: "Treemap",
 *          data: {
 *              type: [main treemap data on astronomical objects and table types],
 *              param: [sub treemap data on edited and non-edited parameter sets],
 *              item: [sub treemap data on edited original item]
 *          }
 *      }];
 */
class DishasMultiTreemapDataset extends DishasDataset {
    /**
     * @param editionData : object of response of an elasticsearch query performed on the edited_text index
     * @param uneditedData : object additional data on all table types and parameter sets that might
     *                  not be associated with an edition
     */
    constructor(editionData = null, uneditedData = null){
        super();
        this.chart = [{
            type: "MultiTreemap",
            data: editionData ? this.generateDatasetFromResponse(editionData, uneditedData) : null
        }];
    }

    addToAstroObject(data, editionId, name = "Unknown astronomical object", id = "ao0") {
        if (!data.hasOwnProperty(id)) {
            data[id] = {
                name: name.capitalize(),
                id: id,
                color: "grey",
                editionIds: [],
                children: {}
            };
        }
        data[id].editionIds.push(editionId);
        return data;
    }

    addToData(data, editionId, isParent = true, name = "Unnamed record", id = "0") {
        if (!data.hasOwnProperty(id)) {
            data[id] = {};
            data[id].name = name.capitalize();
            data[id].id = id;
            data[id].count = 1;
            data[id].editionIds = [];
            if (isParent) data[id].children = {};
        }
        data[id].count += 1;
        data[id].editionIds.push(editionId);
        return data;
    }

    /** * * * * * * * * * * * * * * * * * * * * * *
     * Elasticsearch result object :
     * [{
     * 		"id" : "edition id",
     * 		"kibana_name" : "Table edition title",
     * 		"table_contents" : [{
     * 			"parameter_sets" : [{default_title" : "4 ; 56", "id" : "7"}, {...}]
     * 		},{...}],
     * 		"table_type" : {
     *  		"id" : "table type id", "table_type_name" : "lunar equation",
     *  		"astronomical_object" : {
     *  			"id" : "astro object id",
     *    			"object_name" : "moon",
     *    		},
     *		},
     *		"related_editions" : [{
     *			"original_texts" : [{"id" : "orig item id", "original_text_title" : "orig item title"}, {...}]
     *			}, {...}],
     *		"original_texts" : [{"id" : "orig item id", "original_text_title" : "orig item title"}, {...}]
     *	}]
     * * * * * * * * * * * * * * * * * * * * * * * *
     * additionalData
     * type: {astroId: {
     *       name : "astro object name",
     *       id : "astro object id",
     *       color : "astro object color",
     *       children : {typeId: {
     *           type : "table type name",
     *           typeId : "table type id",
     *           typeCount : "number of editions associated with this table type",
     *           editionIds : []
     *       }, {...}}
     *   }, astroId: {...}},
     * param: {astroId: {
     *       name : "astro object name",
     *       id : "astro object id",
     *       color : "astro object color",
     *       children : {typeId: {
     *           type : "table type name",
     *           typeId : "table type id",
     *           typeCount : "number of editions associated with this table type",
     *           editionIds : [],
     *           children : {paramId: {
     *               param : "default title param",
     *               paramId : "id",
     *               paramCount : "number of edition using this param (being associated with the table type parent),
     *               editionsIds : []
     *           }}
     *       }, {...}}
     *   }, astroId: {...}}
     * * * * * * * * * * * * * * * * * * * * * * * *
     * Treemap dataset
     * [{
     *     name : "astro object name",
     *     id : "astro object id",
     *     color : "astro object color",
     *     children : [{
     *         type : "table type name",
     *         typeId : "table type id",
     *         typeCount : "number of editions associated with this table type",
     *         editionIds : [ids, of, all, editions],
     *         children : [
     *         # FOR THE ITEM DATASET
     *         {
     *             item : "orig item title",
     *             itemId : "id",
     *             itemCount : "number of editions for this orig item (being associated with the table type parent)",
     *             editionsIds : [ids, of, all, editions]
     *         },
     *         # FOR THE PARAM DATASET
     *         {
     *             param : "default title param",
     *             paramId : "id",
     *             paramCount : "number of edition using this param (being associated with the table type parent),
     *             editionsIds : [ids, of, all, editions]
     *         }]
     *     }, {...}]
     * },{...}]
     *
     * @param response : object of response of an elasticsearch query untreated on the edited text index
     * @param additionalData : object additional data on all table types and parameter sets that might
     *                         not appear in the response object because they are not associated with any edited text
     */
    generateDatasetFromResponse(response, additionalData = null){
        let results = retrieveResults(response);

        let typeData = typeof additionalData.type !== "undefined" ? additionalData.type : {};
        let paramData = typeof additionalData.param !== "undefined" ? additionalData.param : {};
        let itemData = {};
        results.map(result => {
            const editionId = `et${result.id}`;
            let astroObjectId = "ao0";
            let typeId = "tt0";

            if (result.hasOwnProperty("table_type")) {
                /* ASTRONOMICAL OBJECT */
                if (result.table_type.hasOwnProperty("astronomical_object")) {
                    astroObjectId = `ao${result.table_type.astronomical_object.id}`;
                    const astroObjectName = normalizeText(result.table_type.astronomical_object.object_name, "Unnamed astronomical object");

                    typeData = this.addToAstroObject(typeData, editionId, astroObjectName, astroObjectId);
                    itemData = this.addToAstroObject(itemData, editionId, astroObjectName, astroObjectId);
                } else {
                    typeData = this.addToAstroObject(typeData, editionId);
                    itemData = this.addToAstroObject(itemData, editionId);
                }

                /* TABLE TYPES */
                typeId = `tt${result.table_type.id}`;
                const typeName = normalizeText(result.table_type.table_type_name, "Unnamed table type");

                typeData[astroObjectId].children = this.addToData(typeData[astroObjectId].children, editionId, false, typeName, typeId);
                itemData[astroObjectId].children = this.addToData(itemData[astroObjectId].children, editionId, true, typeName, typeId);

            } else {
                typeData = this.addToAstroObject(typeData, editionId);
                itemData = this.addToAstroObject(itemData, editionId);

                typeData[astroObjectId].children = this.addToData(typeData[astroObjectId].children, editionId, false,"Unknown table type", "tt0");
                itemData[astroObjectId].children = this.addToData(itemData[astroObjectId].children, editionId, true,"Unknown table type", "tt0");
            }

            /* PARAMETER SETS */
            // if the "table_contents" property exists, it means that it contains also the property "parameter_sets"
            if (result.hasOwnProperty("table_contents")){
                for (let i = result.table_contents.length - 1; i >= 0; i--) {
                    for (let j = result.table_contents[i].parameter_sets.length - 1; j >= 0; j--) {
                        const param = result.table_contents[i].parameter_sets[j];
                        const paramId = `ap${param.id}`;
                        const paramName = normalizeText(param.default_title, "null");

                        paramData[astroObjectId].children[typeId].children = this.addToData(paramData[astroObjectId].children[typeId].children, editionId, false, paramName, paramId);
                    }
                }
            }

            let noItem = true;
            /* ORIGINAL TEXTS */
            if (result.hasOwnProperty("original_texts")){
                noItem = false;
                for (let i = result.original_texts.length - 1; i >= 0; i--) {
                    const item = result.original_texts[i];
                    const itemId = `oi${item.id}`;
                    const itemName = normalizeText(item.original_text_title, "Unnamed original item");

                    itemData[astroObjectId].children[typeId].children = this.addToData(itemData[astroObjectId].children[typeId].children, editionId, false, itemName, itemId);
                }
            }
            // if the "related_editions" property exists, it means that it contains also the property "original_texts"
            if (result.hasOwnProperty("related_editions")){
                noItem = false;
                for (let i = result.related_editions.length - 1; i >= 0; i--) {
                    for (let j = result.related_editions[i].original_texts.length - 1; j >= 0; j--) {
                        const item = result.related_editions[i].original_texts[j];
                        const itemId = `oi${item.id}`;
                        const itemName = normalizeText(item.original_text_title, "Unnamed original item");

                        itemData[astroObjectId].children[typeId].children = this.addToData(itemData[astroObjectId].children[typeId].children, editionId, false, itemName, itemId);
                    }
                }
            }

            if (noItem){
                itemData[astroObjectId].children[typeId].children = this.addToData(itemData[astroObjectId].children[typeId].children, editionId, false, "No original\nitem associated", "oi0");
            }
        });

        return {
            type: Object.values(typeData).map(object => objectToArrayProperty(object, "children")),
            param: Object.values(paramData).map(object => objectToArrayProperty(object, "children")),
            item: Object.values(itemData).map(object => objectToArrayProperty(object, "children"))
        };
    }
}