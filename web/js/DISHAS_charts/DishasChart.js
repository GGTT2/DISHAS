/* jshint esversion: 6 */

/*import {DishasEntity} from './DishasEntity.js';
import {DishasLegend} from './DishasLegend.js';
import {DishasBox} from './DishasBox.js';
import {DishasMapChart} from './DishasMapChart.js';
import {DishasHeatmapChart} from './DishasHeatMap.js';
import {DishasColumnChart} from './DishasColumnChart.js';
import {DishasPieChart} from './DishasPieChart.js';
import {DishasBarChart} from './DishasBarChart.js';
import {DishasScrollbar} from './DishasScrollbar.js';
import {DishasConfig, DishasMapConfig} from './DishasConfig.js';*/

/**
 * DishasCharts
 *
 * This class is an extension of the AmCharts library. It is used to create charts that are associated with record "boxes"
 *
 */
class DishasChart {
    /**
     * @param data : object instance of the class DishasDataset.
     * It must contain at least a dataset for the chart data and a dataset to generate record boxes
     *
     * data = {
     *     chart: [
     *         {type: "name of the chart that is associated with this dataset",
     *          data: [data set used with this chart]},
     *         {type: "name of an other chart used in the visualization",
     *          data: [data set used with this chart]}
     *      ],
     *      box: { dataset used to generate boxes },
     *     (legend: { legend used to generate a legend }) // optional
     * };
     *
     * @param config : object instance of the class DishasConfig
     * that defines the main characteristics of a chart
     *
     * config = {
     *     elementId: {visualization: elementIdChart, box: elementIdBox},
     *     isLegend: false, // if there is a legend to display
     *     height: "height of the chart in em",
     *     ...
     * }
     *
     * DishasChart = {
     *     datasets: {DATA PARAMETER},
     *     config: {CONFIG PARAMETER}
     *     charts: [
     *         {type: chartType, chart: {{instance of a "dishas{chartType}" containing a instance a an "amChart" of the same type}}},
     *         {type: chartType, chart: {{instance of a "dishas{chartType}" containing a instance a an "amChart" of the same type}}}
     *     ]
     *     entity: {information about the entity that can be displayed on a chart},
     *     boxes: [all the record boxes (DishasBox objects) displayed on the screen],
     *     idsAll: [all the id of the entity that are present in the chart],
     *     idsDisplayed: [id that are displayed on the screen of the user],
     *     idsClicked: [ids linked with the data represented on the element the user has clicked],
     *     noData: bool (check if the array of data is empty therefore it is not possible to display any chart)
     * }
     *
     */
    constructor(data, config) {
        /**
         * Datasets used to generate the charts, the legend and the boxes
         * @type {Object}
         */
        this.datasets = data;

        /**
         * Instance of DishasConfig defining the appearance and behavior of the chart
         * @type {Object}
         */
        this.config = config;

        /**
         *
         * @type {DishasEntity}
         */
        this.entity = new DishasEntity(); // All info about the entities that can be displayed on the chart
        this.boxes = []; // Array that contains all the boxes displayed under the chart
        this.charts = [];
        this.container = [];

        // Properties containing the ids of the entity that are displayed in the chart
        this.idsAll = this.datasets.box ? Object.keys(this.datasets.box) : []; // all the id of the entity that are present in the chart
        this.idsDisplayed = this.datasets.box ? Object.keys(this.datasets.box) : []; // id that are displayed on the screen of the user
        this.idsClicked = []; // ids linked with the data represented on the element the user has clicked
        this.clickedElement = null;

        // Property that checks if the array of data is empty therefore it is not possible to display any chart
        this.noData = false;

        this.checkIfData();

        // if there is data to display
        if (this.noData === false){
            this.generateLegend();
            this.generateContainer();
            this.generateCharts();
        } else {
            // if there is no data to display, send an error message
            $(`#${this.config.elementId.visualization}`).append(`
                <h3 id='${this.config.elementId.noData}' class='noInfo' style='text-align: center; padding: 7em 0;'>
                    There is not enough data in the database<br>
                    associated with this record to display this chart
                </h3>`);
        }
    }

