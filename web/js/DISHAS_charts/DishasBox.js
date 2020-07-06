/* jshint esversion: 6 */

/*import {DishasEntity} from './DishasEntity.js';*/

const astroObjectInfo = {
    0: {name: "unknownObject", textColor: "#333"},
    1: {name: "sun", textColor: "#333"},
    2: {name: "mercury", textColor: "#333"},
    3: {name: "venus", textColor: "#333"},
    4: {name: "moon", textColor: "#333"},
    5: {name: "mars", textColor: "white"},
    6: {name: "jupiter", textColor: "#333"},
    7: {name: "saturn", textColor: "#333"},
    8: {name: "sphericalAstronomical", textColor: "white"},
    9: {name: "eighthSphere", textColor: "white"},
    10: {name: "eclipse", textColor: "white"},
    11: {name: "trigonometrical", textColor: "#333"}
};

/**
 * This class is used to generate record boxes under the chart
 */
class DishasBox {
    /**
     * This generates a box to display info and redirect to record pages
     *
     * @param container : jQuery element corresponding to the HTML container for all the boxes
     * @param entityInfo : object containing all the information relative to the entity depicted by the box
     * @param boxData : object containing all the info used to generate the box
     *      {
     *         id: id,
     *         title: "text appearing as title",
     *         keyName1: "text corresponding",
     *         keyName2: "text corresponding",
     *        (keyName3: "text corresponding")
     *     }
     */
    constructor(container, entityInfo, boxData) {
        this.entity = entityInfo.entityName;

        /**
         * Data that will be displayed in the box itself
         * @type {Object}
         */
        this.data = boxData;

        /**
         * Information on how the box should be displayed according to the entity it represents
         * @type {{}}
         */
        this.entityInfo = entityInfo;

        /**
         * Name of the entity of the box in the wording used in the public interface
         * @type {string}
         */
        this.entityName = "";

        /**
         * Title of the box appearing in the upper part
         * @type {string}
         */
        this.title = "";

        /**
         * Path of the pictogram image corresponding to the entity in the assets
         * @type {string}
         */
        this.img = imgPath;

        /**
         * Text appearing in the box body
         * @type {string}
         */
        this.boxInfo = "";

        /**
         * HTML string in order to display a magnifier icon with a link attached to it
         * in order to redirect either to the corresponding record page or the search page
         * @type {string}
         */
        this.magnifier = "";

        /**
         * Color used to display the title on the background color of the box for ease of reading
         * @type {string}
         */
        this.textColor = "";

        this.generateBox(container);

    }

    /**
     * Generates the HTML box in the container given as parameter
     * @param container
     */
    generateBox(container){
        this.entityName = this.entityInfo.name;
        this.title = truncate(trimHtmlTags(this.data.title).replace(/_/g, " "), 40);
        // this.textColor = this.entityInfo.textColor; => style="color: ${this.textColor}"
        this.boxInfo = this.generateBoxInfo(this.entityInfo.boxInfo);

        // IF THE BOX IS SUPPOSED TO REDIRECT TO THE SEARCH PAGE
        if (this.data.hasOwnProperty("query")){
            this.generateQueryMagnifier();
        // IF THE BOX IS SUPPOSED TO REDIRECT TO A RECORD PAGE
        } else {
            this.generateMagnifier();
        }

        this.img = this.img.replace("IMG", `pictograms/${this.entity}.png`);

        container.append(
            `<div class="item-display col-md-3 ${this.entity}">
                 <div class="row designation-row">
                     <div class="record-designation">
                         ${this.generateHeader()}
                     </div>
                 </div>
                 <div class="record-desc">
                     ${this.boxInfo}
                     ${this.magnifier}
                 </div>
             </div>`);
    }

