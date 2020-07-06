/* jshint esversion: 6 */

/*import {DishasMainChart} from "./DishasChart";*/

class DishasMultiTreemapChart extends DishasAbstractChart {
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

        this.config.elementId.chart = {
            type: `${this.config.elementId.chart}-type`,
            param: `${this.config.elementId.chart}-param`,
            item: `${this.config.elementId.chart}-item`
        };

        // use the animated theme to prevent bugs in chart display
        am4core.useTheme(am4themes_animated);

        this.amObject = {
            // Main treemap concerning the astronomical object and table types in the database
            type: am4core.create(this.config.elementId.chart.type, am4charts.TreeMap),
            param: am4core.create(this.config.elementId.chart.param, am4charts.TreeMap),
            item: am4core.create(this.config.elementId.chart.item, am4charts.TreeMap)
        };
        this.series = {
            type: {astroObject: {}, tableType: {}},
            param: {astroObject: {}, tableType: {}, param: {}},
            item: {astroObject: {}, tableType: {}, item: {}}
        };
        this.treemapTypes = ["type", "item", "param"];

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
        this._generateConfig();
        this._generateAllSeries();
    }

    _generateConfig(){
        // Create an export button
        this.amObject.type.exporting.menu = new am4core.ExportMenu();
        /*// make series stacked horizontally
        this.amObject.type.layoutAlgorithm = this.amObject.dice;*/
    }

    _generateAllSeries(){
        // generate all three treemaps
        for (let i = this.treemapTypes.length - 1; i >= 0; i--) {
            this.generateAmTreemap(this.treemapTypes[i]);
        }
        // bind zoom in all of them
        for (let i = this.treemapTypes.length - 1; i >= 0; i--) {
            this.bindZoom(this.treemapTypes[i], "astroObject");
            this.bindZoom(this.treemapTypes[i], "tableType");
            this.bindZoomOut(this.treemapTypes[i]);
        }
    }

    generateAmTreemap(treemapType){
        this.amObject[treemapType].data = this.data[treemapType];

        // only one level visible initially
        this.amObject[treemapType].maxLevels = 1;
        // define data fields
        this.amObject[treemapType].dataFields.value = "count";
        this.amObject[treemapType].dataFields.name = "name";
        this.amObject[treemapType].dataFields.children = "children";
        this.amObject[treemapType].dataFields.color = "color";
        /*this.amObject[treemapType].propertyFields.dummyData = "editionIds";*/

        /* ASTRONOMICAL OBJECT SERIES TEMPLATE */
        let astroObjectsSeries = this.amObject[treemapType].seriesTemplates.create("0");
        this.series[treemapType].astroObject = astroObjectsSeries;
        astroObjectsSeries.strokeWidth = 2;

        let astroObjectTemplate = astroObjectsSeries.columns.template;
        astroObjectTemplate.column.cornerRadius(7, 7, 7, 7);
        this.addHoverState(astroObjectTemplate);
        this.addBackgroundImage(astroObjectTemplate);
        let numberValue = treemapType === "type" ? "table edition(s) and parameter set(s)" :
            treemapType === "item" ? "table edition(s)" : "parameter set(s)";
        astroObjectTemplate.tooltipText = `[font-size: 18px; bold]{name}[/]\n{value} ${numberValue}`;

        /* TABLE TYPE SERIES TEMPLATE */
        let tableTypeSeries = this.amObject[treemapType].seriesTemplates.create("1");
        let tableTypeTemplate = tableTypeSeries.columns.template;
        tableTypeTemplate.fillOpacity = 0;
        this.series[treemapType].tableType = tableTypeSeries;
        this.addHoverState(tableTypeTemplate); // TODO : DOESNT WORK !!

        tableTypeTemplate.tooltipText = "[font-size: 16px; bold]{parentName}[/]\n{name}";
        let tableTypeLabel = tableTypeSeries.bullets.push(new am4charts.LabelBullet());
        tableTypeLabel.locationX = 0.5;
        tableTypeLabel.locationY = 0.5;
        tableTypeLabel.label.text = "[font-size: 16px; bold]{name}[/]";

        /* ORIGINAL ITEM & PARAMETER SETS SERIES TEMPLATE */
        if (treemapType === "item" || "param"){
            // deepest series template (either parameter sets or original items)
            let deepestSeries = this.amObject[treemapType].seriesTemplates.create("2");
            deepestSeries.columns.template.fillOpacity = 0;

            this.series[treemapType][treemapType] = deepestSeries;

            let deepestLabel = deepestSeries.bullets.push(new am4charts.LabelBullet());
            deepestLabel.locationX = 0.5;
            deepestLabel.locationY = 0.5;
            deepestLabel.label.text = "[font-size: 16px; bold]{name}[/]\n{value} edition(s)";
        }
    }

    /**
     * Create an hover state that darken the treemap items on hover
     * @param template
     */
    addHoverState(template){
        // create hover state
        let hoverState = template.states.create("hover");
        // darken on hover
        hoverState.adapter.add("fill", (fill) => {
            if (fill instanceof am4core.Color) {
                return am4core.color(am4core.colors.brighten(fill.rgb, -0.2));
            }
            return fill;
        });
    }

    /**
     * Add a background image for astronomical object level items
     * @param template
     */
    addBackgroundImage(template){
        // add astronomical object pictogram
        let image = template.createChild(am4core.Image);
        image.opacity = 0.15;
        image.align = "center";
        image.valign = "middle";
        image.width = am4core.percent(80);
        image.height = am4core.percent(80);

        // add adapter for href to load correct image
        image.adapter.add("href", function (href, target) {
            const dataItem = target.parent.dataItem;
            if (dataItem) {
                const astroObjectName = dataItem.treeMapDataItem.name.toPascalCase();
                return imgPath.replace("IMG", `pictograms/${astroObjectName}.png`);
            }
        });
    }

    /**
     * Bind zoom on dataItems corresponding in the different treemaps
     * @param treemapType : string (either "type", "item" or "param")
     * @param seriesLevel : string (either "astroObject" or "tableType")
     */
    bindZoom(treemapType, seriesLevel){
        const otherTypes = this.treemapTypes.filter(type => type !== treemapType);
        this.series[treemapType][seriesLevel].columns.template.events.on("hit", (ev) => {
            for (let i = otherTypes.length - 1; i >= 0; i--) {
                if (seriesLevel === "astroObject"){
                    this.amObject[otherTypes[i]].dataItems.each(dataItem => {
                        if (dataItem.name === ev.target.dataItem.dataContext.name){
                            this.amObject[otherTypes[i]].zoomToChartDataItem(dataItem);
                        }
                    });
                } else if (seriesLevel === "tableType"){

                }
            }
        });
    }

    /**
     * Bind zoom out on all treemap when the user click on the "home button"
     * @param treemapType : string (either "type", "item" or "param")
     */
    bindZoomOut(treemapType){
        const otherTypes = this.treemapTypes.filter(type => type !== treemapType);
        this.amObject[treemapType].zoomOutButton.events.on("hit", () => {
            for (let i = otherTypes.length - 1; i >= 0; i--) {
                let otherTreemap = this.amObject[otherTypes[i]];
                otherTreemap.zoomToChartDataItem(otherTreemap._homeDataItem);
            }
        });
    }
}

/*export {DishasTreemapChart};*/