    /**
     * This method checks if the dataset given to generate a DishasChart is empty
     */
    checkIfData(){
        if (typeof this.datasets.chart !== "undefined"){
            // Check if any of the datasets to generate the chart is empty
            for (let i = this.datasets.chart.length - 1; i >= 0; i--) {
                // if any of the datasets is empty, set noData to be true
                if (this.datasets.chart[i].data.length === 0){
                    this.noData = true;
                } else if (this.datasets.chart[i].data === null) {
                    this.noData = true;
                }
            }
        } else {
            this.noData = true;
        }
    }

    /**
     * Add a legend if the config says so
     * Define chart div height as well
     */
    generateLegend(){
        if (this.config.isLegend === true){
            $(`#${this.config.elementId.visualization}`).append(`
                <div class="row chart-legend">
                    <div class="col-md-11" id="col-chart" style="height: ${this.config.height};"></div>
                    <div class="col-md-1" id="col-legend"></div>
                </div>`);

            this.config.elementId.chart = "col-chart";
            this.config.elementId.legend = "col-legend";
        } else {
            this.config.elementId.chart = this.config.elementId.visualization;
            if (!!document.getElementById(this.config.elementId.visualization)){
                $(`#${this.config.elementId.visualization}`).height(this.config.height);
            }
        }
    }

    /**
     * Add a container to put charts in it if the config says so
     */
    generateContainer(){
        if (this.config.isContainer === true){
            // creating a container to put charts in it
            this.container = am4core.create(this.config.elementId.chart, am4core.Container);
            this.container.width = am4core.percent(100); // make it the same size as the div element
            this.container.height = am4core.percent(100);
            this.container.exporting.menu = new am4core.ExportMenu(); // add an exporting menu
        }
    }

    /**
     * This methods generates as many chart as there are datasets associated with a chart type
     */
    generateCharts(){
        for (let i = this.datasets.chart.length - 1; i >= 0; i--) {
            let chartType = this.datasets.chart[i].type;
            let dataSet = this.datasets.chart[i].data;

            // create all the chart associated with the chart type given in the data array
            if (chartType === 'Map'){
                this.charts.push({type: chartType, chart: new DishasMapChart(this, dataSet)});
            } else if (chartType === 'Column') {
                this.charts.push({type: chartType, chart: new DishasColumnChart(this, dataSet)});
            } else if (chartType === 'Bar'){
                this.charts.push({type: chartType, chart: new DishasBarChart(this, dataSet)});
            } else if (chartType === 'Heatmap'){
                this.charts.push({type: chartType, chart: new DishasHeatmapChart(this, dataSet)});
            } else if (chartType === "Treemap"){
                this.charts.push({type: chartType, chart: new DishasTreemapChart(this, dataSet)});
            } else if (chartType === "MultiTreemap"){
                this.charts.push({type: chartType, chart: new DishasMultiTreemapChart(this, dataSet)});
            } else if (chartType === "Graph"){
                this.charts.push({type: chartType, chart: new DishasGraphChart(this, dataSet)});
            } else if (chartType === "Pie"){
                this.charts.push({type: chartType, chart: new DishasPieChart(this, dataSet)});
            }
        }
        if (this.config.isScrollbar === true){
            this.charts.push({type: "Scrollbar", chart: new DishasScrollbar(this)});
        }
    }

