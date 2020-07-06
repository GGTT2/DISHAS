/* jshint esversion: 6 */

/**
 * This method defines a dataset that can be used to generate a DishasChronoMapChart
 * A DishasChronoMapDataset contains a DishasMapDataset and an DishasHeatmapDataset
 * and to fill a instance of DishasDataset
 */
class DishasChronoMapDataset extends DishasDataset {
    constructor(){
        super();
        this.chart = [{
                type: "Map",
                data: null
            },{
                type: "Heatmap",
                data: null
        }];
    }

    /**
     * This method adds a new data instance to the map dataset
     * @param dataset
     */
    addMapDataset(dataset){
        let mapData = this.chart.find(function (o) {
            return o.type === "Map";
        });
        mapData.data = dataset;
    }

    /**
     * This method adds a new data instance to the heatmap dataset
     * @param dataset
     */
    addHeatmapDataset(dataset){
        let heatmapData = this.chart.find(function (o) {
            return o.type === "Heatmap";
        });
        heatmapData.data = dataset;
    }

    /**
     *
     * @param Wresponse : object of response of an elasticsearch query untreated on the "work" index
     * @param PSresponse : object of response of an elasticsearch query untreated on the "primary_source" index
     * @param fieldLists : instance of a TAMASListColumnTemplate
     */
    generateDatasetFromResponse(Wresponse, PSresponse, fieldLists){
        let PSresults = retrieveResults(PSresponse);
        let Wresults = retrieveResults(Wresponse);

        /* DATASET FOR THE MAP */
        let places = DishasMapDataset.generatePlacesData(PSresults, "original_text", fieldLists);
        places = DishasMapDataset.generatePlacesData(Wresults, "work", fieldLists, places);
        let mapData = DishasMapDataset.generateMapData(places);

        /* DATASET FOR THE TIMELINE */
        let timeData = DishasHeatmapDataset.getTimeline(Wresults, PSresults);

        /* DATASET FOR THE BOXES */
        for (let i = fieldLists.original_text.length - 1; i >= 0; i--) {
            // in order to show in the boxes the dates of the all primary source (not only the tpq-taq of the original item)
            if (fieldLists.original_text[i].source === "taq"){fieldLists.original_text[i].source = "primary_source.taq";}
            if (fieldLists.original_text[i].source === "tpq"){fieldLists.original_text[i].source = "primary_source.tpq";}
        }
        let boxesData = DishasBoxDataset.generateBoxesData(PSresults, fieldLists.original_text, "ps");
        boxesData = DishasBoxDataset.generateBoxesData(Wresults, fieldLists.work, "wo", boxesData);

        this.addBoxDataset(boxesData);
        this.addHeatmapDataset(timeData);
        this.addMapDataset(mapData);
    }

}