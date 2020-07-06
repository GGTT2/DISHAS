/* jshint esversion: 6 */

/*import {DishasMainChart} from "./DishasChart";*/

class DishasGraphChart extends DishasAbstractChart {
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
         * Instance of the amcharts graph object
         * @type {{}}
         */
        this.amObject = {};

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
     *  => this.seriesHoverState()
     *  => this.setSeriesHoverState()
     *  => this.setSeriesActiveState()
     *  => this.generateBoxTitle()
     */

    generate(){
        this.amObject = am4core.create(this.config.elementId.chart, am4plugins_forceDirected.ForceDirectedTree);

        this._generateConfig();
        this._generateAllSeries();
    }

    _generateConfig(){
        // Create an export button
        this.amObject.exporting.menu = new am4core.ExportMenu();
    }

    _generateAllSeries(){
        this.series = this.amObject.series.push(new am4plugins_forceDirected.ForceDirectedSeries());
        this.amObject.data = this.data;

        // associate data to chart properties
        this.series.dataFields.value = "value";
        this.series.dataFields.name = "name";
        this.series.dataFields.children = "children";
        this.series.dataFields.color = "color";
        this.series.dataFields.fixed = "fixed";
        this.series.nodes.template.propertyFields.x = "x";
        this.series.nodes.template.propertyFields.y = "y";
        this.series.nodes.template.propertyFields.dummydata = "id";

        // add "Current edition" to the main node
        this.series.nodes.template.label.propertyFields.text = "title";
        this.series.nodes.template.label.fill = am4core.color("black");
        this.series.nodes.template.label.dy = 100;

        // disable collapsible nodes
        this.series.nodes.template.togglable = false;

        // configure graph appearance
        this.series.nodes.template.tooltipText = "{name}";
        this.series.nodes.template.fillOpacity = 1;
        this.series.nodes.template.outerCircle.fillOpacity = 0;
        this.series.nodes.template.outerCircle.strokeWidth = 0;
        this.series.links.template.strokeWidth = 2;
        this.series.minRadius = am4core.percent(3);
        this.series.nodes.template.cursorOverStyle = am4core.MouseCursorStyle.pointer;

        this.setSeriesHoverState(this.series.nodes.template);

        // add stroke that will  be visible on click on a node
        this.setClickedHighlight(this.series.nodes.template.circle);

        // define physics
        this.series.manyBodyStrength = -50;
        this.series.links.template.distance = 1;
        this.series.links.template.strength = 1;

        let picto = this.series.nodes.template.createChild(am4core.Image);
        picto.propertyFields.href = "picto";
        picto.horizontalCenter = "middle";
        picto.verticalCenter = "middle";

        this.series.nodes.template.events.on("hit", ev => {
            this.hitEvent(ev.target);
        });
    }

    hitEvent(eventTarget) {
        super.hitEvent(eventTarget.circle, eventTarget.dummydata);
        this.showClickedState(eventTarget.circle);
    }

    generateBoxTitle(eventTarget) {
        return eventTarget.dataItem.name.charAt(0).toUpperCase() + eventTarget.dataItem.name.slice(1);
    }
}

/*export {DishasGraphChart};*/
