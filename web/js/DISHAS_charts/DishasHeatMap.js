/* jshint esversion: 6 */

/*import {DishasAbstractChart} from "./DishasChart";*/

class DishasHeatmapChart extends DishasAbstractChart {
    constructor(dishasChart, data){
        /**
         * Inherited properties :
         *  => this.dishasChart
         *  => this.amObject
         *  => this.data
         *  => this.config
         *  => this.series
         */
        super(dishasChart, data);

        /**
         * Instance of the amcharts XYchart object
         * @type {{}}
         */
        this.amObject = {};

        /**
         * Minimal date in the heat map dataset
         */
        this.minDate = null;

        /**
         * Maximal date in the heat map dataset
         */
        this.maxDate = null;

        /**
         * Percentage representing a year according to minimal and maximal date
         */
        this.yearRange = null;

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
     *  => this.generateBoxTitle()
     */

    generate(){
        this.amObject = this.dishasChart.container.createChild(am4charts.XYChart);
        this.minDate = getMinValueInArray(Object.keys(this.data));
        this.maxDate = getMaxValueInArray(Object.keys(this.data));
        this.yearRange = 1 / (this.maxDate - this.minDate);

        this._generateConfig();
        this._generateAllSeries();
    }

    _generateConfig(){
        this.amObject.toFront();
        this.amObject.height = 240;
        this.amObject.y = 510;
        this.amObject.x = -10;
        this.amObject.data = Object.values(this.data).sort((a, b) => (a.year > b.year) ? 1 : -1);

        // Create axes
        let xAxes = this.amObject.xAxes.push(new am4charts.CategoryAxis());
        xAxes.dataFields.category = "year";
        let yAxes = this.amObject.yAxes.push(new am4charts.CategoryAxis());
        yAxes.dataFields.category = "i";

        // Creating a cursor for the heatmap
        this.amObject.cursor = new am4charts.XYCursor();
        this.amObject.cursor.lineY.disabled = true;
        this.amObject.cursor.behavior = "none";
        yAxes.tooltip.disabled = true;
        // Configuring the content of tooltip for the year axis
        xAxes.tooltip.background.fill = am4core.color("#9b9b9b");
        xAxes.tooltip.background.strokeWidth = 0;
        xAxes.tooltip.background.cornerRadius = 3;
        xAxes.tooltip.background.pointerLength = 0;
        /*xAxes.tooltip.pointerOrientation = "down";*/
        xAxes.tooltip.dy = -150;

        // An adapter can modify the "normal" behavior of any amCharts object
        // Here it changes how the tooltip text is displayed when the user is hovering the heat map
        xAxes.adapter.add("getTooltipText", (date) => {
            const yearData = this.data[date];
            let tooltips = "";
            let number = 0;
            for (let j = this.config.series.length -1; j >= 0; j--) {
                let entity = this.config.series[j].entity;
                let entityName = yearData[entity] > 1 ? pluralize(this.dishasChart.entity[entity].heatmapTooltip) : this.dishasChart.entity[entity].heatmapTooltip;
                if (yearData[entity] > 0){number ++;}
                tooltips = tooltips + `\n${entityName} : [bold]${yearData[entity]}[/]`;
            }

            return `[bold]${date} — ${parseInt(date)+10}[/]${tooltips}${number === 0 ? "" : "\nClick to see more"}`;
        });

        // Configuration of the background grid
        xAxes.renderer.minGridDistance = 50; // tighten date labels
        xAxes.renderer.grid.template.strokeOpacity = 0;
        yAxes.renderer.grid.template.strokeOpacity = 0;
        xAxes.renderer.labels.template.fill = am4core.color("#636266");
        xAxes.renderer.labels.template.dy = -10;
        yAxes.renderer.labels.template.hidden = true;
    }

    _generateAllSeries(){
        for (let i = this.config.series.length -1; i >= 0; i--) {
            this.series[this.config.series[i].entity] = this._generateSeries(this.config.series[i]);
        }
    }

    _generateSeries(config){
        let series = this.amObject.series.push(new am4charts.ColumnSeries());
        series.dataFields.value = config.entity;
        series.dataFields.categoryX = "year";
        series.dataFields.categoryY = "i";
        series.columns.template.fill = am4core.color(config.color);
        series.columns.template.width = am4core.percent(100);

        this.setSeriesHoverState(series.columns.template);
        this.setClickedHighlight(series.columns.template);

        // in order to show boxes that are associated with a date when clicking on a heat map stripe
        series.columns.template.events.on("hit", ev => {
            this.hitEvent(ev.target);
        });

        const isYearEmpty = (yearData, series) => {
            let empty = true;
            for (let i = series.length - 1; i >= 0; i--) {
                // if all the series in year data are not empty
                if (yearData[series[i].entity] > 0) {empty = false;}
            }
            return empty;
        };

        // in order to change the cursor appearance when hovering heatmap series
        series.columns.template.events.on("over", (ev) => {
            ev.target.cursorOverStyle = am4core.MouseCursorStyle.pointer;
            const yearData = this.data[ev.target.dataItem.dataContext.year];

            // if the is an item created at this date, change the cursor to be a pointer
            if (! isYearEmpty(yearData, this.config.series)){
                ev.target.cursorOverStyle = am4core.MouseCursorStyle.pointer;
            } else {
                ev.target.cursorOverStyle = am4core.MouseCursorStyle.default;
            }
        });

        series.heatRules.push({
            target: series.columns.template,
            property: "fillOpacity",
            min: 0,
            max: 0.8
        });

        return series;
    }

    hitEvent(eventTarget) {
        const yearData = this.data[eventTarget.dataItem.dataContext.year];
        const ids = typeof yearData.ids !== "undefined" ? yearData.ids : [];
        super.hitEvent(eventTarget, ids);
        this.showClickedState(eventTarget);
    }

    generateBoxTitle(eventTarget) {
        let clickedDate = eventTarget.dataItem.dataContext.year;
        let s = this.dishasChart.idsClicked.length > 1 ? "s" : "";
        if (parseInt(clickedDate) === 0){
            return `Undated record${s} and record${s} created in the decade of 0-10`;
        } else {
            return `Record${s} created in the decade of ${clickedDate}-${parseInt(clickedDate)+10}`;
        }
    }
}

/*export {DishasHeatmapChart};*/
