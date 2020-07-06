/* jshint esversion: 6 */

class DishasScrollbar {
    constructor(dishasChart){
        this.dishasChart = dishasChart;
        this.config = dishasChart.config;
        for (let i = dishasChart.charts.length - 1; i >= 0; i--) {
            if (dishasChart.charts[i].type === "Map") {
                this.dishasMap = dishasChart.charts[i].chart;
            } else if (dishasChart.charts[i].type === "Heatmap") {
                this.dishasHeatmap = dishasChart.charts[i].chart;
            }
        }
        this.amObject = new am4charts.XYChartScrollbar();
        this.timeframeLabel = this.createBoxLabel(this.dishasMap, 480);

        this.generate();
    }

    generate(){
        this.dishasHeatmap.amObject.scrollbarX = this.amObject;

        for (let i = this.config.series.length -1; i >= 0; i--) {
            this.amObject.series.push(Object.values(this.dishasHeatmap.series)[i]);
        }

        this.amObject.events.on("rangechanged", () => {
            this.updateMap();
        });

        this.dishasHeatmap.amObject.zoomOutButton.events.on("hit", () => {
            this.updateMap(true);
        });

        this.bindSeriesInLegend();
    }

    /**
     * Creates a box on the map in which it is possible to display labels
     *
     * @param chart object : chart in which the label must appear
     * @param y int : vertical offset from the top
     */
    createBoxLabel(chart, y) {
        let box = this.dishasChart.container.createChild(am4core.Container);
        box.width = 100;
        box.height = 35;
        box.align = "center";
        box.y = y;
        box.padding(10, 10, 10, 10);
        box.background.fill = am4core.color("#000");
        box.background.fillOpacity = 0.1;
        let boxLabel = box.createChild(am4core.Label);
        boxLabel.align = "center";

        return boxLabel;
    }

    /**
     * update the appearance of the map pins according to the time range selected by the scrollbar
     * @param isZoomOut : boolean value indicating if the event was triggered on click on the zoom out button
     */
    updateMap(isZoomOut = false){
        this.dishasHeatmap.deselectItem(null);
        this.dishasMap.deselectItem(null);
        $(`#${this.config.elementId.box}`).empty();

        let cursorMin = isZoomOut ? 0 : this.amObject.range.start;
        let cursorMax = isZoomOut ? 1 : this.amObject.range.end;

        // Conversion of the min and max range value into date (parseInt to get rid of the floating values)
        let dateMinRange = parseInt((cursorMin / this.dishasHeatmap.yearRange) + this.dishasHeatmap.minDate);
        let dateMaxRange = parseInt((cursorMax / this.dishasHeatmap.yearRange) + this.dishasHeatmap.minDate);
        // Show the timerange selected
        this.timeframeLabel.text = `${dateMinRange} — ${dateMaxRange}`;

        // set the Unknown place label to be transparent
        this.dishasMap.unknownPlaceLabel.fillOpacity = 0;

        // Get the index of th first and last object in the dataset corresponding to the selected timerange
        let start = Math.floor(cursorMin * this.dishasHeatmap.amObject.data.length);
        let end = Math.ceil(cursorMax * this.dishasHeatmap.amObject.data.length);

        // retrieve the sub-dataset containing all the data for the selected timerange
        let timeData = this.dishasHeatmap.amObject.data.slice(start,end);
        let timerangeIds = [];
        // add to timerangeIds all the ids of the items that are present between the date min and date max
        timeData.map(yearData => {if (typeof yearData.ids !== "undefined") timerangeIds.push(...yearData.ids);});
        // remove all duplicate ids
        timerangeIds = timerangeIds.filter(unique);

        // for each place in the map dataset
        for (let i = Object.keys(this.dishasMap.data).length - 1; i >= 0; i--) {
            let latlong = Object.keys(this.dishasMap.data)[i];
            let place = this.dishasMap.data[latlong];

            // for each series that is displayed on the map
            for (let l = this.config.series.length -1; l >= 0; l--) {
                // retrieve all ids that are both in the timeframe selected (timerangeIds)
                // and in the current place (place.ids[entityName])
                let entityName = this.config.series[l].entity;
                if (place.ids[entityName].length !== 0) {
                    // add to the ids property the ids currently visible (intersection of time and place)
                    this.dishasMap.mapPins[entityName][latlong].properties.dummyData.ids =
                        timerangeIds.filter(value => place.ids[entityName].includes(value));
                    // change the radius and the tooltip of the pin
                    this.dishasMap.definePinAppearance(this.dishasMap.mapPins[entityName][latlong], this.dishasChart.entity[entityName].name);
                }
            }
        }
    }

    /**
     * Allows to toggle map and time chart series simultaneously on click on legend items
     */
    bindSeriesInLegend() {
        for (let i = this.config.series.length - 1; i >= 0; i--) {
            const entity = this.config.series[i].entity;
            const mapSeries = this.dishasMap.series[entity];
            const timeSeries = this.dishasHeatmap.series[entity];

            mapSeries.events.on("hidden", () => {
                timeSeries.hide();
            });

            mapSeries.events.on("shown", () => {
                timeSeries.show();
            });
        }
    }
}

/*export {DishasScrollbar};*/
