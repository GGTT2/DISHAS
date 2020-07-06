/* global $ */
/* global HotTable */
/* global SmartNumber */
/* global SmartNumberField */
/* global ForwardLinearInterpolation */
/* global BetweenLinearInterpolation */
/* global BetweenArgument */
/* global Diff1_vertical_classic */
/* global Diff1_vertical_reverse */
/* global Diff1_horizontal_classic */
/* global Diff1_horizontal_reverse */
/* global Diff1_classic */
/* global Diff1_reverse */
/* global Identity1Arg */
/* global Identity2Arg */
/* global Diff12Arg */
/* global Diff12ArgHorizontal */
/* global Diff11Arg */
/* global Diff22Arg */
/* global Diff22ArgHorizontal */
/* global Diff21Arg */
/* global resizeAll */

/**
 * Add a slot for an additional horizontal information table
 * @param {Number} horizontalIndex  Index of the new slot
 */
function addHorizontalInfoSlot(horizontalIndex) {
    $("#bottom-zone>tbody").append("<tr><td><div style=\"height: 30px; overflow: hidden;\"><div id=\"bottom-info-slot-" + horizontalIndex + "\" style=\"height: 40px; overflow: auto;\"></div></div></td></tr>");
}

/**
 * Add a slot for an additional vertical information table
 * @param {Number} verticalIndex  Index of the new slot
 */
function addVerticalInfoSlot(verticalIndex) {
    $("#middle-right-zone>tbody>tr").append("<td><div style=\"width: 100px; overflow: hidden;\"><div id=\"right-info-slot-" + verticalIndex + "\" style=\"overflow-y: auto;overflow-x: hidden\"></div></div></td>");
    $("#middle-right-zone>tbody>tr").append("<td><div id=\"right-info-slot-separator-" + verticalIndex + "\" style=\"width: 10px;\"></div></td>");
}