    generateHeader(){
        if (this.data.hasOwnProperty("astroObject")){
            const astroObject = astroObjectInfo[this.data.astroObject];
            const astroImg = imgPath.replace("IMG", `pictograms/${astroObject.name}.png`);

            return `<div class="col-md-2 astro-picto ${astroObject.name}">
                        <img src="${astroImg}" alt="${astroObject.name} pictogram" style="height: 30px; margin-left: 5px"/>
                    </div>
                    <div class="col-md-2" style="margin-left: -5px">
                        <img src="${this.img}" alt="${this.entityName} pictogram" style="height: 30px"/>
                    </div>
                    <div class="col-md-7 col-title">
                        <h4 class="item-title record-title">${this.title}</h4>
                    </div>`;
        } else {
            return `<div class="col-md-2">
                        <img src="${this.img}" alt="${this.entityName} pictogram" style="height: 30px"/>
                    </div>
                    <div class="col-md-10 col-title">
                        <h4 class="item-title record-title">${this.title}</h4>
                    </div>`;
        }
    }

    /**
     * Generates the descriptive text to be displayed in the box body
     */
    generateBoxInfo(){
        const boxInfo = this.entityInfo.boxInfo; // array of property names corresponding to the information that will appear in the box
        // if there is more than one piece of information to be included in the box body
        if (boxInfo.length > 1){
            // Define the first line of info appearing on the box
            let info1;
            if (boxInfo.includes("tpaq")){
                info1 = getTpaq(this.data.from, this.data.to) + "<br/>";
            } else {
                info1 = truncateWithHtml(this.data[boxInfo[0]], 29) + "<br/>";
            }
            // Define the second line
            const info2 = truncateWithHtml(this.data[boxInfo[1]], 29) + "<br/>";
            // Define the third line in case there is one
            const info3 = boxInfo.length === 3 ? `${truncateWithHtml(this.data[boxInfo[2]], 29)}<br/>` : "";

            return `<span class="record-info">${info1}</span>
                    <span class="record-info">${info2}</span>
                    <span class="record-info">${info3}</span>`;
        // if there only one definition to include
        } else {
            return `<div class="record-def">${this.data[boxInfo[0]].truncate(82)}</div><br/>`;
        }
    }

    /**
     * Change the properties values when displaying a box of an astronomical object or a table type
     * allowing to use the correct color and pictogram
     * @param id
     */
    getAstroObjectInfo(id){
        const object = astroObjectInfo[id];
        this.entity = object.name;
        this.entityName = object.name;
        this.textColor = object.textColor;
    }

    /**
     * generate the HTML string to display a magnifier icon that will redirect to the record page
     */
    generateMagnifier(){
        // Generate URL to the record page corresponding
        const url = generateRoute(this.entityInfo.path, this.data.id);

        if (this.entity === "astronomicalObject"){
            this.getAstroObjectInfo(this.data.id);
        } else if (this.entity === "tableType"){
            this.getAstroObjectInfo(this.data.astroId);
        }

        this.magnifier = `<span class="glyphicon glyphicon-search"></span>&#0009;
                          <a href="${url}" title="Go to the record page">See record</a>`;
    }

    /**
     * This method is used to generate a magnifier icon that will redirect to the search page
     * (used only for the mathematical parameter boxes)
     * @return {string}
     */
    generateQueryMagnifier(){
        const query = this.data.query;

        this.magnifier = `<div class="row">
                             <span class="glyphicon glyphicon-search col-md-1" style="padding-right: 10px;"></span>
                             <form class="col-md-10" action="${generateRoute("tamas_astro_search")}" method="post" style="padding: 0;">
                                 <button type="submit"
                                         name="query"
                                         value='{
                                     "query":${query.query},
                                     "title":"${query.title}",
                                     "entity":"${query.entity}"
                                     }'
                                         title="${query.hover}"
                                         data-value="${query.title}"
                                         class="btn-link send-query">
                                     Find usages
                                 </button>
                             </form>
                         </div>`;
    }
}

/*export {DishasBox};*/
