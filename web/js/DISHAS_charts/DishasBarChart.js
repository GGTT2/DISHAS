/* jshint esversion: 6 */

/*import {DishasMainChart} from "./DishasChart";*/

class DishasBarChart extends DishasXYChart {
    constructor(dishasChart, data){
        /**
         * Inherited properties :
         *  => this.dishasChart
         *  => this.amObject
         *  => this.data
         *  => this.config
         *  => this.series
         *  => this.hoverRange
         *  => this.clickedRange
         *  => this.categoryAxis
         */
        super(dishasChart, data);

        /**
         * String indicating what type of primary source content we are dealing with
         * "pages", "folios" ou "folios/pages"
         * @type {string}
         */
        this.page = "";

        /**
         * Array of all works as formulated in the category labels
         * @type {*[]}
         */
        this.works = [];

        /**
         * Clusters of original texts to compute layout of the visualisation
         * @type {{}}
         */
        this.workBounds = {};

        /**
         * Artificially given ids to the original texts series displayed on the chart
         * @type {*[]}
         */
        this.ids = [];

        /**
         * Amcharts object of the value axis of the chart
         * @type {{}}
         */
        this.valueAxis = {};

        /**
         * Array of page cluster corresponding to original texts
         * @type {*[]}
         */
        this.pages = [];

        this.generate();
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
     *  => this.setSeriesStates()
     *  => this.setSeriesHoverState()
     *  => this.setSeriesActiveState()
     *  => this.setCategoryHoverState()
     *  => this.generateBoxTitle()
     *  => this.configureSeries()
     */

    generate(){
        am4core.useTheme(am4themes_animated);

        this.dishasChart.config.isLegend = true;
        this.dishasChart.config.elementId.chartContainer = this.dishasChart.config.elementId.chart;
        this.dishasChart.config.elementId.chart = "chart-div";

        let spreadOutSet = this.data;
        this.data = {};
        this.data.spreadOutSet = spreadOutSet;
        this.data.stackedSet = this.generateStackedDataset();
        this.data.clusteredSet = {};

        // Create a dropdown to select the visualisation
        $(`#${this.dishasChart.config.elementId.chartContainer}`).html(`
            <select style="margin-left: 2em;" id="select">
                <option selected value="spread">Page disposition</option>
                <option value="stack">Page quantity</option>
            </select>
            <div id="${this.dishasChart.config.elementId.chart}" style="height: 35em;"></div>`);

        this.amObject = am4core.create(this.dishasChart.config.elementId.chart, am4charts.XYChart);

        this.page = this.isFolio();

        this._generateConfig();
        this._generateAllSeries();

        this.selectView("spread");

        this.categoryAxisZoom(this.works, "Y", 5);
    }

    _generateConfig(){
        // Create an export button
        this.amObject.exporting.menu = new am4core.ExportMenu();

        // configuring the category axis : WORK
        this.categoryAxis = this.amObject.yAxes.push(new am4charts.CategoryAxis());
        this.categoryAxis.dataFields.category = "work";
        this.categoryAxis.renderer.inversed = true;
        this.categoryAxis.renderer.grid.template.location = 0;

        let labels = this.categoryAxis.renderer.labels.template;
        labels.wrap = true;
        labels.maxWidth = 200;
        labels.cursorOverStyle = am4core.MouseCursorStyle.pointer;
        labels.propertyFields.dummydata = "Work-ids";

        this.setCategoryHoverState(labels);

        labels.events.on("hit", ev => {
            this.hitEvent(ev.target);
        });

        // configuring the value axis
        this.valueAxis = this.amObject.xAxes.push(new am4charts.ValueAxis());
        this.valueAxis.renderer.opposite = true;
        this.valueAxis.title.text = `[font-size:18px]Cumulated number of ${this.page} per work[/]
        [font-size:12px](Multiple ${this.dishasChart.entity.originalText.name.pluralize()} might be localised on the same ${this.page} of the ${this.dishasChart.entity.primarySource.name})[/]`;

        // configuring the cursor behavior
        this.amObject.cursor = new am4charts.XYCursor();
        this.amObject.cursor.lineX.disabled = false;
        this.amObject.cursor.lineY.disabled = true;
        this.categoryAxis.cursorTooltipEnabled = false;

        // configuring the behavior when the user chooses a view
        let dropdown = document.getElementById("select");
        dropdown.addEventListener("change", () => {
            this.selectView(dropdown.value);
        });
    }

    _generateAllSeries(){
        this.amObject.data = this.data.spreadOutSet;

        // creating an object containing all the original items clustered by work
        for (let i = this.data.spreadOutSet.length; i >= 1; i--) {
            // for each item in the dataset
            let item = this.data.spreadOutSet[i-1];
            // Push to this.data.clusteredSet, all the original items that are associated with it
            if (this.works.includes(item.work)) {
                this.data.clusteredSet[item.work].push({"from" : parseFloat(item.from), to : parseFloat(item[i]), "OIid" : i});
            } else {
                this.works.push(item.work);
                this.data.clusteredSet[item.work] = [{"from" : parseFloat(item.from), to : parseFloat(item[i]), "OIid" : i}];
            }
        }

        // this.data.clusteredDataSet = {"work1" :
        //                          [{"from" : 4, "to" : 6, "OIid" : 2},
        //                           {"from" : 1, "to" : 3, "OIid" : 1}]}

        // for each work in the dataset
        for (let i = this.works.length - 1; i >= 0; i--) {
            let work = this.works[i];

            // ordering the original items by their 'from' property

            // this.data.clusteredDataSet = {"work1" :
            //         [{"from" : 4, "to" : 6, "OIid" : 1},
            //          {"from" : 1, "to" : 3, "OIid" : 2}]}

            // order the original items by from property (i.e. by page min)
            this.data.clusteredSet[work].sort(function(a,b) {
                return a.from - b.from;
            });

            // this.data.clusteredDataSet = {"work1" :
            //                          [{"from" : 1, "to" : 3, "OIid" : 1},
            //                           {"from" : 4, "to" : 6, "OIid" : 2}]}

            this.pages = this.data.clusteredSet[work];

            let j = 0;
            let bounds = [];
            // clustering pages (bounds) of set of original items that are on the same pages
            for (let l = 0 ; l < this.pages.length ; l++) {
                // for each original item in a work
                if ($.isEmptyObject(bounds)) {
                    this.instantiateBound(bounds, j, l);
                } else { // if the the pageMin ("from") of an original item is contained inside an already existing bound
                    if (this.pages[l].from >= bounds[j].bound[0] && this.pages[l].from < bounds[j].bound[1]){
                        let OImin = this.pages[l].from;
                        let OImax = this.pages[l].to;
                        let [workMin, workMax] = bounds[j].bound;

                        let min = Math.min(OImin, workMin);
                        let max = Math.max(OImax, workMax);

                        bounds[j].bound = [min, max];
                        bounds[j].origItems.push(this.pages[l].OIid);
                    } else {
                        j++;
                        this.instantiateBound(bounds, j, l);
                    }
                }
            }

            this.workBounds[work] = bounds;
            // For each work :
            // bounds = [{bound : [min, max], origItems : [id1, id2]},
            //           {bound : [min, max], origItems : [id3]}]

            // for each bound of pages in a work
            for (let i = bounds.length - 1; i >= 0; i--) {
                let bound = bounds[i];
                // for each original item contained in this page bound
                for (let j = bound.origItems.length - 1; j >= 0; j--) {
                    let id = bound.origItems[j];
                    this.ids.push(id);
                    // create a series for each original item
                    this.series[id] = this._generateSeries(id);
                }
            }
        }
    }

    _generateSeries(field){
        let series = this.amObject.series.push(new am4charts.ColumnSeries());

        series.dataFields.openValueX = "from";
        series.dataFields.valueX = field;
        series.dataFields.categoryY = "work";

        this.configureSeries(series);

        return series;
    }

    generateBoxTitle(eventTarget) {
        if (eventTarget.className === "Column"){
            // ON CLICK ON A COLUMN ELEMENT
            return `Original item originated from the <i>${eventTarget.dataItem.dataContext.work.slice(6).split("[/]")[0]}</i>`;
        } else {
            // ON CLICK ON A LABEL
            return `Records in the primary source originated from the <i>${eventTarget.currentText.slice(6).split("[/]")[0]}</i>`;
        }
    }

    /**
     * This method generates a new bound in the bounds object
     * @param bounds
     * @param j
     * @param i
     */
    instantiateBound(bounds, j, i){
        bounds[j] = {};
        bounds[j].bound = [this.pages[i].from, this.pages[i].to];
        bounds[j].origItems = [this.pages[i].OIid];
    }

    /**
     * This methods takes the spread out set and returns a stacked set
     *
     * @return {any}
     */
    generateStackedDataset(){
        const stackedSet = JSON.parse(JSON.stringify(this.data.spreadOutSet));
        let range;
        let works = {};
        for (let i = stackedSet.length; i >= 1; i--) {
            let item = stackedSet[i-1];
            if (! works.hasOwnProperty(item.work))
                works[item.work] = 0;
            range = item[i] - item.from;
            item.from = works[item.work];
            item[i] = works[item.work] + range;
            works[item.work] += range;
        }
        return stackedSet;
    }

    /**
     * This method determines if the primary source contains only folios/pages
     * or a mix of both
     */
    isFolio(){
        let count = 0;
        for (let i = this.data.spreadOutSet.length - 1; i >= 0; i--) {
            count = this.data.spreadOutSet[i].folio === false ? count-1 : count+1;
        }
        if (count === -this.data.spreadOutSet.length){
            return "pages";
        } else if (count === this.data.spreadOutSet.length){
            return "folios";
        } else {
            return "folios/pages";
        }
    }

    /**
     * This method allows the user to switch between the two views of the visualization
     * i.e. to switch between stacked dataset and spread out dataset
     *
     * @param select
     */
    selectView(select){
        this.deselectItem(null);

        if (select === "spread") {
            this.amObject.data = this.data.spreadOutSet;
            this.valueAxis.title.text = `[font-size:18px]Layout of ${this.page} in the primary source[/]
        [font-size:12px](Multiple ${this.dishasChart.entity.originalText.name.pluralize()} might be localised on the same ${this.page} of the ${this.dishasChart.entity.primarySource.name})[/]`;

            // percent of the row allocated to element display
            const heightBar = 80 * (4/this.works.length);

            for (let i = Object.values(this.workBounds).length - 1; i >= 0; i--) {
                let bounds = Object.values(this.workBounds)[i];
                for (let j = Object.values(bounds).length - 1; j >= 0; j--) {
                    let bound = Object.values(bounds)[j];
                    let numberOfItems = bound.origItems.length;
                    for (let l = numberOfItems - 1; l >= 0; l--) {
                        let id = bound.origItems[l];
                        let heightItem = heightBar/numberOfItems;
                        // setting the height of the cell in percent
                        this.series[id].columns.template.height = heightBar/numberOfItems;

                        // configuring the displacement of the cell comparing to the center
                        if (heightItem !== heightBar){
                            // offset goes from -50 (the item is centred on the upper limit of the row) to 50
                            this.series[id].columns.template.dy = -(heightBar/2 - heightItem/2) + heightItem*l;
                        }
                    }
                }
            }

            // add the page unit after the axis labels
            this.valueAxis.renderer.labels.template.adapter.add("text", (label) => {
                if (typeof label === "undefined") return "";
                if (label === "0") return "0";
                let pageUnit = this.page === "folios" ? "f." : this.page === "folios/pages" ? "p./f." : "p.";
                return `${pageUnit}${label.replace(/\D/g,'')}`;
            });

        } else if (select === "stack") {
            this.amObject.data = this.data.stackedSet;
            this.valueAxis.title.text = `[font-size:18px]Cumulated number of ${this.page} per work[/]
        [font-size:12px](Multiple original items might be localised on the same ${this.page} of the primary source)[/]`;
            for (let i = this.ids.length - 1; i >= 0; i--) {
                let id = this.ids[i];
                this.series[id].columns.template.dy = 0;
                this.series[id].columns.template.height = am4core.percent(80);
            }

            // add the page unit after the axis labels
            this.valueAxis.renderer.labels.template.adapter.add("text", (label) => {
                if (label === "0") return "0";
                let pageUnit = this.page === "folios" ? "ff." : this.page === "folios/pages" ? "pp./ff." : "pp.";
                return `${label.replace(/\D/g,'')} ${pageUnit}`;
            });
        }
    }
}

/*export {DishasBarChart};*/
