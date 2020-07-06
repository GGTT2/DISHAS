/* jshint esversion: 6 */
am4internal_webpackJsonp(["2ff7"],{V3Xd:function(e,t,i){"use strict";Object.defineProperty(t,"__esModule",{value:!0});var r={};i.d(r,"PointedCircle",function(){return c}),i.d(r,"PinBullet",function(){return g}),i.d(r,"FlagBullet",function(){return m}),i.d(r,"Star",function(){return C});var n=i("m4/l"),o=i("1qam"),a=i("aCit"),s=i("hGwe"),l=i("Gg2j"),c=function(e){function t(){var t=e.call(this)||this;return t.className="PointedCircle",t.element=t.paper.add("path"),t.radius=18,t.pointerAngle=90,t.applyTheme(),t}return n.c(t,e),t.prototype.draw=function(){e.prototype.draw.call(this);var t=this.pointerBaseWidth,i=this.pointerLength;i<=.001&&(i=.001);var r=this.pointerAngle+180,n=this.radius;t>2*n&&(t=2*n);var o=this.pointerX,a=this.pointerY,c=s.moveTo({x:o,y:o}),u=l.DEGREES*Math.atan(t/2/i);u<=.001&&(u=.001);var p=r-u,h=r+u;c+=s.lineTo({x:o+i*l.cos(p),y:a+i*l.sin(p)}),c+=s.arcToPoint({x:o+i*l.cos(h),y:a+i*l.sin(h)},n,n,!0,!0),c+=s.lineTo({x:o,y:o}),this.path=c},Object.defineProperty(t.prototype,"radius",{get:function(){return this.getPropertyValue("radius")},set:function(e){this.setPropertyValue("radius",e,!0)},enumerable:!0,configurable:!0}),Object.defineProperty(t.prototype,"pointerAngle",{get:function(){return this.getPropertyValue("pointerAngle")},set:function(e){this.setPropertyValue("pointerAngle",e,!0)},enumerable:!0,configurable:!0}),t.prototype.getTooltipY=function(){return l.sin(this.pointerAngle)*(.8*-this.pointerLength-this.radius)-this.radius},t.prototype.getTooltipX=function(){return l.cos(this.pointerAngle)*(.8*-this.pointerLength-this.radius)},t}(o.a);a.c.registeredClasses.PointerCircle=c;var u=i("TXRX"),p=i("FzPm"),h=i("tjMS"),d=i("MIZb"),g=function(e){function t(){var t=e.call(this)||this;t.className="PinBullet";var i=new d.a,r=t.createChild(p.a);r.shouldClone=!1,r.isMeasured=!1,r.fill=i.getFor("background"),r.radius=Object(h.c)(85),t.circle=r;var n=t.background;return n.fill=i.getFor("alternativeBackground"),n.fillOpacity=1,n.pointerBaseWidth=20,n.pointerLength=20,n.radius=25,n.events.on("propertychanged",t.invalidate,t,!1),t.applyTheme(),t}return n.c(t,e),t.prototype.validate=function(){e.prototype.validate.call(this);var t=this.background,i=t.pointerX,r=t.pointerY,n=t.pointerLength,o=t.pointerBaseWidth,a=t.pointerAngle+180,s=t.radius;o>2*s&&(o=2*s);var c=l.DEGREES*Math.atan(o/2/n);c<=.001&&(c=.001);var u=a-c,p=a+c,d={x:i+n*l.cos(u),y:r+n*l.sin(u)},g={x:i+n*l.cos(p),y:r+n*l.sin(p)},y=d.x,f=g.x,b=d.y,m=g.y,v=s*s,P=Math.sqrt((f-y)*(f-y)+(m-b)*(m-b)),x=(y+f)/2-Math.sqrt(v-P/2*(P/2))*((b-m)/P),C=(b+m)/2-Math.sqrt(v-P/2*(P/2))*((f-y)/P);this.circle&&(this.circle.radius instanceof h.a&&(this.circle.width=2*s,this.circle.height=2*s));var V=this.image;V?(V.x=x,V.y=C,V.width=2*s,V.height=2*s,V.element.attr({preserveAspectRatio:"xMidYMid slice"}),this.circle&&(this.circle.scale=1/V.scale)):this.circle&&(this.circle.x=x,this.circle.y=C);var O=this.label;O&&(O.x=x,O.y=C)},Object.defineProperty(t.prototype,"image",{get:function(){return this._image},set:function(e){e&&(this._image=e,this._disposers.push(e),e.shouldClone=!1,e.parent=this,e.horizontalCenter="middle",e.verticalCenter="middle",this.circle&&(e.mask=this.circle))},enumerable:!0,configurable:!0}),Object.defineProperty(t.prototype,"label",{get:function(){return this._label},set:function(e){e&&(this._label=e,this._disposers.push(e),e.shouldClone=!1,e.parent=this,e.horizontalCenter="middle",e.verticalCenter="middle",e.textAlign="middle",e.dy=2)},enumerable:!0,configurable:!0}),t.prototype.copyFrom=function(t){e.prototype.copyFrom.call(this,t),t.image&&(this._image||(this.image=t.image.clone()),this._image.copyFrom(t.image)),this.circle&&t.circle&&this.circle.copyFrom(t.circle),t.label&&(this._label||(this.label=t.label.clone()),this._label.copyFrom(t.label))},t.prototype.createBackground=function(){return new c},t}(u.a);a.c.registeredClasses.PinBullet=g;var y=i("p9TX"),f=i("w4m0"),b=i("PTiM"),m=function(e){function t(){var t=e.call(this)||this;t.className="FlagBullet";var i=t.background;i.fillOpacity=1,i.events.on("propertychanged",t.invalidate,t,!1),i.waveHeight=1.5,i.waveLength=7,i.setWavedSides(!0,!1,!0,!1),i.strokeOpacity=1;var r=new d.a;t.stroke=r.getFor("alternativeBackground"),t.pole=t.createChild(b.a),t.pole.strokeOpacity=1,t.width=22,t.height=16;var n=new y.a;return n.padding(3,5,3,5),n.dy=1,n.events.on("propertychanged",t.invalidate,t,!1),n.events.on("positionchanged",t.invalidate,t,!1),n.strokeOpacity=0,t.label=n,t.poleHeight=10,t.applyTheme(),t}return n.c(t,e),t.prototype.validate=function(){e.prototype.validate.call(this),this.updateBackground();var t=this.background;this.pole.y1=0;var i=this.poleHeight,r=this.label,n=t.pixelHeight;i>0?(this.pole.y2=-i-n,r&&(r.y=-i-n)):(this.pole.y2=-i+n,r&&(r.y=-i)),r&&"middle"==r.horizontalCenter&&(this.pole.y2=-i)},t.prototype.updateBackground=function(){var e=this._background;if(e){var t=this.label;t?(e.x=t.maxLeft,e.width=t.measuredWidth,e.height=t.measuredHeight):(e.width=Math.abs(this.maxRight-this.maxLeft),e.height=Math.abs(this.maxBottom-this.maxTop));var i=this.poleHeight;e.y=i>0?-i-e.pixelHeight:-i}},Object.defineProperty(t.prototype,"label",{get:function(){return this._label},set:function(e){e?(this._label=e,this._disposers.push(e),e.parent=this,e.shouldClone=!1):(this._label&&this._label.dispose(),this._label=e,this.invalidate())},enumerable:!0,configurable:!0}),t.prototype.copyFrom=function(t){t.label&&(this.label=t.label.clone()),t.pole&&this.pole.copyFrom(t.pole),e.prototype.copyFrom.call(this,t)},t.prototype.createBackground=function(){return new f.a},Object.defineProperty(t.prototype,"poleHeight",{get:function(){return this.getPropertyValue("poleHeight")},set:function(e){this.setPropertyValue("poleHeight",e,!0)},enumerable:!0,configurable:!0}),t}(u.a);a.c.registeredClasses.FlagBullet=m;var v=i("Vs7R"),P=i("Mtpk"),x=i("v9UT"),C=function(e){function t(){var t=e.call(this)||this;return t.className="Star",t.pointCount=5,t.arc=360,t.radius=100,t.innerRadius=Object(h.c)(30),t.cornerRadius=0,t.innerCornerRadius=0,t.startAngle=-90,t.element=t.paper.add("path"),t.applyTheme(),t}return n.c(t,e),t.prototype.draw=function(){e.prototype.draw.call(this);var t=this.startAngle,i=this.arc,r=this.pointCount,n=this.radius,o=this.pixelInnerRadius,a=this.cornerRadius;a>n-o&&(a=n-o);var c=this.innerCornerRadius;c>n-a-o&&(c=n-a-o);for(var u=i/r/2,p="",h=0;h<r;h++){var d=t+h*i/r;if(a>0){var g={x:o*l.cos(d-u),y:o*l.sin(d-u)},y={x:n*l.cos(d),y:n*l.sin(d)},f={x:o*l.cos(d+u),y:o*l.sin(d+u)},b=l.getAngle(y,g),m=l.getAngle(y,f),v=y.x+a*l.cos(b),P=y.y+a*l.sin(b),x=y.x+a*l.cos(m),C=y.y+a*l.sin(m);p+=s.lineTo({x:v,y:P}),p+=" Q"+y.x+","+y.y+" "+x+","+C}else p+=s.lineTo({x:n*l.cos(d),y:n*l.sin(d)});if(d+=u,c>0){g={x:n*l.cos(d-u),y:n*l.sin(d-u)},y={x:o*l.cos(d),y:o*l.sin(d)},f={x:n*l.cos(d+u),y:n*l.sin(d+u)},b=l.getAngle(y,g),m=l.getAngle(y,f),v=y.x+c*l.cos(b),P=y.y+c*l.sin(b),x=y.x+c*l.cos(m),C=y.y+c*l.sin(m);p+=s.lineTo({x:v,y:P}),p+=" Q"+y.x+","+y.y+" "+x+","+C}else p+=s.lineTo({x:o*l.cos(d),y:o*l.sin(d)})}this.arc<360&&(p+=s.lineTo({x:0,y:0})),p=(p+=s.closePath()).replace("L","M"),this.path=p},Object.defineProperty(t.prototype,"startAngle",{get:function(){return this.getPropertyValue("startAngle")},set:function(e){this.setPropertyValue("startAngle",l.normalizeAngle(e),!0)},enumerable:!0,configurable:!0}),Object.defineProperty(t.prototype,"arc",{get:function(){return this.getPropertyValue("arc")},set:function(e){P.isNumber(e)||(e=360),this.setPropertyValue("arc",e,!0)},enumerable:!0,configurable:!0}),Object.defineProperty(t.prototype,"radius",{get:function(){var e=this.getPropertyValue("radius");return P.isNumber(e)||(e=0),e},set:function(e){this.setPropertyValue("radius",e,!0)},enumerable:!0,configurable:!0}),Object.defineProperty(t.prototype,"radiusY",{get:function(){var e=this.getPropertyValue("radiusY");return P.isNumber(e)||(e=this.radius),e},set:function(e){this.setPropertyValue("radiusY",e,!0)},enumerable:!0,configurable:!0}),Object.defineProperty(t.prototype,"innerRadius",{get:function(){return this.getPropertyValue("innerRadius")},set:function(e){this.setPercentProperty("innerRadius",e,!0,!1,10,!1)},enumerable:!0,configurable:!0}),Object.defineProperty(t.prototype,"pixelInnerRadius",{get:function(){return x.relativeToValue(this.innerRadius,this.radius)},enumerable:!0,configurable:!0}),Object.defineProperty(t.prototype,"cornerRadius",{get:function(){return this.getPropertyValue("cornerRadius")},set:function(e){this.setPropertyValue("cornerRadius",e,!0)},enumerable:!0,configurable:!0}),Object.defineProperty(t.prototype,"innerCornerRadius",{get:function(){return this.getPropertyValue("innerCornerRadius")},set:function(e){this.setPropertyValue("innerCornerRadius",e,!0)},enumerable:!0,configurable:!0}),Object.defineProperty(t.prototype,"pointCount",{get:function(){var e=this.getPropertyValue("pointCount");return l.max(3,e)},set:function(e){this.setPropertyValue("pointCount",e,!0)},enumerable:!0,configurable:!0}),t}(v.a);a.c.registeredClasses.Star=C,window.am4plugins_bullets=r}},["V3Xd"]);


