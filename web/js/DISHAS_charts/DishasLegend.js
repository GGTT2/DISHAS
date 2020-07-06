/* jshint esversion: 6 */

/**
 * The DishasLegend has clickable items
 */
class DishasLegend {
    /**
     * @param XYchart
     * @param elementId : string of the id of the DOM element where to generate the legend
     */
    constructor(XYchart, elementId){
        this.XYchart = XYchart;
        this.legendData = XYchart.dishasChart.datasets.legend;
        this.elementId = elementId;
        this.astroObjects = [
            {name: "sun", label: "Sun"},
            {name: "mercury", label: "Merc."},
            {name: "venus", label: "Venus"},
            {name: "moon", label: "Moon"},
            {name: "mars", label: "Mars"},
            {name: "jupiter", label: "Jupiter"},
            {name: "saturn", label: "Saturn"},
            {name: "sphericalAstronomical", label: "Spher."},
            {name: "eighthSphere", label: "8<sup>th</sup> sph."},
            {name: "eclipse", label: "Eclipse"},
            {name: "trigonometrical", label: "Trigo."},
        ];

        $(`#${elementId}`).html(`
            <div class="legend-parent">
                <section class="legend container">
                    <header>
                        <h3>Legend</h3>
                    </header>
        
                    <div class="legend-container"></div>
                </section>
            </div>`);

        // for each astronomical object
        for (let i = 1; i < this.astroObjects.length + 1; i++) {
            let astroObject = this.astroObjects[i-1];
            // create an legend item
            $(".legend-container").append(`
                <section class="box">
                    <div class="border-${i}">
                        <section class="color-legend ${astroObject.name}"></section>
                    </div>
                    <h4>${astroObject.label}</h4>
                </section>`);

            // select the legend item corresponding to the current astronomical object
            let legendItem = $(`.border-${i}`);
            // select the section that contains the color of the legendItem
            let legendColor = legendItem.children();

            // if the legend data contains data for this astronomical object
            if (Object.keys(this.legendData).includes(String(i))){
                // make it clickable
                legendItem.addClass("clickable-legend");
                legendItem.click(() => {
                    let filter = `Records which subject is : ${this.XYchart.dishasChart.datasets.box[`ao${i}`].title}`;
                    this.XYchart.deselectItem(null);
                    this.XYchart.dishasChart.idsClicked = this.legendData[i];
                    this.XYchart.dishasChart.generateBoxes(filter);
                });
            } else {
                legendColor.addClass("unclickable-legend");
            }
        }
    }
}

/*export {DishasLegend};*/
