/* jshint esversion: 6 */

/* ==================================================== BOXES DATA ==================================================== */
/**
 * This method defines a dataset that can be used to generate a DishasBox
 * and to fill a instance of DishasDataset
 */
class DishasBoxDataset {
    constructor(){
        this.type = "Box";
        this.data = {};
    }

    /**
     * This method adds a new data instance (new record) to the dataset
     * @param entity
     * @param id
     * @param title
     * @param minDate
     * @param info2
     * @param info3
     */
    addData(entity, id, title, minDate, info2, info3=""){
        let entityInfo = DishasEntity.getEntity(entity);
        this.data[`${entityInfo.prefixId}${id}`] = new DishasBoxData(entityInfo, id, title, minDate, info2, info3);
    }

    /**
     * Returns an html string that will be used as metadata in the record boxes
     *
     * @example metadataDisplay(["Kūshyār ibn Labbān","John of Saxony"],{"unknown": "Unknown creator"})
     *          => "Kūshyār ibn Labbān & John of Saxony"
     * @example metadataDisplay([],{})
     *          => "<span class='noInfo>No information provided</span>"
     *
     * @param textValues
     * @param properties
     * @return {string}
     */
    static metadataDisplay(textValues = [], properties = {}){
        if (isDefined(textValues)){
            let cellContent = "";
            for (let i = 0; i < textValues.length; i++){
                if (i !== 0){
                    cellContent += " & ";
                }
                cellContent += `${textValues[i]}`;
            }
            return cellContent;
        } else {
            return noInfo(properties.unknown);
        }
    }

    /**
     * This function returns an object that can be used to generate
     * the inserts below the historical map
     *
     * @param results : object to be formatted
     * @param fieldList : object of the class TAMASListTableTemplate
     * @param prefix : string to put before the key name
     * @param formattedResults : object in which the formatted results will be put
     */
    static generateBoxesData(results, fieldList, prefix="", formattedResults={}){
        let sourceFields = generateSources(fieldList, true);
        let places = {};
        for (let result of results){
            let i;
            let formattedResult = {};
            for (i = fieldList.length - 1; i >= 0; i--) {
                // textDisplay returns the value that is going to be displayed in the cell :
                // 		- the text correctly formatted (according to the field.properties)
                // 		- OR no information provided (if there is no value in the result array)
                // selectNodeOfObject returns the value of the node in the array of result
                // corresponding to string given in sourceFields[i]
                if (isDefined(sourceFields[i])){
                    formattedResult[fieldList[i].name] = this.metadataDisplay(selectNodeOfObject(sourceFields[i], result), fieldList[i].properties);
                }
            }

            /* IN ORDER TO SHOW WHEN A PRIMARY SOURCE WAS CREATED IN MULTIPLE PLACES */
            let place = formattedResult.place;
            let id = formattedResult.id;
            if (formattedResults.hasOwnProperty(`${prefix}${id}`))
            { // if the current primary source is already in the array of formatted results
                if (! checkIfArrayContainsString(place, places[`${prefix}${id}`])){
                    // if the places associated with this primary source does not contain the current place
                    places[`${prefix}${id}`].push(place);
                    // add the current place to places
                    if (places[`${prefix}${id}`].length > 2){ // if there is more than 2 places of creation
                        formattedResults[`${prefix}${id}`].place = places[`${prefix}${id}`].length+"<i> creation places</i>";
                        // change the text to display to be "N creations places"
                    } else { // if there is only two different places of creation
                        // Keep only the country in the place name
                        let firstPlace = places[`${prefix}${id}`][0].includes(",") ? places[`${prefix}${id}`][0].split(", ")[places[`${prefix}${id}`][0].split(", ").length-1] : places[`${prefix}${id}`][0];
                        let secondPlace = places[`${prefix}${id}`][1].includes(",") ? places[`${prefix}${id}`][1].split(", ")[places[`${prefix}${id}`][1].split(", ").length-1] : places[`${prefix}${id}`][1];
                        formattedResults[`${prefix}${id}`].place = `${firstPlace} & ${secondPlace}`;
                    }
                }
            } else {
                places[`${prefix}${id}`] = [place];
                // add the current place to the array of places associated with the id of the current primary source / work
                formattedResults[`${prefix}${id}`] = formattedResult;
            }
        }
        return formattedResults;
    }
}

/**
 * {
 *     id: id,
 *     title: "Title displayed in the upper part of the box, next to the icon"
 *     order: date used to order the boxes chronologically
 *     info2: "text used as second line of info in the record box"
 *     info3: "text used as third line of info in the record box"
 * }
 */
class DishasBoxData {
    constructor(entityInfo, id, title, minDate, info2, info3){
        this.id = id;
        this.title = title;

        // BESOIN D'UN CHAMPS POUR L'ORDONNANCEMENT PAR DATE :
        // IL FAUT QUE TOUS AIENT UN CHAMP POUR ORDONNER QUOIQUIL
        this.order = minDate;

        if (entityInfo.boxInfo === ["tpaq", "place"]){
            this.from = minDate;
            this.to = info2;
            this.place = info3;
        } else {
            let info = [info2, info3];
            for (let i = entityInfo.boxInfo.length - 1; i >= 0; i--) {
                this[entityInfo.boxInfo[i]] = info[i];
            }
        }
    }
}