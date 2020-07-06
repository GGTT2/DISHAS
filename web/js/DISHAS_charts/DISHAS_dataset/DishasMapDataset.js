/* jshint esversion: 6 */

/**
 * This method defines a dataset that can be used to generate a DishasMapChart
 * and to fill a instance of DishasDataset
 */
class DishasMapDataset {
    constructor(){
        this.type = "Map";
        this.data = [];
    }

    /**
     * This method adds a new data instance (new place) to the dataset
     * @param latitude
     * @param longitude
     * @param placeName
     */
    addData(latitude, longitude, placeName){
        this.data.push(new DishasMapData(latitude, longitude, placeName));
    }

    /**
     * This function takes the results of an elasticsearch ajax call
     * and fill the "places" object given as argument with the results contained in the response
     *
     * "places" looks like this :
     * {
     *     "location1" : [
     *         { ...Result 1... },
     *         { ...Result 2... }
     *     ],
     *     "location2" : [
     *         { ...Result 3... }
     *     ]
     * }
     * @param results : object containing all results of an object of results from an ajax call to elasticsearch
     * @param entity : string of the entity name in snake_case
     * @param fieldLists
     * @param places : object to fill
     * @return {*}
     */
    static generatePlacesData(results, entity, fieldLists, places={}){
        let fieldList = fieldLists[entity];

        for (let i = results.length - 1; i >= 0; i--) {
            if (isDefined(results[i].place)){
                places = addToObject(places, DishasMapData.generatePlaceMetadata(entity,fieldList,results[i]), results[i].place.location, false);
            } else {
                places = addToObject(places, DishasMapData.generatePlaceMetadata(entity,fieldList,results[i]), "0,0", false);
            }
        }
        return places;
    }

    /**
     * This method takes an object of results (from an ajax call to ElasticSearch)
     * ordered by place and return an array looking like this
     * (each object of the array corresponding to one unique place) :
     * {
     *     lat,long: {
     *          "lat": latitude,
     *          "long": longitude,
     *          "place": "place name",
     *          "ids": {
     *              work: [wo1, wo6, etc.],
     *              primarySource: [ps3, etc.]
     *              }
     *          }
     *     }
     * }
     *
     * @param places : object containing all the place metadata of the result of a query ordered by place
     * @return {Array}
     */
    static generateMapData(places){
        let mapData = {};
        for (let i = Object.values(places).length - 1; i >= 0; i--) {
            let place = Object.values(places)[i];
            let latlong = place[0].latlong.split(",");
            let placeData = {
                "lat": latlong[0], // if the latitude was not provided, the value of latlong is equal to [0]
                "long": isDefined(latlong[1]) ? latlong[1] : 0,
                "place": place[0].place,
                "ids": {work: [], primarySource: []}
            };

            for (let j = place.length - 1; j >= 0; j--) {
                let item = place[j];
                let entity = item.entity === "work" ? "work" : "primarySource";
                placeData.ids[entity].push(item.entity === "work" ? "wo"+item.id : "ps"+item.id);
            }
            mapData[place[0].latlong] = placeData;
        }

        return mapData;
    }
}

/**
 * This class defines the object that can be used to fill an instance of DishasMapDataSet
 */
class DishasMapData {
    constructor(latitude, longitude, placeName){
        this.lat = latitude;
        this.long = longitude;
        this.place = placeName;
        this.items = [];
    }

    /**
     * This method creates an new item in the array of items associated with a place
     * @param entityName
     * @param id
     * @param title
     * @param tpq
     * @param taq
     */
    addItem(entityName, id, title, tpq, taq){
        this.items.push(
            {
                "entity": entityName,
                "id": id,
                "title": title,
                "from": tpq,
                "to": taq
            }
        );
    }

    /**
     * This static method generates for each field of a result (retrieved from an list of results from an ElasticSearch query)
     * a formatted string that is going to be displayed as metadata of the current result
     *
     * Returns :
     * {
     *     title : "formatted title",
     *     fieldname : "formatted information",
     *     otherfieldname: "formatted information"
     * }
     *
     * @param entity : string of the entity name in snake_case
     * @param template : object containing all the properties for the entity being currently treated
     * @param resultObject : object containing one result of an object of results from an ajax call to elasticsearch
     */
    static generatePlaceMetadata(entity, template, resultObject) {
        let placeMetadata = {};
        for (let i = template.length - 1; i >= 0; i--) {
            if (template[i].name === "title"){
                placeMetadata.title = formatTitle(resultObject, template[i], entity);
            } else {
                placeMetadata[template[i].name] = formatText(retrieveNodeValue(template[i], resultObject), template[i].properties);
            }
        }
        placeMetadata.entity = entity;
        return placeMetadata;
    }
}