/*import {DishasAbstractChart} from "./DishasChart";*/

class DishasMapChart extends DishasAbstractChart {

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
         * Instance of the amcharts map object
         * @type {{}}
         */
        this.amObject = {};

        /**
         * Object containing all the amcharts pins displayed on the map
         * @type {{}}
         */
        this.mapPins = {};

        /**
         * Amcharts label object indicating the "unknown place" position on the map
         * @type {{}}
         */
        this.unknownPlaceLabel = {};

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
        this.amObject = this.dishasChart.container.createChild(am4maps.MapChart);

        this._generateConfig();
        this._generateAllSeries();
    }

    /**
     * This method configures the background map
     */
    _generateConfig(){
        this.amObject.y = -70;
        this.amObject.toBack();

        // Set projection and quality definition of the map
        this.amObject.geodata = am4geodata_worldLow;
        this.amObject.projection = new am4maps.projections.Miller();
        this.amObject.deltaLongitude = -10; // move the map a little to the left

        // Create map polygon series containing delineation of the countries
        let polygonSeries = this.amObject.series.push(new am4maps.MapPolygonSeries());
        // Exclude Antartica
        polygonSeries.exclude = ["AQ"];
        // Load data (like country names and polygon shapes) from GeoJSON
        polygonSeries.useGeodata = true;
        polygonSeries.hiddenInLegend = true;

        // Configure appearance of the background map
        let polygonTemplate = polygonSeries.mapPolygons.template;
        polygonTemplate.fill = am4core.color("#d7d7d7"); // color of the countries
        polygonTemplate.stroke = am4core.color("#d7d7d7");

        if (this.dishasChart.config.showCountries === true){
            // Create hover state and set alternative fill color
            polygonTemplate.states.create("hover").properties.fill = am4core.color("#b9b9b9");
            polygonTemplate.tooltipText = "{name}"; // showing country names on hover
        }

        // Pre-zoom to Eurasia
        this.amObject.homeZoomLevel = this.config.homeZoomLevel;
        this.amObject.homeGeoPoint = this.config.homeGeoPoint;

        // Zoom control
        this.amObject.zoomControl = new am4maps.ZoomControl();
        this.amObject.zoomControl.dy = -120; // move the zoom control up

        this.amObject.legend = new am4charts.Legend();
        this.amObject.legend.position = "right";
        this.amObject.legend.dx = 10;

        // Create a label to indicate that the 0-0 coordinates is an Unknown place
        this.unknownPlaceLabel = this.createLabelOnMap("Unknown\nplace", 0, 0);
    }

    /**
     * This method generates as many series as there are series in the config object
     * @private
     */
    _generateAllSeries(){
        for (let i = this.config.series.length -1; i >= 0; i--) {
            // generates as many series as there are series in the config object
            // and add to the mapPins property all the map pins created when creating the series
            this.mapPins[this.config.series[i].entity] = this._generateSeries(this.config.series[i]);
        }
    }

    /**
     * Creates map points on the chart for a series
     * A series is symbolised with an unique color. It corresponds to a series in the dataset
     *
     * @param config : object defining the configuration for one series in particular
     * @return {Array}
     * @private
     */
    _generateSeries(config){
        const entityName = config.entity; // entity name in camelCase
        const seriesInfo = this.dishasChart.entity[entityName];

        this.series[entityName] = this.amObject.series.push(new am4maps.MapImageSeries());
        this.series[entityName].fill = am4core.color(config.color);
        this.series[entityName].name = seriesInfo.legend; // defining a name for the legend

        let seriesPins = {};

        for (let i = Object.keys(this.data).length - 1; i >= 0; i--) {
            const latlong = Object.keys(this.data)[i];
            const place = this.data[latlong];

            // if there is item of the current entity in the current place
            if (place.ids[entityName].length !== 0) {
                let entityTitle = this.dishasChart.entity[entityName].name; // entity name in "Normal case"

                // create a map point for each place in the dataset
                let placePoint = this.series[entityName].mapImages.create();
                placePoint.latitude = parseFloat(place.lat);
                placePoint.longitude = parseFloat(place.long);
                placePoint.nonScaling = true; // the map pin stays at the same size when zooming

                // define map pin appearance
                seriesPins[latlong] = placePoint.createChild(am4plugins_bullets.PinBullet); // creating a map pin for each place
                seriesPins[latlong].circle.radius = am4core.percent(100); // defining the color of the circle to be the same size as the radius

                seriesPins[latlong].circle.fill = am4core.color(config.color); // set the color of the circle
                seriesPins[latlong].background.fill = am4core.color("#919191"); // set the color of the pointy end
                seriesPins[latlong].background.pointerBaseWidth = 3; // set the width of the pointy end
                seriesPins[latlong].background.pointerLength = 14; // set the length of the pointy end
                seriesPins[latlong].background.pointerAngle = config.angle; // set the angle of the pin

                seriesPins[latlong].circle.entity = entityTitle; // add an entity property

                // define styling on hover and on click
                this.setClickedHighlight(seriesPins[latlong].circle);
                this.setSeriesHoverState(seriesPins[latlong].circle);

                // define a label to show the number of records represented by the pin
                seriesPins[latlong].label = placePoint.createChild(am4core.Label);
                seriesPins[latlong].label.fill = am4core.color("white");
                seriesPins[latlong].label.paddingBottom = 3;
                // override tooltip automatic background color
                seriesPins[latlong].label.tooltip.getFillFromObject = false;
                seriesPins[latlong].label.tooltip.background.fill = am4core.color(config.color);

                // add to the map point a property that defines which item and place it represents
                seriesPins[latlong].properties.dummyData = {ids: place.ids[entityName], placeName: place.place};
                // the set radius and tooltip accordingly
                this.definePinAppearance(seriesPins[latlong], entityTitle);
                seriesPins[latlong].circle.cursorOverStyle = am4core.MouseCursorStyle.pointer; // make the cursor a point when hovering the circle
                seriesPins[latlong].label.cursorOverStyle = am4core.MouseCursorStyle.pointer;

                seriesPins[latlong].circle.events.on("hit", event => {
                    this.hitEvent(event.target);
                });
                seriesPins[latlong].label.events.on("hit", event => {
                    this.hitEvent(event.target);
                });
            }
        }
        return seriesPins;
    }

    /**
     * allow to show boxes on click on a map pin
     * @param eventTarget
     */
    hitEvent(eventTarget){
        if (eventTarget.className === "Label"){
            eventTarget = eventTarget.parent.circle;
        }
        super.hitEvent(eventTarget, eventTarget._parent.properties.dummyData.ids);
        this.showClickedState(eventTarget);
    }

    generateBoxTitle(eventTarget) {
        // pluralize if there is multiple records to display
        const entityName = this.dishasChart.idsClicked.length > 1 ? pluralize(eventTarget.entity) : eventTarget.entity;
        return `${entityName} created in ${eventTarget._parent.properties.dummyData.placeName}`;
    }

    hitAnimation(element){
        let animation = element.animate([
                            {property:"strokeWidth", from:0, to:20},
                            {property:"strokeOpacity", from:0.6, to:0}
                            ], 500);
        animation.events.on("animationended", () => {
            this.hitAnimation(element);
        });
    }

    /**
     * Creates a label next to a map point
     *
     * @param text string : text of the label
     * @param lat number : latitude of the point
     * @param long number : longitude of the point
     */
    createLabelOnMap(text, lat, long) {
        let mapSeries = this.amObject.series.push(new am4maps.MapImageSeries());
        mapSeries.hiddenInLegend = true;

        let place = mapSeries.mapImages.create();
        place.latitude = lat;
        place.longitude = long;

        let placeText = place.createChild(am4core.Label);
        placeText.text = text;
        placeText.fontSize = 13;
        placeText.fill = am4core.color("black");
        placeText.verticalCenter = "middle";
        placeText.paddingLeft = 14;
        placeText.nonScaling = true;

        return placeText;
    }

    /**
     * This method defines the radius and the tooltip of a pin
     * according the ids stored in its dummyData property
     * @param pin
     * @param entityName
     */
    definePinAppearance(pin, entityName){
        let ids = pin.properties.dummyData.ids;

        // if there is records associated with the place
        if (ids.length !== 0){
            // set the radius of the pin to to have a radius proportional to the number of records
            pin.background.radius = Math.log(ids.length*100)*1.5;

            // defining tooltip appearing when hovering a map point
            let more = ids.length >= 2 ? "\n‣  [bold]. . .[/]" : "";
            entityName = ids.length >= 2 ? pluralize(entityName) : entityName;
            let title = "";
            if (typeof this.dishasChart.datasets.box[ids[0]] !== "undefined"){
                let recordData = this.dishasChart.datasets.box[ids[0]];
                // defining title of one record of the records associated with the map pin
                title = `\n‣ ${trimHtmlTags(recordData.title)} ${getTpaq(recordData.from, recordData.to)}`;
            }

            let tooltip = `[bold; font-size:18px]${ids.length} ${entityName}[/]${title}${more}
                            Click to see more`;
            let placeName = trimHtmlTags(pin.properties.dummyData.placeName);

            pin.label.text = ids.length;
            pin.label.tooltipText = `[font-size:18px]${placeName}[/]
                                               ${tooltip}`;

            // if the pin is place on 0,0 (i.e. the "Unknown place" location) is visible
            if (placeName === "Unknown place"){
                this.unknownPlaceLabel.fillOpacity = 0.4;
            }
        } else {
            // if there is no data to show, make the pin disappear
            pin.label.text = "";
            pin.label.tooltipText = "";
            pin.background.radius = 0;
        }
    }
}

/*export {DishasMapChart};*/
