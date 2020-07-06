/* jshint esversion: 6 */

/**
 * This class defines properties for each entity that can be displayed
 * in an instance of a DishasChart as color and titles in tooltips
 */
class DishasEntity {
    constructor(){
        this.primarySource = new DishasPrimarySource();
        this.originalText = new DishasOriginalText();
        this.work = new DishasWork();
        this.editedText = new DishasEditedText();
        this.parameterSet = new DishasParameterSet();
        this.astronomicalObject = new DishasAstronomicalObject();
        this.mathematicalParameter = new DishasMathematicalParameter();
        this.formulaDefinition = new DishasFormulaDefinition();
        this.tableType = new DishasTableType();
        this.localisationParameter = new DishasLocalisationParameter();
    }

    static getEntity(entityName, property=""){
        let entityInfo = new DishasEntity();
        return property === "" ? entityInfo[entityName] : entityInfo[entityName][property];
    }
}

class DishasAbstractEntity {
    constructor(){
        /**
         * Name of the entity in camel case as formulated in the database model
         * @type {string}
         */
        this.entityName = "";
        /**
         * Name of the entity as formulated in the ObjectUserInterfaceName
         * from the Definition Table of the database
         * @type {string}
         */
        this.name = "";
        /**
         * Designation of the entity in the legend
         * @type {string}
         */
        this.legend = "";
        /**
         * Path to the record page of the entity
         * @type {string}
         */
        this.path = "";
        /**
         * Prefix put before the id of one of the record of the entity
         * In order to distinguish it from other entity ids
         * @type {string}
         */
        this.prefixId = "";
        /**
         * Designation of the entity in the tooltip of the heatmap
         * Of the chronomap
         * @type {string}
         */
        this.heatmapTooltip = "";
        /**
         * Property in the box dataset that is used to order boxes
         * @type {string}
         */
        this.sortProperty = "";
        /**
         * Array of information that are displayed in the record box of the entity
         * The elements of the array corresponds to the property names that contains
         * the information in the box dataset
         * Special case : "tpaq" corresponds in fact to two distinct properties in the box dataset
         * => "from" and "to" that are put combined afterwards
         * @type {*[]}
         */
        this.boxInfo = [];
    }
}

class DishasPrimarySource extends DishasAbstractEntity {
    constructor(){
        super();
        this.entityName = "primarySource";
        this.name = objects.primarySource.objectUserInterfaceName.capitalize();
        this.legend = this.name.replace(" ", "\n");
        this.path = "tamas_astro_viewPrimarySource";
        this.prefixId = "ps";
        this.heatmapTooltip = "Table";
        this.boxInfo = ["tpaq", "library", "place"];
        this.sortProperty = "from";
    }
}

class DishasOriginalText extends DishasAbstractEntity {
    constructor(){
        super();
        this.entityName = "originalText";
        this.name = objects.originalText.objectUserInterfaceName.capitalize();
        this.legend = this.name.replace(" ", "\n");
        this.path = "tamas_astro_viewOriginalText";
        this.prefixId = "oi";
        this.heatmapTooltip = this.name;
        this.boxInfo = ["tpaq", "shelfmark", "place"];
        this.sortProperty = "from";
    }
}

class DishasWork extends DishasAbstractEntity {
    constructor(){
        super();
        this.entityName = "work";
        this.name = objects.work.objectUserInterfaceName.capitalize();
        this.legend = this.name;
        this.path = "tamas_astro_viewWork";
        this.prefixId = "wo";
        this.heatmapTooltip = this.name;
        this.boxInfo = ["tpaq", "author", "place"];
        this.sortProperty = "from";
    }
}

class DishasEditedText extends DishasAbstractEntity {
    constructor(){
        super();
        this.entityName = "editedText";
        this.name = objects.editedText.objectUserInterfaceName.capitalize();
        this.legend = this.name.replace(" ", "\n");
        this.path = "tamas_astro_viewTableEdition";
        this.prefixId = "et";
        this.boxInfo = ["type", "author", "date"];
        this.sortProperty = "date";
    }
}

class DishasParameterSet extends DishasAbstractEntity {
    constructor(){
        super();
        this.entityName = "parameterSet";
        this.name = objects.parameterSet.objectUserInterfaceName.capitalize();
        this.legend = this.name.replace(" ", "\n");
        this.path = "tamas_astro_viewParameterSet";
        this.prefixId = "ap";
        this.boxInfo = ["format", "value", "unit"];
        this.sortProperty = "value";
    }
}

class DishasMathematicalParameter extends DishasAbstractEntity {
    constructor(){
        super();
        this.entityName = "mathematicalParameter";
        this.name = objects.mathematicalParameter.objectUserInterfaceName.capitalize();
        this.legend = this.name.replace(" ", "\n");
        this.prefixId = "mp";
        this.heatmapTooltip = this.name;
        this.boxInfo = ["type", "value", "number"];
        this.sortProperty = "value";
    }
}

class DishasLocalisationParameter extends DishasAbstractEntity {
    constructor(){
        super();
        this.entityName = "localisationParameter";
        this.name = objects.localisationParameter.objectUserInterfaceName.capitalize();
        this.legend = this.name.replace(" ", "\n");
        this.prefixId = "mm";
        this.heatmapTooltip = this.name;
        this.boxInfo = ["place", "long", "epoch"];
        this.sortProperty = "place";
    }
}

class DishasFormulaDefinition extends DishasAbstractEntity {
    constructor(){
        super();
        this.entityName = "formulaDefinition";
        this.name = objects.formulaDefinition.objectUserInterfaceName.capitalize();
        this.legend = this.name.replace(" ", "\n");
        this.prefixId = "fd";
        this.path = "tamas_astro_viewFormulaDefinition";
        this.boxInfo = ["tableType", "formula"];
        this.sortProperty = "tableType";
    }
}

class DishasTableType extends DishasAbstractEntity {
    constructor(){
        super();
        this.entityName = "tableType";
        this.name = objects.tableType.objectUserInterfaceName.capitalize();
        this.legend = this.name.replace(" ", "\n");
        this.prefixId = "tt";
        this.path = "tamas_astro_viewTableType";
        this.boxInfo = ["def"];
        this.sortProperty = "def";
    }
}

class DishasAstronomicalObject extends DishasAbstractEntity {
    constructor(){
        super();
        this.entityName = "astronomicalObject";
        this.name = objects.astronomicalObject.objectUserInterfaceName.capitalize();
        this.path = "tamas_astro_astronomicalObject";
        this.prefixId = "ao";
        this.boxInfo = ["def"];
        this.sortProperty = "def";
    }
}

/*export {DishasEntity};*/

