/* jshint esversion: 6 */

/**
 * This class is used to define the main characteristics of a chart
 * It defines if a legend must be displayed, the height of the chart and sets the ids
 * of the DOM elements where the chart and the record boxes will appear
 */
class DishasConfig {
    constructor(height="40em", isLegend=true, elementIdChart="chartdiv", elementIdBox="boxdiv"){
        this.elementId = {visualization: elementIdChart, chart: "", legend: "", box: elementIdBox, noData: "alert-info"};
        this.isLegend = isLegend;
        this.isContainer = false;
        this.isScrollbar = false;
        this.height = height;
    }
}

/**
 * This class is used when creating a chronological map
 * It defines which part of the map is visible to the user when loading the page
 * (homeZoomLevel : at what level is the map zoomed in?
 *  homeGeoPoint : geographical coordinates of the map centre)
 *
 * It defines is the countries are visible (showCountries)
 *
 * It defines how each series represented on the map will look like
 * In the array of the series property, each object will set an entity name,
 * a color and an angle in order to create map pins and heat map stripes accordingly
 *
 * config = {
 *  	homeZoomLevel: 2.1,
 *  	homeGeoPoint: {latitude:30, longitude: 60},
 *  	showCountries: false,
 *  	series:
 *  	[
 *  		{
 *  			entity: "entityName",
 *  			color: "color",
 *  			angle: 110
 *  		}, { ... }
 *  	]
 *  }
 */
class DishasMapConfig extends DishasConfig {

    /**
     * @param colorEntity object : {entityName1: "color", entityName2: "color"}
     * @param lat array : all latitudes of the map points
     * @param long array : all longitudes of the map points
     */
    constructor(colorEntity, lat=[], long=[]){
        super();
        this.isScrollbar = true;
        this.isContainer = true;
        this.isLegend = false;
        this.height = "52em";

        this.homeZoomLevel = 2.1;
        this.homeGeoPoint = {latitude:30, longitude: 60};

        this.showCountries = false;

        if (lat.length !== 0){
            this.homeGeoPoint.latitude = getMiddleValue(lat);
        }
        if (long.length !== 0){
            this.homeGeoPoint.longitude = getMiddleValue(long);
        }
        if (lat.length !== 0 && long.length !== 0){
            // ESSAYER DE FAIRE UN TRUC POUR LE ZOOM LEVEL
        }

        // Depending on the number of series to display, define the angle of the map points
        const angle = {
            1: [90],
            2: [70, 110],
            3: [50, 90, 130],
            4: [30, 70, 110, 150],
            5: [10, 50, 90, 130, 170],
            6: [15, 45, 75, 105, 135, 165],
            7: [90, 60, 30, 0, 180, 150, 120],
            8: [-45, -15, 45, 75, 105, 135, 165, -165],
            9: [90, 60, 30, 0, -30, -150, 180, 150, 120],
            10: [-75, -45, -15, 45, 75, 105, 135, 165, -165, -135],
            11: [90, 60, 30, 0, -30, -60, -120, -150, 180, 150, 120],
            12: [90, 60, 30, 0, -30, -60, -90, -120, -150, 180, 150, 120]
        };
        let seriesAngle = angle[Object.keys(colorEntity).length];

        this.series = [];
        for (let i = Object.keys(colorEntity).length - 1; i >= 0; i--) {
            let entityName = Object.keys(colorEntity)[i];
            this.series.push({
                entity: entityName,
                color: colorEntity[entityName],
                angle: seriesAngle[i]
            });
        }
    }
}

class DishasTreemapConfig extends DishasConfig{
    constructor(currentNode) {
        super();
        this.currentNode = currentNode;
        this.isLegend = false;
        this.height = currentNode === "TT-rec" ? "16em" : "400px";
    }
}

/*export {DishasConfig, DishasMapConfig};*/