    /**
     * This method generates "record boxes" under the chart
     *
     * The boxes generally display a date as info1 and a place as info2 :
     * to display a date correctly formatted, the data object needs to contain
     * two keys for this field : a key "from" corresponding to the tpq date, and a key "to" for taq
     *
     * boxData = {
     *     prefix+Id1 : {
     *         id: id,
     *         title: "text appearing as title",
     *         keyName1: "text corresponding",
     *         keyName2: "text corresponding",
     *        (keyName3: "text corresponding")
     *     },
     *     prefix+Id2 : {
     *         id: id,
     *         title: "text appearing as title",
     *         keyName1: "text corresponding",
     *         keyName2: "text corresponding",
     *        (keyName3: "text corresponding")
     *     },{ ... }
     * }
     *
     * @param title : string detailing on what the user clicked ("items created in {place name} / {date}")
     */
    generateBoxes(title) {
        // Empty the array of boxes
        this.boxes = [];

        const entityNames = {
            "wo": "work",
            "ps": "primarySource",
            "oi": "originalText",
            "et": "editedText",
            "ap": "parameterSet",
            "mp": "mathematicalParameter",
            "ao": "astronomicalObject",
            "tt": "tableType"
        };

        let divBoxes = $(`#${this.config.elementId.box}`);
        // Add a title above the boxes
        divBoxes.html(`<h2>${title}</h2>`);
        // scroll down if the records boxes are out of the screen of the user
        divBoxes[0].scrollIntoView({behavior: "smooth", block: "center"});

        const boxesData = this.datasets.box;
        const ids = this.idsClicked;

        if (typeof ids !== "undefined" && ids.length !== 0){
            let entityBox = {};
            ids.map(id => {
                const name = entityNames[id.substring(0, 2)]; // each id has a prefix that indicates with entity it is related to
                if (!entityBox.hasOwnProperty(name))
                    entityBox[name] = [];
                entityBox[name].push(id);

                if (! document.getElementById(`${name}-box`)){
                    divBoxes.append(`<div class="box-category">
                                         <span id="${name}-number">1</span>
                                         <span id="${name}-name">${this.entity[name].name}</span>
                                     </div>
                                     <div id="${name}-box" class="row"></div>
                                     <div id="${name}-page"></div>`);
                } else {
                    let recordNbspan = $(`#${name}-number`);
                    let recordNb = parseInt(recordNbspan.text())+1;
                    recordNbspan.text(recordNb);
                    if (recordNb === 2){
                        let recordNameSpan = $(`#${name}-name`);
                        recordNameSpan.text(recordNameSpan.text().pluralize());
                    }
                }
            });

            for (let i = Object.keys(entityBox).length - 1; i >= 0; i--) {
                const name = Object.keys(entityBox)[i];
                let nameBox = $(`#${name}-box`);
                $(`#${name}-page`).pagination({
                    dataSource: Object.values(entityBox)[i],
                    pageSize: 4,
                    callback: ids => {
                        nameBox.empty();
                        // sort the array of ids by their "sorting property"
                        sortArrayByObjectProperty(ids, boxesData, this.entity[name].sortProperty);

                        ids.map(id => {
                            if (typeof boxesData[id] !== "undefined"){
                                this.boxes.push(new DishasBox(nameBox, this.entity[name], boxesData[id]));
                            }
                        });
                    }
                });
            }
        } else {
            divBoxes.append("<h3 class='noInfo'>There is no item corresponding in the database</h3>");
        }
    }
}

/**
 * This class is the parent class of all the type of DishasChart (MapChart, XYChart, etc.)
 */
class DishasAbstractChart {
    constructor(dishasChart, data){
        /**
         * Instance of the dishas chart parent
         */
        this.dishasChart = dishasChart;

        /**
         * Instance of the amchart chart object
         */
        this.amObject = {};

        /**
         * Dataset used by the chart
         */
        this.data = data;

        /**
         * Configuration for the chart (DishasConfig)
         */
        this.config = this.dishasChart.config;

        /**
         * Series used by the amchart chart object
         * @type {{}}
         */
        this.series = {};

        /**
         * Event listener on the document
         */
        this.clickListener();
    }

    /**
     * Event listener on the click on all the page
     * Allows to remove boxes and highlight when clicking outside of the chart or the box div
     */
    clickListener(){
        // unselect item if the user clicked outside the chart div
        $(document).click(e => {
            // if the clicked target is not inside the chart or the box div
            const isOutsideChart = !$(e.target).closest(`#${this.dishasChart.config.elementId.visualization}`).length;
            const isOutsideBox = !$(e.target).closest(`#${this.dishasChart.config.elementId.box}`).length;

            if (isOutsideBox && isOutsideChart) {
                if (this.dishasChart.clickedElement !== null){
                    this.deselectItem(null);
                    $(`#${this.config.elementId.box}`).empty();
                }
            }
        });
    }

    /**
     * Method used to instantiate the chart object
     */
    generate(){}

    /**
     * Method used to generate one chart series
     * @private
     */
    _generateSeries(){}

    /**
     * Method used to generate all series of the chart
     * @private
     */
    _generateAllSeries(){}

    /**
     * Method to configure the chart display
     * @private
     */
    _generateConfig(){}

    /**
     * Returns the title to be displayed above the record boxes
     * To be implemented via inheritance
     */
    generateBoxTitle(){}

