/* jshint esversion: 6 */

/*import {DishasBoxDataset, DishasBoxData} from 'DishasBoxDataset';
import {DishasChronoMapDataset, DishasChronoMapData} from 'DishasChronoMapDataset';
import {DishasHeatmapDataset, DishasHeatmapData} from 'DishasHeatmapDataset';
import {DishasMapDataset, DishasMapData} from 'DishasMapDataset';
import {DishasLegendDataset} from 'DishasLegendDataset';*/

/**
 * This class can be given to a instance of DishasChart
 * in order to generate a chart with the data it contains
 */
class DishasDataset {
    constructor(){
        this.chart = [];
        this.box = [];
        this.legend = [];
    }

    /**
     * This method adds a dataset to the array of chart datasets
     * dataset = {type: "chart type", data: [chart data]}
     * @param dataset
     */
    addChartDataset(dataset){
        this.chart.push(dataset);
    }

    /**
     * This method associate a dataset to an instance of a DishasDataset in order to generate record boxes
     * @param dataset
     */
    addBoxDataset(dataset){
        this.box = dataset;
    }

    /**
     * This method associate a dataset to an instance of a DishasDataset in order to generate a clickable legend
     * @param dataset
     */
    addLegendDataset(dataset){
        this.legend = dataset;
    }
}