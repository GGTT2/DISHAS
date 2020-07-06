/* jshint esversion: 6 */

/**
 * This method defines a dataset that can be used to generate a DishasTreemapChart
 * this.chart = [{
 *          type: "Treemap",
 *          data: [treemap data on astronomical objects and table types]
 *      }];
 */
class DishasTreemapDataset extends DishasDataset {
    /**
     * @param editionData : object of response of an elasticsearch query performed on the edited_text index
     * @param uneditedData : object additional data on all table types and parameter sets that might
     *                  not be associated with an edition
     */
    constructor(editionData = null, uneditedData = null){
        super();
        this.chart = [{
            type: "Treemap",
            data: editionData ? this.generateDatasetFromResponse(editionData, uneditedData) : null
        }];
    }

    addToAstroObject(data, name = "Unknown astronomical object", id = "ao0", color = "rgb(200, 200, 200)") {
        if (!data.hasOwnProperty(id)) {
            data[id] = {
                name: name.capitalize(),
                id: id,
                nbParam: 0,
                nbEdition: 0,
                color: color,
                children: {}
            };
        }
        data[id].nbEdition += 1;
        return data;
    }

    addToData(data, isParent = true, name = "Unnamed record", id = "0") {
        if (!data.hasOwnProperty(id)) {
            data[id] = {};
            data[id].name = name.capitalize();
            data[id].nbParam = 0;
            data[id].nbEdition = 0;
            data[id].id = id;
            data[id].count = 1;
        }
        data[id].count += 1;
        data[id].nbEdition += 1;
        return data;
    }

    /** * * * * * * * * * * * * * * * * * * * * * *
     * Elasticsearch result object :
     * [{
     * 		"table_type" : {
     *  		"id" : "table type id", "table_type_name" : "lunar equation",
     *  		"astronomical_object" : {
     *  			"id" : "astro object id",
     *    			"object_name" : "moon",
     *    		},
     *		}
     *	}]
     * * * * * * * * * * * * * * * * * * * * * * * *
     * additionalData
     * { astroId: {
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
     *     }, {...}]
     * },{...}]
     *
     * @param response : object of response of an elasticsearch query untreated on the edited text index
     * @param additionalData : object additional data on all table types and parameter sets that might
     *                         not appear in the response object because they are not associated with any edited text
     */
    generateDatasetFromResponse(response, additionalData = null){
        let results = retrieveResults(response);

        let typeData = additionalData ? additionalData : {};
        results.map(result => {
            let astroObjectId = "ao0";
            let typeId = "tt0";

            if (result.hasOwnProperty("table_type")) {
                /* ASTRONOMICAL OBJECT */
                if (result.table_type.hasOwnProperty("astronomical_object")) {
                    astroObjectId = `ao${result.table_type.astronomical_object.id}`;
                    const astroObjectName = normalizeText(result.table_type.astronomical_object.object_name, "Unnamed astronomical object");

                    typeData = this.addToAstroObject(typeData, astroObjectName, astroObjectId);
                } else {
                    typeData = this.addToAstroObject(typeData);
                }

                /* TABLE TYPES */
                typeId = `tt${result.table_type.id}`;
                const typeName = normalizeText(result.table_type.table_type_name, "Unnamed table type");
                typeData[astroObjectId].children = this.addToData(typeData[astroObjectId].children, false, typeName, typeId);

            } else {
                typeData = this.addToAstroObject(typeData);
                typeData[astroObjectId].children = this.addToData(typeData[astroObjectId].children, false,"Unknown table type", "tt0");
            }
        });

        Object.values(typeData).map(object => objectToArrayProperty(object, "children"));

        return typeData;
    }
}