    /**
     * Remove the highlight on the previously clicked element
     * Remove boxes associated with it
     * and set the event target as the clicked element
     * @param eventTarget
     */
    deselectItem(eventTarget){
        if (this.dishasChart.clickedElement !== null){
            this.removeClickedState();
        }
        this.dishasChart.clickedElement = eventTarget;
    }

    /**
     * Method called on hit on certain chart elements in order to show record boxes
     * and add an highlight effect on the clicked element
     */
    hitEvent(eventTarget, idsClicked = null){
        this.deselectItem(eventTarget);
        this.dishasChart.idsClicked = idsClicked ? idsClicked : eventTarget.dummydata;
        this.dishasChart.generateBoxes(this.generateBoxTitle(eventTarget));
    }

    /**
     * Return an amcharts color object slightly darker than the one given as parameter
     * @param color
     * @return {*}
     */
    darkenColor(color){
        if (typeof color === "string"){
            color = am4core.color(color).rgb;
            /*const rgbValues = rgb.match(/\d{1,3}/g);
            rgb = {r: parseInt(rgbValues[0]), g: parseInt(rgbValues[1]), b: parseInt(rgbValues[2])};*/
        }
        return am4core.color(am4core.colors.brighten(color, -0.15));
    }

    /**
     * Create an hover state that darken the chart items on hover
     * and on click
     * @param series : object template of the chart item
     */
    setSeriesStates(series){
        series.togglable = true;

        this.setSeriesHoverState(series);
        this.setSeriesActiveState(series);

        // To prevent bugs, remove all active states of charts series
        // And bring the the click item to the front
        series.events.on("hit", (ev) => {
            ev.target.dataItem.component.toFront();
            this.amObject.series.each(series => {
                series.columns.each(column => {
                    column.isActive = false;
                });
            });
        });
    }

    /**
     * Add an hover state to series template
     * @param series
     */
    setSeriesHoverState(series){
        // darken on hover
        let hoverState = series.states.create("hover");
        hoverState.adapter.add("fill", (fill) => {
            if (fill instanceof am4core.Color){
                return this.darkenColor(fill.rgb);
            }
            return fill;
        });
    }

    /**
     * Add an active state to series template
     * @param series
     * @param stateName
     */
    setSeriesActiveState(series, stateName = "active"){
        // add stroke on click
        let clickedState = series.states.create(stateName);
        this.setClickedHighlight(clickedState.properties, true);
        /*clickedState.adapter.add("fill", (fill) => {
            if (fill instanceof am4core.Color){
                return this.darkenColor(fill.rgb);
            }
            return fill;
        });*/
    }

    /**
     * Set the highlight style for the clicked elements
     * @param template
     * @param isVisible
     */
    setClickedHighlight(template, isVisible = false){
        template.stroke = am4core.color("black");
        template.strokeOpacity = 0.3;
        template.strokeWidth = isVisible ? 3 : 0;
    }

    showClickedState(element){
        element.strokeWidth = 3;
        //element.fill = this.darkenColor(element.dataItem.dataContext.color);
    }

    removeClickedState(){
        if (this.dishasChart.clickedElement !== null){
            this.dishasChart.clickedElement.strokeWidth = 0;
            //this.dishasChart.clickedElement.circle.fill = am4core.color(this.dishasChart.clickedElement.dataItem.dataContext.color);
        }
    }
}

/**
 * Abstract class parent of DishasBarChart and DishasColumnChart
 */
class DishasXYChart extends DishasAbstractChart {
    constructor(dishasChart, data) {
        /**
         * Inherited properties :
         *  => this.dishasChart
         *  => this.data
         *  => this.config
         *  => this.series
         */
        super(dishasChart, data);

        /**
         * Range that constitutes the highlight of the column/row
         * when hovering its label
         */
        this.hoverRange = {};

        /**
         * Range that constitutes the highlight of the column/row
         * when clicking on its label
         */
        this.clickedRange = {};

        /**
         * Category axis of the chart
         * @type {{}}
         */
        this.categoryAxis = {};

        /**
         * Instance of DishasLegend
         * @type {DishasLegend}
         */
        this.legend = new DishasLegend(this,"col-legend");
    }

