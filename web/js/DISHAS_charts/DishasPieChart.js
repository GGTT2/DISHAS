/* jshint esversion: 6 */

/*import {DishasMainChart} from "./DishasChart";*/

class DishasPieChart extends DishasAbstractChart {
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

        this.dishasChart.container.layout = "horizontal";

        /**
         * Both amcharts Pie Chart instances
         * @type {{tableType: *, astroObject: *}}
         */
        this.amObject = {
            astroObject: this.dishasChart.container.createChild(am4charts.PieChart),
            tableType: this.dishasChart.container.createChild(am4charts.PieChart)
        };

        /**
         * Series of both pie charts
         * @type {{tableType: {}, astroObject: {}}}
         */
        this.series = {
            astroObject: {},
            tableType: {}
        };

        /**
         * Currently selected slice
         * @type {{}}
         */
        this.selectedSlice = {};

        /**
         * Array containing the two svg lines connecting both pie charts
         * @type {Array}
         */
        this.lines = [];

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
        this._generateAstroObjectSeries();
        this._generateTableTypeSeries();
        this._generateConfig();
    }

    /**
     * Generate the series associated with the main pie chart, depicting the different astronomical object of the database
     * @private
     */
    _generateAstroObjectSeries(){
        this.amObject.astroObject.data = this.data;

        // Add and configure Series
        this.series.astroObject = this.amObject.astroObject.series.push(new am4charts.PieSeries());
        this.series.astroObject.dataFields.value = "sum";
        this.series.astroObject.dataFields.category = "object";
        this.series.astroObject.slices.template.propertyFields.fill = "color";
        this.series.astroObject.slices.template.states.getKey("active").properties.shiftRadius = 0;

        this.series.astroObject.slices.template.events.on("hit", event => {
            this.selectSlice(event.target.dataItem);
        });
    }

    /**
     * Generates the series associated with the sub pie chart, depicting the different table types associated
     * with an astronomical object
     *
     * @private
     */
    _generateTableTypeSeries(){
        this.amObject.tableType.width = am4core.percent(30);
        this.amObject.tableType.radius = am4core.percent(80);

        // Add and configure Series
        this.series.tableType = this.amObject.tableType.series.push(new am4charts.PieSeries());
        this.series.tableType.dataFields.value = "value";
        this.series.tableType.dataFields.category = "name";
        this.series.tableType.slices.template.states.getKey("active").properties.shiftRadius = 0;
        this.series.tableType.labels.template.disabled = true;
        this.series.tableType.ticks.template.disabled = true;
        this.series.tableType.alignLabels = false;
        /*this.series.tableType.events.on("positionchanged", this.updateLines);*/

        this.series.tableType.slices.template.events.on("hit", event => {
            // box display
        });
    }

    _generateConfig() {
        for (let i = 1; i >= 0; i--) {
            this._generateLine();
        }

        this.amObject.astroObject.events.on("datavalidated", () => {
            setTimeout(() => {
                this.selectSlice(this.series.astroObject.dataItems.getIndex(0));
            }, 1000);
        });
    }

    _generateLine(){
        let line = this.dishasChart.container.createChild(am4core.Line);
        line.strokeDasharray = "2,2";
        line.strokeOpacity = 0.5;
        line.stroke = "#434343";
        line.isMeasured = false;

        this.lines.push(line);
    }

    selectSlice(dataItem){
        this.selectedSlice = dataItem.slice;

        const fill = this.selectedSlice.fill;
        const count = dataItem.dataContext.subData.length;

        this.series.tableType.colors.list = [];
        for (let i = 0; i < count; i++) {
            this.series.tableType.colors.list.push(fill.brighten(i * 2 / count));
        }

        this.amObject.tableType.data = dataItem.dataContext.subData;
        this.series.tableType.appear();

        /*const middleAngle = this.selectedSlice.middleAngle;
        const firstAngle = this.series.astroObject.slices.getIndex(0).startAngle;
        let animation = this.series.astroObject.animate([
            { property: "startAngle", to: firstAngle - middleAngle },
            { property: "endAngle", to: firstAngle - middleAngle + 360 }], 600, am4core.ease.sinOut);
        animation.events.on("animationprogress", this.updateLines);

        this.selectedSlice.events.on("transformed", this.updateLines());*/
    }

    updateLines(){
        if (this.selectedSlice) {
            let p11 = { x: this.selectedSlice.radius * am4core.math.cos(this.selectedSlice.startAngle), y: this.selectedSlice.radius * am4core.math.sin(this.selectedSlice.startAngle) };
            let p12 = { x: this.selectedSlice.radius * am4core.math.cos(this.selectedSlice.startAngle + this.selectedSlice.arc), y: this.selectedSlice.radius * am4core.math.sin(this.selectedSlice.startAngle + this.selectedSlice.arc) };

            p11 = am4core.utils.spritePointToSvg(p11, this.selectedSlice);
            p12 = am4core.utils.spritePointToSvg(p12, this.selectedSlice);

            let p21 = { x: 0, y: -this.series.tableType.pixelRadius };
            let p22 = { x: 0, y: this.series.tableType.pixelRadius };

            p21 = am4core.utils.spritePointToSvg(p21, this.series.tableType);
            p22 = am4core.utils.spritePointToSvg(p22, this.series.tableType);

            this.lines[0].x1 = p11.x;
            this.lines[0].x2 = p21.x;
            this.lines[0].y1 = p11.y;
            this.lines[0].y2 = p21.y;

            this.lines[1].x1 = p12.x;
            this.lines[1].x2 = p22.x;
            this.lines[1].y1 = p12.y;
            this.lines[1].y2 = p22.y;
        }
    }

    deselectItem(eventTarget){
        if (this.dishasChart.clickedElement !== null){
            // remove shadow from previously clicked element
            if (this.dishasChart.clickedElement._className === "Column"){
                this.dishasChart.clickedElement.strokeWidth = 1;
            }
            this.dishasChart.clickedElement.filters.values[0].opacity = 0;
        }
        this.dishasChart.clickedElement = eventTarget;
    }
}

/*export {DishasPieChart};*/