class DTITable extends HotTable {
    /**
     * Handsontable implementation of the main table, with bindings with 
     * the DOM elements of the interface, and
     * appropriate jquery. 
     * **This is the class currently used for the main table in the interface**
     * @param  {String}  containerId   DOM id of the div that will contain the table
     * @param  {Object}  keyBindings   Dictionary for key bindings. Put it to "standard" to use standard key bindings
     * @return {HotTable}
     */
    constructor(containerId, keyBindings) {
        super(containerId, keyBindings);
    }
    /**
     * Ensure that the scrolling of the side vertical information tables are synchronized with the scrolling of this main table
     * @return {undefined}
     */
    synchronizeVerticalScrollings() {
        var scroll = $("#"+this.containerId+" div.wtHolder").scrollTop();
        $("#"+this.containerId+" div.wtHolder").scrollTop(scroll);
        for(var i=0; i<10; i++) {
            $("#right-info-slot-"+i).scrollTop(scroll);
        }
    }
    /**
     * Ensure that the scrolling of the side horizontal information tables are synchronized with the scrolling of this main table
     * @return {undefined}
     */
    synchronizeHorizontalScrollings() {
        var scroll = $("#"+this.containerId+" div.wtHolder").scrollLeft();
        $("#"+this.containerId+" div.wtHolder").scrollLeft(scroll);
        for(var i=0; i<10; i++) {
            $("#bottom-info-slot-"+i).scrollLeft(scroll);
        }
    }
    /**
     * Change the string displayed in the log box
     * @param  {String} str  content of the log box
     * @return {undefined}
     */
    updateLog(str) {
        $("#textarea-logs").val(str);
    }
    /**
     * Return the DOM id of the horizontal slot with the specified index
     * @param  {Number} index  index of the horizontal slot
     * @return {undefined}
     */
    getHorizontalInfoId(index) {
        return "bottom-info-slot-"+index;
    }
    /**
     * Return the DOM id of the vertical slot with the specifed index
     * @param  {Number} index  index of the verticam slot
     * @return {undefined}
     */
    getVerticalInfoId(index) {
        return "right-info-slot-"+index;
    }
    /**
     * Give an appropriate size to the horizontal slot with the specified index
     * @param  {Number} index        index of the horizontal slot
     * @param  {Object} graphicSpec  graphic template of the information table
     * @return {undefined}
     */
    resizeHorizontalInfoDiv(index, graphicSpec) {
        $("#bottom-info-slot-"+index).height(45);
    }
    /**
     * Give an appropriate size to the vertical slot with the specified index
     * @param  {Number} index        index of the vertical slot
     * @param  {Object} graphicSpec  graphic template of the information table
     * @return {undefined}
     */
    resizeVerticalInfoDiv(index, graphicSpec) {
        $("#right-info-slot-"+index).width(105+Number(50*(graphicSpec.ncells)));
        $("#right-info-slot-"+index).parent().width(25+Number(50*(graphicSpec.ncells)));
    }
    /**
     * Empty the vertical slot with the specified index
     * @param  {Number} index  index of the vertical slot
     * @return {undefined}
     */
    removeVerticalInfoDiv(index) {
        $("#right-info-slot-"+index).html("");
        $("#right-info-slot-"+index).parent().width(0);
        $("#right-info-slot-separator-"+index).width(0);
    }
    /**
     * Empty the horizontal slot with the specified index
     * @param  {Number} index  index of the horizontal slot
     * @return {undefined}
     */
    removeHorizontalInfoDiv(index) {
        $("#bottom-info-slot-"+index).html("");
        $("#bottom-info-slot-"+index).parent().height(0);
    }
    /**
     * Create a header for the specfied vertical information table
     * @param  {Number} index  index of the vertical slot
     * @param  {String} name   title to write in the header
     * @return {undefined}
     */
    createVerticalInfoHeader(index, name) {
        if(name === undefined) {
            name = "INFO-"+index;
        }
        this.createVerticalInfoDiv(index);
        $("#vertical-title-"+index).append("<div class=\"tableheader\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" + name + "&nbsp;</div>");
        $("#vertical-info-"+index).append(this.buttonVerticalString(index));
    }
    /**
     * Fill the vertical slot of specifed index with 2 divs, one for the title, one for the table
     * @param  {Number} index  Index of the vertical slot
     * @return {undefined}
     */
    createVerticalInfoDiv(index) {
        $("#right-info-slot-"+index).prepend("<div id=\"vertical-title-"+index+"\" style=\"position:absolute; height:20px; float: left;overflow: auto; display:inline; z-index:12;\"></div>");
        $("#right-info-slot-"+index).prepend("<div id=\"vertical-info-"+index+"\" style=\"position:absolute; width:20px; float: left;overflow: auto; display:inline; z-index:14;\"></div>");
    }
    /**
     * Return a HTML String describing a button that when clicked will destroy the vertical table with the specified index
     * @param  {Number} index  index of the vertical slot
     * @return {String}
     */
    buttonVerticalString(index) {
        return "<button style=\"padding: 0px; z-index: 15;height:20px; width:20px; background:red;\" onclick=\"hots[0].removeVerticalInformationTable("+index+");\"><div style=\"line-height:0; font-size: 150%;\">&times;</div></button>";
    }
    /**
     * Create a header for the specified horizontal information table
     * @param  {Number} index  index of the horizontal slot
     * @param  {String} name   title to write in the header
     * @return {undefined}
     */
    createHorizontalInfoHeader(index, name) {
        if(name === undefined) {
            name = "INFO-" + index;
        }
        this.createHorizontalInfoDiv(index);
        $("#horizontal-title-"+index).append("<div class=\"tableheader\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" + name + "&nbsp;</div>");
        $("#horizontal-info-"+index).append(this.buttonHorizontalString(index));
    }
    /**
     * Fill the horizontal slot of specifed index with 2 divs, one for the title, one for the table
     * @param  {Number} index  Index of the horizontal slot
     * @return {undefined}
     */
    createHorizontalInfoDiv(index) {
        $("#bottom-info-slot-"+index).prepend("<div id=\"horizontal-title-"+index+"\" style=\"position:absolute; height:20px; float: left;overflow: auto; display:inline; z-index:12;\"></div>");
        $("#bottom-info-slot-"+index).prepend("<div id=\"horizontal-info-"+index+"\" style=\"position:absolute; width:20px; float: left;overflow: auto; display:inline; z-index:14;\"></div>");
    }
    /**
     * Return a HTML String describing a button that when clicked will destroy the horizontal table with the specified index
     * @param  {Number} index  index of the horizontal slot
     * @return {String}
     */
    buttonHorizontalString(index) {
        return "<button style=\"padding: 0px; z-index: 15;height:20px; width:20px; background:red;\" onclick=\"hots[0].removeHorizontalInformationTable("+index+");\"><div style=\"line-height:0; font-size: 150%;\">&times;</div></button>";
    }
    /**
     * Remove the specified vertical information table.
     * If this table is linked to a horizontal information table,
     * it will be destroyed too.
     * @param  {Number} index  index of the vertical slot containing the table
     * @return {undefined}
     */
    removeVerticalInformationTable(index) {
        var autotool = this.verticalInformationTables[index];
        if(autotool === undefined)
            return;
        if(autotool.linkedIndex !== undefined) {
            this.horizontalInformationTables[autotool.linkedIndex].linkedIndex = undefined;
            this.removeHorizontalInformationTable(autotool.linkedIndex);
        }
        this.verticalInformationTables[index].destroy();
        this.verticalInformationTables[index] = undefined;
        this.zones[0].verticalInformationTables[index] = undefined;
        //$('#'+this.toolBox['right_info_slot']+'-'+index+' div.wtHolder').html("");
        $("#right-info-slot-"+index).html("");
        $("#right-info-slot-"+index).parent().width(0);
        $("#right-info-slot-separator-"+index).width(0);
    }
    /**
     * Remove the specified horizontal information table.
     * If this table is linked to a vertical information table,
     * it will be destroyed too.
     * @param  {Number} index  index of the horizontal slot containing the table
     * @return {undefined}
     */
    removeHorizontalInformationTable(index) {
        var autotool = this.horizontalInformationTables[index];
        if(autotool.linkedIndex !== undefined) {
            this.verticalInformationTables[autotool.linkedIndex].linkedIndex = undefined;
            this.removeVerticalInformationTable(autotool.linkedIndex);
        }
        this.horizontalInformationTables[index].destroy();
        this.horizontalInformationTables[index] = undefined;
        this.zones[0].horizontalInformationTables[index] = undefined;
        $("#bottom-info-slot-"+index).html("");
        $("#bottom-info-slot-"+index).parent().height(0);
    }
    /**
     * Action to perform when the user switch of zone
     * (from main table to difference table, or the opposite)
     * @return {undefined}
     */
    onSwitchZone() {
        if(typeof this.zoneIndex === "undefined" || this.zoneIndex === 0) {
            $("#switch-icon").removeClass("switch2");
            $("#switch-icon").addClass("switch");
            $('#button-fill-model').removeClass("disabled");
            $('#button-fill-model').removeAttr("disabled");
            $('#button-fill-diff').addClass("disabled");
            $('#button-fill-diff').attr("disabled", "disabled");
            
            $("#button-fill").attr("data-target", "#dialog-fill");
        }
        else {
            $("#switch-icon").removeClass("switch");
            $("#switch-icon").addClass("switch2");
            $('#button-fill-diff').removeClass("disabled");
            $('#button-fill-diff').removeAttr("disabled");
            $('#button-fill-model').addClass("disabled");
            $('#button-fill-model').attr("disabled", "disabled");
        
            if(this.nargs === 1) {
                $("#button-fill").attr("data-target", "#dialog-fill-diff-1arg");
            }
            else {
                $("#button-fill").attr("data-target", "#dialog-fill-diff-2arg");
            }
        }
    }
    /**
     * Apply new handsontable settings
     * @param  {Object} settings  a dictionary of settings as needed by the handsontable library
     * @return {undefined}
     */
    updateSettings(settings) {
        this.hot.updateSettings(settings);
    }
    /**
     * Select the appropriate tab to display in the console box,
     * base on the selected cells and some of their properties
     * @param  {SuperCell} SuperCell  the currently selected \ :js:attr:`SuperCell`\ 
     * @param  {Array} MetaCells      the list of selected \ :js:attr:`MetaCell`\ s
     * @return {undefined}
     */
    showCommentary(SuperCell, MetaCells) {
        $("#textarea-commentary").val(SuperCell.props.user_comment);
        $("#textarea-commentary").removeAttr("readonly");
        $("#textarea-logs").val(SuperCell.log());
        if(MetaCells.length === 1) {
            var metacell = MetaCells[0];
            $('#textarea-critical-apparatus').val(metacell.props.critical_apparatus);
            if(metacell.props.critical_apparatus !== "") {
                $('#tab-critical').click();
                return ;
            }
        }
        if(SuperCell.props.user_comment !== "")
            $("#tab-commentary").click();
        else
            $("#tab-logs").click();
    }
    /**
     * Focus the log tab in the console box
     * @return {undefined}
     */
    focusLog() {
        $("#textarea-commentary").val("");
        $("#textarea-logs").val("");
        $("#textarea-commentary").attr("readonly", true);
        $("#tab-logs").click();
    }
    /**
     * Focus the cell commentary area
     * @return {undefined}
     */
    focusCommentary() {
        $("#tab-commentary").click();
        $("#textarea-commentary").focus();
    }
    /**
     * Show a graph of the current table
     * (histogram in case of 1-arg tables, heat map in case of 2-arg tables)
     * @return {undefined}
     */
    showGraph() {
        this.fillGraph("graph");
        $("#dialog-graph").dialog("open");
    }
    /**
     * Read the mathematical parameters from the form fields,
     * and perform the necessary updates (change the values in the predict
     * with model interface)
     * @return {undefined}
     */
    updateMathematicalParameters() {
        var res = {entryDisplacementFloat: 0.0, argumentDisplacementFloat: 0.0, entryShift: 0, argumentShift: 0};
        var dataJson = $("#tamas_astrobundle_tablecontent_mathematicalParameter").find(":selected").attr("data-json");
        if(dataJson !== undefined) {
            res = JSON.parse(dataJson);
            for(var key in res) {
                if(res[key] === undefined || res[key] === null)
                    res[key] = 0.0;
            }
        }
        this.mathematicalParameters = res;
        this.transferParameters();
    }
    /**
     * Read the astronomical parameters from the form fields,
     * and perform the necessary updates (change the values in the predict
     * with model interface)
     * @return {undefined}
     */
    updateAstronomicalParameters() {
        var res = {};
        var selectedSets = $('#tamas_astrobundle_tablecontent_parameterSets').val();
        if(selectedSets !== null && selectedSets !== undefined) {
            $('#tamas_astrobundle_tablecontent_parameterSets option').each(function(){
                if(selectedSets.indexOf(String($(this).attr('data-id'))) >= 0) {
                    var dataJson = JSON.parse($(this).attr('data-json'));
                    for(var param in dataJson) {
                        res[param] = dataJson[param];
                    }
                }
            });
        }
        this.astronomicalParameters = res;
        this.transferParameters();
    }
    /**
     * Transfer the current table parameters to the predict with model
     * interface fields
     * @return {undefined}
     */
    transferParameters() {
        if(this.model === undefined)
            return;
        var res = this.mathematicalParameters;
        if(res.entryDisplacementFloat !== undefined && 'C0' in this.parameter_snfs) {
            this.parameter_snfs['C0'].fill(new SmartNumber(res.entryDisplacementFloat));
            this.parameter_snfs['C0'].input.change();
        }
        if(res.argumentDisplacementFloat !== undefined && 'Cx' in this.parameter_snfs) {
            this.parameter_snfs['Cx'].fill(new SmartNumber(res.argumentDisplacementFloat));
            this.parameter_snfs['Cx'].input.change();
        }
        if(res.argument2DisplacementFloat !== undefined && 'Cy' in this.parameter_snfs) {
            this.parameter_snfs['Cy'].fill(new SmartNumber(res.argument2DisplacementFloat));
            this.parameter_snfs['Cy'].input.change();
        }
        for(var param in this.astronomicalParameters) {
            this.parameter_snfs['o' + param].fill(new SmartNumber(this.astronomicalParameters[param]))
            this.parameter_snfs['o' + param].input.change();
        }
    }
    /**
     * Transfer the parameters of the specified model to the predict
     * with model interface fields.
     * @param  {ParametricFunction} model
     * @return {undefined}
     */
    parametersToFields(model) {
        if(model === undefined)
            var model = this.model;
        for(var param in model.parameters) {
            this.parameter_snfs[param].fill(
                new SmartNumber(Number(model.parameters[param].value)));
        }
    }
    /**
     * Action to perform when a new model is selected.
     * 
     * @param  {ParametricFunction} model  new selected model
     * @param  {Boolean} transfer          whether to transfer the parameter values of the new model to the form fields
     * @return {undefined}
     */
    onNewModel(model, transfer) {
        if(transfer === undefined)
            transfer = true;
        this.parameter_snfs = {};
        if(model === undefined)
            return;
        this.model = model;
        var hot = this;

        $("#parameter-list").html("");
        $("#model-description").html("<u>" + hot.model.name + "</u>");
        $("#model-description-wip").html("");

        for(var param in hot.model.parameters) {
            if(param[0] !== 't') {
                var parameter = hot.model.parameters[param];
            	var check_attribute = "";
            	if(parameter.locked)
            		check_attribute = 'checked="true"';
                var buffer = "<tr>";
                var style = "";
                buffer += `
                <td><label>` + parameter.latexName+`</label></td>
                <td>
    	            <input style="` + style + `" class="form-control numbers" type="text" 
    	            id="parameter-`+parameter.name+`_SmartNumber">
    	            <input style="` + style + `" class="form-control numbers" type="hidden" 
    	            id="parameter-`+parameter.name+`">
                </td>
                <td>
    	            <select id="parameter_typeOfNumber_` + param + `" name="tamas_astrobundle_parametervalue[typeOfNumber]" 
                class="form-control">
    	            	<option value="sexagesimal">sexagesimal</option>
    		            <option value="floating_sexagesimal">floating sexagesimal</option>
    		            <option value="historical">historical</option>
    		            <option value="integer_and_sexagesimal">integer and sexagesimal</option>
    		            <option value="historical_decimal">historical decimal</option>
    		            <option value="temporal">temporal</option>
    		            <option value="decimal" selected>decimal</option>
    	            </select>
    	        </td>
                <td><input style=" `+ style + `" class=""`+check_attribute+`
                 type="checkbox" id="parameter-locked-`+parameter.name+`">
                </td>
                 </tr>`;
                if(parameter.linkedParameter !== undefined) {
                    buffer += "<tr><td style=\"border-top: 0px;\"><label>" + parameter.linkedParameter.latexName+"</label></td><td style=\"border-top: 0px;\"><input style=\"" + style + "\" class=\"form-control numbers\" type=\"text\" id=\"parameter-"+parameter.linkedParameter.name+"_SmartNumber\">"
                    buffer += "<input style=\"" + style + "\" class=\"form-control numbers\" type=\"hidden\" id=\"parameter-"+parameter.linkedParameter.name+"\"></td></tr>";
                }
                $("#parameter-list").append(buffer);

                (function() {
                    var local_param = param;
                    var parameter = hot.model.parameters[param];
                    $("#parameter-locked-"+parameter.name).change(function() {
                        var checked = $("#parameter-locked-"+parameter.name).is(":checked");
                        parameter.locked = checked;
                        if(parameter.linkedParameter !== undefined) {
                            parameter.linkedParameter.locked = checked;
                        }
                    });

                    var snf = new SmartNumberField("parameter-"+parameter.name+"_SmartNumber", undefined, undefined, "decimal");
                    hot.parameter_snfs[parameter.name] = snf;
                    snf.bindSelect("parameter_typeOfNumber_" + local_param);
                    if(parameter.linkedParameter !== undefined) {
                        var linkedSnf = new SmartNumberField("parameter-"+parameter.linkedParameter.name+"_SmartNumber", undefined, undefined, "decimal");
                        hot.parameter_snfs[parameter.linkedParameter.name] = linkedSnf;
                        linkedSnf.bindSelect("parameter_typeOfNumber_" + local_param);
                        snf.onUpdate(function(that) {
                            linkedSnf.fill(new SmartNumber(parameter.transform(that.smartNumber.computeDecimal())));
                        });
                        linkedSnf.onUpdate(function(that) {
                            snf.fill(new SmartNumber(parameter.linkedParameter.transform(that.smartNumber.computeDecimal())));
                        });
                    }
                    if(parameter !== undefined) {
                        snf.bindTarget("parameter-"+parameter.name, "float");
                        snf.fill(new SmartNumber(parameter.value));
                    }
                    $("#parameter-locked-"+parameter.name).val(parameter.locked);
                    if(parameter.linkedParameter !== undefined) {
                        linkedSnf.bindTarget("parameter-"+parameter.linkedParameter.name, "float");
                        linkedSnf.fill(new SmartNumber(parameter.linkedParameter.value));
                    }
                })();
            }
        }
        for(var param in hot.model.parameters) {
            (function() {
                var local_param = param;
                var parameter = hot.model.parameters[param];
                $("#parameter-"+parameter.name).change(function() {
                    hot.model.setParameterValue(local_param, Number($("#parameter-"+parameter.name).val()));
                    if(parameter.linkedParameter !== undefined) {
                        $("#parameter-"+parameter.linkedParameter.name).val(parameter.linkedParameter.value);
                    }
                });
            })();
        }
        if(transfer)
            this.transferParameters();
    }
    /**
     * Binds several DOM ids to actions of this class
     * @return {undefined}
     */
    interfaceBindings() {
        var hot = this;

        /* ===================
               COMMENTARY
        =====================*/
        
        $("#textarea-commentary").val("");
        $("#textarea-commentary").attr("readonly", true);
        
        // Edit the commentary of the selected supercell with the
        // content of the textarea-commentary box.
        $("#textarea-commentary").change( function() {
            var cells = hot.selectionToSuperCells();
            if(cells.length === 1) {
                cells[0].setProp("user_comment", $("#textarea-commentary").val());
            }
        });
        // Put a grey div on the screen and highlight the commentary
        // box and the target cells
        $("#textarea-commentary").focusin( function() {
            var cells = hot.selectionToSuperCells();
            if(cells.length === 1) {
                cells[0].setProp("commentary_zone", true);
                hot.deselectCell();
                
                $("#grey-bg").css("z-index","10");
                $("#bottom-right-zone").css("z-index","15");
            }
            hot.commentaryFocused = true;
            hot.render();
        });
        // Remove commentary highlight
        $("#textarea-commentary").focusout( function() {
            var cells = hot.selectionToSuperCells();
            if(cells.length === 1) {
                cells[0].setProp("commentary_zone", false);
                cells[0].setProp("user_comment", ($("#textarea-commentary").val()));
                $("#grey-bg").css("z-index","-10");
                $("#bottom-right-zone").css("z-index","0");
            }
            hot.commentaryFocused = false;
            hot.render();
        });
        // Shortcut for quitting commentary
        $("#textarea-commentary").keydown( function(param) {
            if(param.key === "Escape") {
                $("#textarea-commentary").focusout();
                hot.refocus();
            }
        });

        /* ===================
               APPARATUS
        =====================*/

        // Edit the critical apparatus of the selected cell with
        // the content of the textarea-critical-apratus box
        $("#textarea-critical-apparatus").change( function() {
            var cells = hot.selectionToMetaCells();
            if(cells.length === 1) {
                cells[0].setProp("critical_apparatus", $("#textarea-critical-apparatus").val());
            }
        });
        // Put a grey div on the screen and highlight the apparatus
        // box and the target cells
        $("#textarea-critical-apparatus").focusin( function() {
            var cells = hot.selectionToMetaCells();
            if(cells.length === 1) {
                cells[0].setProp("apparatus_zone", true);
                hot.deselectCell();
                
                $("#grey-bg").css("z-index","10");
                $("#bottom-right-zone").css("z-index","15");
            }
            hot.criticalFocused = true;
            hot.render();
        });
        // Remove apparatus highlight
        $("#textarea-critical-apparatus").focusout( function() {
            var cells = hot.selectionToMetaCells();
            if(cells.length === 1) {
                cells[0].setProp("apparatus_zone", false);
                cells[0].setProp("critical_apparatus", $("#textarea-critical-apparatus").val());
                $("#grey-bg").css("z-index","-10");
                $("#bottom-right-zone").css("z-index","0");
            }
            hot.criticalFocused = false;
            hot.render();
        });
        // Shortcut for quitting critical apparatus
        $("#textarea-critical-apparatus").keydown( function(param) {
            if(param.key === "Escape") {
                $("#textarea-critical-apparatus").focusout();
                hot.refocus();
            }
        });

        /* ==============================
                       TOOLS
        ============================== */

        $(".button-validate").click( function() {
            hot.refocus();
            hot.validateSelection();
        });
        
        $(".button-forward").mouseenter( function () {
            hot.previewTool(ForwardLinearInterpolation);
            hot.render();
        });
        $(".button-forward").mouseleave( function () {
            hot.cleanPreview(ForwardLinearInterpolation, true);
            hot.render();
        });
        $(".button-forward").click( function() {
            hot.cleanPreview(ForwardLinearInterpolation, true);
            hot.refocus();
            hot.activateTool(ForwardLinearInterpolation);
            hot.moveDown();
            hot.previewTool(ForwardLinearInterpolation);
            hot.render();
        });
        
        $(".button-between").mouseenter( function () {
            hot.previewTool(BetweenLinearInterpolation);
            hot.previewTool(BetweenArgument);
            hot.render();
        });
        $(".button-between").mouseleave( function () {
            hot.cleanPreview(BetweenLinearInterpolation);
            hot.cleanPreview(BetweenArgument);
            hot.render();
        });
        $(".button-between").click( function() {
            hot.refocus();
            hot.activateTool(BetweenLinearInterpolation);
            hot.activateTool(BetweenArgument);
            hot.render();
        });
        
        $(".button-super").click( function() {
            hot.refocus();
            hot.superSelect();
        });
        
        $(".button-line").click( function() {
            hot.refocus();
            hot.lineSelect();
        });
        
        $(".button-column").click( function() {
            hot.refocus();
            hot.columnSelect();
        });
        
        $(".button-commentary").click( function() {
            hot.focusCommentary();
        });
        
        $(".button-graph").click( function() {
            hot.showGraph();
        });
        
        $(".button-undo").click( function() {
            hot.undo();
            hot.render();
        });
        
        $(".button-redo").click( function() {
            hot.redo();
            hot.render();
        });
        
        $(".button-switch").click( function() {
            hot.switchZone();
            switchTable(); //see this function in src/TAMAS/AstroBundle/Resources/views/DishasTableInterface/js/nav.html.twig
        });

        $("#fill-lsq").click(function() {
            hot.performLSQ();
        });

        /* ================================
                AUTOFILL for DIFFERENCES
        ================================ */
        
        $("#fill-diff-2arg-done").click( function() {
            switch($("#fill-diff-2arg-method").val().trim()) {
            case "vertical-classic":
                Diff1_vertical_classic.activateTool([hot.zones[0], hot.zones[hot.zoneIndex]], []);
                hot.render();
                break;
            case "vertical-reversed":
                Diff1_vertical_reverse.activateTool([hot.zones[0], hot.zones[hot.zoneIndex]], []);
                hot.render();
                break;
            case "horizontal-classic":
                Diff1_horizontal_classic.activateTool([hot.zones[0], hot.zones[hot.zoneIndex]], []);
                hot.render();
                break;
            case "horizontal-reversed":
                Diff1_horizontal_reverse.activateTool([hot.zones[0], hot.zones[hot.zoneIndex]], []);
                hot.render();
                break;
            }
        });
        
        $("#fill-diff-1arg-done").click( function() {
            switch($("#fill-diff-1arg-method").val().trim()) {
            case "classic":
                Diff1_classic.activateTool([hot.zones[0], hot.zones[hot.zoneIndex]], []);
                hot.render();
                break;
            case "reversed":
                Diff1_reverse.activateTool([hot.zones[0], hot.zones[hot.zoneIndex]], []);
                hot.render();
                break;
            }
        });

        /* ================================
               INFORMATION TABLES
        ================================ */

        function createInfoFromSelect(selector) {
            //var format = $("#"+hot.toolBox["create_info"]["create_info_format"]).val();
            var format = hot.zones[0].spec.entries[0].type;
            var toolName = $(selector).val();
            var verticalIndex = hot.verticalInformationTables.length;
            var horizontalIndex = hot.horizontalInformationTables.length;
            
            var tool1 = undefined;
            var tool2 = undefined;
            var json_data = undefined;
            switch(toolName) {
            case "identity":
                if(hot.nargs === 2) {
                    var tool1 = Identity2Arg;
                    var tool2 = Identity2Arg;
                }
                else {
                    var tool1 = Identity1Arg;
                }
                break;
            case "diff1":
                if(hot.nargs === 2) {
                    var tool1 = Diff12Arg;
                    var tool2 = Diff12ArgHorizontal;
                }
                else {
                    var tool1 = Diff11Arg;
                }
                break;
            case "diff2":
                if(hot.nargs === 2) {
                    var tool1 = Diff22Arg;
                    var tool2 = Diff22ArgHorizontal;
                }
                else {
                    var tool1 = Diff21Arg;
                }
                break;
            default:
                if(toolName in editions) {
                    var tool1 = undefined;
                    var tool2 = undefined;
                    var json_data = editions[toolName].json;
                    break;
                }
                else
                    return;
            }

            if(json_data === undefined) {
                var verticalGraphicSpec = table.zones[0].spec;
                var horizontalGraphicSpec = table.zones[0].spec;
            }
            else {
                var verticalGraphicSpec = json_data.template;
                var horizontalGraphicSpec = json_data.template;
            }

            addVerticalInfoSlot(verticalIndex);
            addHorizontalInfoSlot(horizontalIndex);
            
            if(hot.nargs === 2) {
                hot.createLinkedComputation(verticalIndex,horizontalIndex,tool1,tool2,verticalGraphicSpec,horizontalGraphicSpec,toolName,json_data);
            }
            else {
                hot.createVerticalComputation(verticalIndex, tool1, verticalGraphicSpec,toolName,json_data);
            }

            resizeAll();

            hot.touchAllValidated();
            
            hot.updateInfos();
            //hot.renderInfos();
            hot.synchronizeVerticalScrollings();
            hot.synchronizeHorizontalScrollings();
            
            /* hack for AJ's presentation */
            var coordinates = hot.getSelectedCell();
            var xcell = coordinates[0];
            var ycell = coordinates[1];
            if(hot.selectedMetaZone.superGrid[xcell][ycell] === null) {
                xcell += 1;
                ycell += 1;
            }
            if(!hot.selectedMetaZone.superGrid[xcell][ycell].isEntry()) {
                ycell = hot.selectedMetaZone.spec.args[0].ncells;
                if(!hot.selectedMetaZone.superGrid[xcell][ycell].isEntry()) {
                    xcell += 1;
                }
            }
            hot.selectCell(xcell, ycell);
            
            hot.render();
        }
        
        $("#create-info-done").click( function() {
            createInfoFromSelect("#create-info-method");
        });

        $("#comparison-info-done").click( function() {
            createInfoFromSelect("#compare-table-list");
        });
        
        function updateInformations() {
            if(hot.zones !== undefined)
                hot.touchAllValidated();
        }
        
        updateInformations();

        /* ===============================
        =============================== */
        
        // Link a change of parameters to the update of parameters
        // in this table
        $("#tamas_astrobundle_tablecontent_mathematicalParameter").change( function() {
            hot.updateMathematicalParameters();
        });
        $("#apply-parameter").click( function(e) {
            hot.updateMathematicalParameters();
            hot.updateAstronomicalParameters();
        });
        this.updateMathematicalParameters();

        $('#tamas_astrobundle_tablecontent_parameterSets').change(function(){
            hot.updateAstronomicalParameters();
        });
        this.updateAstronomicalParameters();

        // intialize some interface buttons to the right
        // enabled/disabled state
        $("#switch-icon").removeClass("switch2");
        $("#switch-icon").addClass("switch");
        $('#button-fill-model').removeClass("disabled");
        $('#button-fill-model').removeAttr("disabled");
        $('#button-fill-diff').addClass("disabled");
        $('#button-fill-diff').attr("disabled", "disabled");
    }
}
