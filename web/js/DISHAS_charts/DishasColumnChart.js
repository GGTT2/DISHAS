/* jshint esversion: 6 */

/*import {DishasMainChart} from "./DishasChart";*/

class DishasColumnChart extends DishasXYChart {
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
         * Array of all primary sources as formulated in the category labels
         * @type {*[]}
         */
        this.sources = [];

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
        this.amObject = am4core.create(this.dishasChart.config.elementId.chart, am4charts.XYChart);
        this.sources = this.getSources();

        this._generateConfig();
        this._generateAllSeries();
    }

    _generateConfig(){
        // Create an export button
        this.amObject.exporting.menu = new am4core.ExportMenu();

        // Create axes
        this.categoryAxis = this.amObject.xAxes.push(new am4charts.CategoryAxis());
        this.categoryAxis.dataFields.category = "primSource";
        this.categoryAxis.renderer.grid.template.location = 0;

        this.categoryAxisZoom(this.sources, "X", 6);

        let valueAxis = this.amObject.yAxes.push(new am4charts.ValueAxis());
        valueAxis.min = 0;
        // add the page unit after the axis labels
        valueAxis.renderer.labels.template.adapter.add("text", (label) => {
            if (label === "0") return "0";
            // all page values are converted in pages which allow to always show "pp."
            return `${label} pp.`;
        });

        // Configuration column labels display
        let labels = this.categoryAxis.renderer.labels.template;
        labels.wrap = true;
        labels.maxWidth = 120;
        labels.cursorOverStyle = am4core.MouseCursorStyle.pointer;
        labels.propertyFields.dummydata = "PS-ids";

        let shadow = labels.filters.push(new am4core.DropShadowFilter);
        shadow.opacity = 0;
        shadow.dx = 2;
        shadow.dy = -2;
        shadow.blur = 3;

        this.setCategoryHoverState(labels);

        labels.events.on("hit", ev => {
            this.hitEvent(ev.target);
        });
    }

    _generateAllSeries(){
        this.amObject.data = this.data;

        // Create a series for each original item in the dataset
        for (let i = this.data.length - 1; i >= 0; i--) {
            this.series[this.data[i]["OI-id"][0]] = this._generateSeries(i);
        }
    }

    _generateSeries(field){
        let series = this.amObject.series.push(new am4charts.ColumnSeries());

        series.dataFields.openValueY = "from";
        series.dataFields.valueY = field;
        series.dataFields.categoryX = "primSource";

        series.columns.template.width = am4core.percent(60);

        this.configureSeries(series);

        return series;
    }

    generateBoxTitle(eventTarget){
        if (eventTarget.className === "Column"){
            // ON CLICK ON A COLUMN ELEMENT
            return `Original item originated from the <i>${eventTarget.dataItem.dataContext.primSource.slice(6).split("[/]")[1]}</i>`;
        } else {
            // ON CLICK ON A LABEL
            return `Records originated from the <i>${eventTarget.currentText.slice(6).split("[/]")[1]}</i>`;
        }
    }

    /**
     * This methods returns the array of all primary sources contained in the chart
     * @return {Array}
     */
    getSources(){
        let sources = [];
        for (let i = this.data.length - 1; i >= 0; i--) {
            if (! sources.includes(this.data[i].primSource)){
                sources.push(this.data[i].primSource);
            }
        }
        return sources;
    }
}

/*export {DishasColumnChart};*/