    /**
     * Inherited methods :
     *  => this.generate()
     *  => this._generateSeries()
     *  => this._generateAllSeries()
     *  => this._generateConfig()
     *  => this.hitEvent()
     *  => this.deselectItem()
     *  => this.darkenColor()
     *  => this.generateBoxTitle()
     *  => this.setSeriesStates()
     *  => this.setSeriesHoverState()
     *  => this.setSeriesActiveState()
     */

    /**
     * Method called on hit on certain chart elements in order to show record boxes
     * and add an highlight effect on the clicked element
     * @param eventTarget
     */
    hitEvent(eventTarget) {
        super.hitEvent(eventTarget);
        this.showClickedState(eventTarget);
    }

    /**
     * Apply the highlight on the newly clicked element
     * either a column/row element or an axis label
     */
    showClickedState(element){
        if (element.className === "AxisLabel"){
            this.clickedRange.axisFill.fillOpacity = 0.1;
            this.clickedRange.category = element.currentText;
            this.categoryAxis.invalidateDataItems();
        } else {
            element.stroke = am4core.color("black");
            element.strokeWidth = 3;
            element.strokeOpacity = 0.3;
            element.dataItem.component.toFront();
        }
    }

    /**
     * Remove the highlight on the previously clicked element
     * either a column/row element or an axis label
     */
    removeClickedState(){
        if (this.dishasChart.clickedElement !== null){
            if (this.dishasChart.clickedElement.className === "AxisLabel"){
                this.clickedRange.axisFill.fillOpacity = 0;
            } else {
                this.dishasChart.clickedElement.strokeWidth = 1;
                this.dishasChart.clickedElement.strokeOpacity = 1;
                this.dishasChart.clickedElement.stroke = am4core.color("white");
            }
        }
    }

    /**
     * Create an hover state that darken the chart columns/row on hover
     * on its label by creating a range
     * Create also a range in order to highlight the column/row when clicking on its label
     * @param categoryLabel : object template of the label
     */
    setCategoryHoverState(categoryLabel){
        this.hoverRange = this.categoryAxis.axisRanges.create();
        this.hoverRange.axisFill.fill = "black";
        this.hoverRange.label.disabled = true;
        this.hoverRange.grid.disabled = true;
        this.clickedRange = this.categoryAxis.axisRanges.create();
        this.clickedRange.axisFill.fill = "black";
        this.clickedRange.label.disabled = true;
        this.clickedRange.grid.disabled = true;

        categoryLabel.events.on("over", ev => {
            this.hoverRange.axisFill.fillOpacity = 0.1;
            this.hoverRange.category = ev.target.currentText;
            this.categoryAxis.invalidateDataItems();
        });

        categoryLabel.events.on("out", () => {
            this.hoverRange.axisFill.fillOpacity = 0;
        });
    }

    /**
     * Configure the series appearance and relation to data
     * @param series
     */
    configureSeries(series){
        // Make it stacked
        series.stacked = true;

        // Column configuration
        series.stroke = am4core.color("rgb(255,255,255)");
        series.strokeWidth = 1;
        series.strokeOpacity = 1;

        series.columns.template.propertyFields.fill = "color";
        series.columns.template.propertyFields.tooltipText = "origItemTooltip";
        series.columns.template.cursorOverStyle = am4core.MouseCursorStyle.pointer;
        series.columns.template.propertyFields.dummydata = "OI-id";

        this.setSeriesHoverState(series.columns.template);

        series.columns.template.events.on("hit", ev => {
            this.hitEvent(ev.target);
        });
    }

    /**
     * This function adds the scrollbar and triggers a zoom on the chart if there is more
     * categories than the nbVisible given as parameter
     * @param categories : array of all categories displayed in the chart category axis
     * @param orientation : string giving the orientation of the scrollbar (either "X" or "Y")
     * @param nbVisible : int maximum number of categories after which the scrollbar is added and the chart is zoomed in
     */
    categoryAxisZoom(categories, orientation, nbVisible){
        if (categories.length > nbVisible){
            this.amObject[`scrollbar${orientation}`] = new am4core.Scrollbar();

            this.amObject.events.on("ready", () => {
                window.setTimeout(() => {
                    this.categoryAxis.zoom({start: 0, end: (1 / categories.length) * (nbVisible - 1), priority: "start"}, false, false);
                }, 1500);
            });
        }
    }
}

/*export {DishasChart, DishasAbstractChart, DishasXYChart, DishasConfig, DishasMapConfig, DishasBox};*/
