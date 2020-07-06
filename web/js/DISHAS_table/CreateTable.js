/* global hots */
/* global $ */
/* global commentaryBinding */
/* global Table1ArgZone */
/* global Table2ArgZone */
/* global Diff11Arg */
/* global Diff21Arg */
/* global Diff12Arg */
/* global Diff22Arg */
/* global Diff12ArgHorizontal */
/* global Diff22ArgHorizontal */
/* global DTITable */

/**
 * Contains all the table created. Since we never have 2 handsontables (DISHAS_table) at the same
 * time, this array contains at max 1 element.
 * 
 * @type {Array}
 */
hots = [];

// Bind some actions to the release of some keys 
// (because handsontable does not provide bindings on keyup events)
$(this).keyup( function(param) {
    for(var i=0; i<hots.length; i++) {
        var hot = hots[i];
        if(!hot.isActive())
            continue;
        if(hot.toolsKeyBindings[param.originalEvent.code] !== undefined) {
            hot.keyUp(param.originalEvent.code);
        }
        if(param.key === commentaryBinding  && !param.originalEvent.shiftKey && !param.originalEvent.ctrlKey) {
            if(hot.focusCommentary !== undefined)
                hot.focusCommentary();
        }
    }
});

/**
 * Use this function when creating the main table for the first time.
 * Since we do not create a new table when the user modify the template
 * but only reshape the current one, this function is called only once.
 * 
 * @param  {Object} spec         template of the table
 * @param  {String} containerId  the id of the DOM object containing the table
 * @return {undefined}
 */
function createHotTable(spec, containerId) {
    // if the table is a 2-argument table, we must compute the width of the
    // super-columns (max between the second argument and the entry)
    if (spec.args.length === 2)
        spec.cwidth = Math.max(spec.args[1].ncells, spec.entries[0].ncells);
    
    // actually instantiate the table
    var table = new DTITable(containerId, "standard");
    
    // create the main zone from a standard layout
    var selectedMetaZone;
    if (spec.args.length === 1)
        selectedMetaZone = new Table1ArgZone(spec, {hot: table});
    else
        selectedMetaZone = new Table2ArgZone(spec, {hot: table});
    table.createFromZone(selectedMetaZone);

    // When changing an argument of the selectedMetaZone, we want to change
    // the arguments of all the zones of the table
    selectedMetaZone.linkedArgumentZones = table.zones;
    
    // We add as many zones as needed
    var prevSpec = spec;
    // if "spec.entries.length = 2", meaning there is a difference table, this loop is executed
    for (var i=1; i<spec.entries.length; i++) {
        // duplicate the specifications
        var newSpec = JSON.parse(JSON.stringify(prevSpec));
        // reverse the order of the entry objects (apparently)
        newSpec.entries = newSpec.entries.splice(1).concat(newSpec.entries);

        var zone;
        if (newSpec.args.length === 1)
            zone = new Table1ArgZone(newSpec, {hot: table, secondary: true});
        else
            zone = new Table2ArgZone(newSpec, {hot: table, secondary: true});
        zone.linkedArgumentZones = table.zones;
        table.addZone(zone);
        prevSpec = newSpec;
    }
    
    // We create "auto computations" for the table.
    // The differences are always computed automatically
    // and are stored in a "virtual zone".
    // (The information table will then read those zones and display them)
    table.selectedMetaZone.mathZones = table.mathZones;
    if (table.nargs === 1) {
        table.createMathZone("diff1", Diff11Arg);
        table.createMathZone("diff2", Diff21Arg);
    } else {
        table.createMathZone("diff1", Diff12Arg);
        table.createMathZone("diff2", Diff22Arg);
        table.createMathZone("diff1_horizontal", Diff12ArgHorizontal);
        table.createMathZone("diff2_horizontal", Diff22ArgHorizontal);
    }
    
    hots[0]=(table);
    table.typeChanged(table.selectedMetaZone.spec.table_type);
    return table;
}

