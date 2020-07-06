/* jshint esversion: 6 */

/*import {DishasAbstractChart} from "./DishasChart";*/

class DishasTreemapChart extends DishasAbstractChart {
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
         * Instance of the amcharts treemap object
         * @type {{}}
         */
        this.amObject = {};

        /**
         * object containing all the metadata related to the astronomical object/table type that has been clicked on
         * @type {{}}
         */
        this.visibleObject = {};

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
        // use the animated theme to prevent bugs in chart display
        am4core.useTheme(am4themes_animated);

        this.amObject = am4core.create(this.config.elementId.chart, am4charts.TreeMap);

        this._generateConfig();
        this._generateAllSeries();
    }

    _generateConfig(){
        // Create an export button
        this.amObject.exporting.menu = new am4core.ExportMenu();
    }

    _generateAllSeries(){
        this.amObject.data = Object.values(this.data);

        // only one level visible initially
        this.amObject.maxLevels = 1;
        // define data fields
        this.amObject.dataFields.value = "count";
        this.amObject.dataFields.name = "name";
        this.amObject.dataFields.children = "children";
        this.amObject.dataFields.color = "color";
        this.amObject.propertyFields.dummyData = "nbParam";
        this.amObject.propertyFields.dummyData = "nbEdition";

        /* ASTRONOMICAL OBJECT SERIES TEMPLATE */
        let astroObjectsSeries = this.amObject.seriesTemplates.create("0");
        astroObjectsSeries.strokeWidth = 2;

        let astroObjectTemplate = astroObjectsSeries.columns.template;
        astroObjectTemplate.column.cornerRadius(7, 7, 7, 7);
        this.setSeriesHoverState(astroObjectTemplate);
        this.addBackgroundImage(astroObjectTemplate);

        astroObjectTemplate.tooltipText = this.config.currentNode === "astro-nav" ?
            `[font-size: 18px; bold]{name}[/]
            [bold]{nbEdition}[/] table edition(s) and [bold]{nbParam}[/] parameter set(s)
            associated with this object` :
            `[font-size: 18px; bold]{name}[/]
            {nbEdition} table edition(s) and {nbParam} parameter set(s)
            related to this table type`;

        astroObjectTemplate.events.on("hit", (ev) => {
            this.hitEvent(ev.target, "astroObject");
        });

        /* TABLE TYPE SERIES TEMPLATE */
        let tableTypeSeries = this.amObject.seriesTemplates.create("1");
        let tableTypeTemplate = tableTypeSeries.columns.template;
        tableTypeTemplate.fillOpacity = 0;

        tableTypeTemplate.events.on("hit", ev => {
            this.hitEvent(ev.target, "tableType");
        });
        const TTtooltip = this.config.currentNode === "astro-nav" ?
            `[font-size: 16px; bold]{parentName}[/]
             [font-size: 16px]{name}[/]
             [bold]{nbEdition}[/] table edition(s) and [bold]{nbParam}[/] parameter set(s)
             related to this table type` :
            "[font-size: 16px; bold]{parentName}[/]\nParameter: {name}\n{nbEdition} edition(s) related";
        tableTypeTemplate.tooltipText = TTtooltip;

        let tableTypeLabel = tableTypeSeries.bullets.push(new am4charts.LabelBullet());
        tableTypeLabel.locationX = 0.5;
        tableTypeLabel.locationY = 0.5;
        tableTypeLabel.label.text = "[font-size: 16px; bold]P: {name}[/]";
        tableTypeLabel.cursorOverStyle = am4core.MouseCursorStyle.pointer;

        tableTypeLabel.events.on("hit", ev => {
            this.hitEvent(ev.target, "tableType");
        });
        tableTypeLabel.tooltipText = TTtooltip;

        this.amObject.zoomOutButton.events.on("hit", () => {
            this.hideAstroObject();
        });

        this.amObject.zoomOutButton.marginRight = 30;
        this.amObject.zoomOutButton.marginTop = 8;
    }

    hitEvent(eventTarget, level) {
        if (level === "astroObject"){
            this.visibleObject = eventTarget.dataItem.dataContext.dataContext;
            if (this.config.currentNode === "astro-nav"){
                this.dishasChart.idsClicked = this.visibleObject.typeIds;
                this.showAstroObject();
            } else {
                this.dishasChart.idsClicked = this.visibleObject.editionIds;
                this.dishasChart.generateBoxes(`Editions and parameters related to « ${this.visibleObject.name} »`);
            }
        } else if (level === "tableType"){
            const data = eventTarget.dataItem.dataContext;
            if (this.config.currentNode === "astro-nav"){
                this.dishasChart.idsClicked = data.dataContext.id;
                this.dishasChart.generateBoxes(`${data.parent.dataContext.name} | ${data.dataContext.name}`);
            } else {
                this.dishasChart.idsClicked = data.dataContext.editionIds;
                this.dishasChart.generateBoxes(`Records related to the parameter « ${data.name} »`);
            }
        }
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
        image.adapter.add("href", (href, target) => {
            const dataItem = target.parent.dataItem;
            if (dataItem) {
                const propName = this.config.currentNode === "astro-nav" ? "name" : "objectName";
                const astroObjectName = dataItem.dataContext.dataContext[propName].toCamelCase();

                return imgPath.replace("IMG", `pictograms/${astroObjectName}.png`);
            }
        });
    }

    /**
     * Automatically zoom in the treemap to display the table types associated with the astronomical object
     * that the id was given as parameter
     * @param id
     */
    zoomToObject(id){
        window.setTimeout(() => {
            if (this.config.currentNode === "astro-nav"){
                this.amObject.dataItems.each((dataItem) => {
                    if (dataItem.dataContext.id === `ao${id}`){
                        this.amObject.zoomToChartDataItem(dataItem);
                        this.visibleObject = this.data[`ao${id}`];
                        this.dishasChart.idsClicked = this.visibleObject.typeIds;
                        this.showAstroObject();
                    }
                });
            } else {
                this.amObject.dataItems.each((dataItem) => {
                    if (dataItem.dataContext.id === id){
                        this.amObject.zoomToChartDataItem(dataItem);
                        this.visibleObject = this.data[0];
                        this.dishasChart.idsClicked = this.visibleObject.editionIds;
                        this.dishasChart.generateBoxes(`Editions and parameters related to « ${this.visibleObject.name} »`);
                    }
                });
            }
        }, 1200);
    }

    /**
     * Display on the screen the definition of the astronomical object and the table type boxes associated with it
     */
    showAstroObject(){
        $("#astronomical-object-metadata").html(`<h2 style="padding-left: 50px">${this.visibleObject.name} definition</h2><br/>
                                            « <span class="astronomical-object-definition">${this.visibleObject.def}</span> »<br/>
                                            <div class="row astronomical-object-number">
                                                ${this.generateQuery("parameter_set")} 
                                                <span class="col-md-1">and</span>
                                                ${this.generateQuery("edited_text")} 
                                                <span class="col-md-5">related in the database.</span>
                                            </div>`);
        this.dishasChart.generateBoxes(`Table types related to « ${this.visibleObject.name} »`);
    }

    /**
     * Remove all information related to an astronomical object display below the treemap
     */
    hideAstroObject(){
        this.visibleObject = {};
        $("#astronomical-object-metadata").empty();
        $(`#${this.config.elementId.box}`).empty();
    }

    /**
     * Generate a html link that will redirect to the search page and post a query to the elasticsearch server
     * in order to allow user to access records related to the currently clicked astronomical object
     * @param entity
     * @return {string}
     */
    generateQuery(entity){
        const query = `{"match": {"table_type.astronomical_object.id": "${this.visibleObject.id.replace("ao", "")}"}}`;
        const entityName = entity === "parameter_set" ? "parameter set" : "table edition";
        const entityNumber = this.visibleObject[entity === "parameter_set" ? "nbParam" : "nbEdition"];
        const nEntity = entityNumber > 1 ? "s" : "";

        return `<form class="col-md-3" action="${generateRoute("tamas_astro_search")}" method="post" style="padding: 0;">
                    <button type="submit"
                            name="query"
                            value='{
                                "query":${query},
                                "title":"All ${entityName} that are associated to the « ${this.visibleObject.name} »",
                                "entity":"${entity}"
                                }'
                            title="Find all ${entityName} associated with the ${this.visibleObject.name}"
                            data-value="Find all ${entityName} associated with the ${this.visibleObject.name}"
                            class="btn-link send-query">
                        <span class="glyphicon glyphicon-search" style="padding-right: 10px;"></span> ${entityNumber} ${entityName}${nEntity}
                    </button>
                </form>`;
    }
}

/*export {DishasTreemapChart};*/