/**
 * Use this function when reshaping the current existing table with
 * the spec (template) provided
 * @param  {Object} spec   template of the table
 * @param  {Number} index  OBSOLETE. Index of the table in the hots global variable. Since we create only one handsontable, this index is always 0.
 * @return {undefined}
 */
function respecHotTable(spec, index) {
    // Switch back to the main zone
    hots[0].switchZone(0);
    
    // actually index is always equal to 0
    if(index === undefined)
        var index = 0;

    // if the table is a 2-argument table, we must compute the width of the
    // super-columns (max between the second argument and the entry)
    if(spec.args.length === 2)
        spec.cwidth = Math.max(spec.args[1].ncells, spec.entries[0].ncells);

    var table = hots[index];
    
    // get the old spec (template) of the table    
    var originalJSON = table.selectedMetaZone.asOriginalJSON();
    var oldSpec = table.selectedMetaZone.spec;
    
    if(oldSpec.entries.length === 2) {
        var differenceOriginalJSON = table.zones[1].asOriginalJSON();
        var diffOldSpec = table.zones[1].spec;
    }
    
    // create the main zone from a standard layout
    if(spec.args.length === 1)
        var selectedMetaZone = new Table1ArgZone(spec, {hot: table});
    else
        var selectedMetaZone = new Table2ArgZone(spec, {hot: table});
    
    // remove all previous auto-tools
    for(var i in table.verticalInformationTables) {
        table.removeVerticalInformationTable(i);
    }

    // reset the undo/redo queue
    table.redoQueue = [];
    table.snapshotQueue = [];

    // destroy the several zones
    for(var i in table.zones) {
        table.zones[i].destroy();
        delete table.zones[i];
    }
    table.zones = [];
    if(table.selectedMetaZone.destroy !== undefined)
        table.selectedMetaZone.destroy();
    table.selectedMetaZone = undefined;
    
    // rebuild the table from the new selectedMetaZone
    table.createFromZone(selectedMetaZone);
    
    // When changing an argument of the selectedMetaZone, we want to change
    // the arguments of all the zones of the table
    selectedMetaZone.linkedArgumentZones = table.zones;
    
    // add as many zones as needed
    var prevSpec = spec;
    for(var i=1; i<spec.entries.length; i++) {
        var newSpec = JSON.parse(JSON.stringify(prevSpec));
        newSpec.entries = newSpec.entries.splice(1).concat(newSpec.entries);
        if(newSpec.args.length === 1)
            var zone = new Table1ArgZone(newSpec, {hot: table, secondary: true});
        else
            var zone = new Table2ArgZone(newSpec, {hot: table, secondary: true});
        zone.linkedArgumentZones = table.zones;
        table.addZone(zone);
        prevSpec = newSpec;
    }

    // Remove previous mathZones
    for(var name in table.mathZones) {
        table.mathZones[name].destroy();
    }
    table.mathZones = {};
    
    // We create "auto computations" for the table.
    // The differences are always computed automatically
    // and are stored in a "virtual zone".
    // (The information table will then read those zones and display them)
    table.selectedMetaZone.mathZones = table.mathZones;
    if(table.nargs === 1) {
        table.createMathZone("diff1", Diff11Arg);
        table.createMathZone("diff2", Diff21Arg);
    }
    else {
        table.createMathZone("diff1", Diff12Arg);
        table.createMathZone("diff2", Diff22Arg);
        table.createMathZone("diff1_horizontal", Diff12ArgHorizontal);
        table.createMathZone("diff2_horizontal", Diff22ArgHorizontal);
    }
    
    hots[0]=(table);

    // if the new table has the same number of arguments than the previous one,
    // we can load the previous JSON into the new table
    if(spec.args.length === oldSpec.args.length) {
        table.selectedMetaZone.fromOriginalJSON(originalJSON, oldSpec);
        if(spec.entries.length === 2 && oldSpec.entries.length === 2) {
            table.zones[1].fromOriginalJSON(differenceOriginalJSON, diffOldSpec);
        }
    }
    table.render();
    table.typeChanged(table.selectedMetaZone.spec.table_type);
    return table;
}




