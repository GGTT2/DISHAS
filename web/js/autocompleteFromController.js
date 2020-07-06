/**
 * This function deals with every autocompleted fields EXCEPT the dynamically created ones --> see function addField(), in addField.js
 * It is based on jquery ui method .autocomplete().
 * For each form, one field is considered as necessary. This field is the main input, where autocomplete is performed. 
 * If autocomplete successes, the rest of the form doesn't show and the id of the retrieved object is given to controller through a hidden field; else, admin can add a new X.
 * There must be 2 cases of success :either the admin has selected one from list, or it submitted value is equivalent to one of the list - but he didn't click on it.
 * 
 * @param {type} entityName : name of the PHP object / form we are dealing with.
 * @param {type} route
 * @param {type} inputId
 * @returns {undefined}
 */
function myAutocomplete(formId, entityName, route, inputId) {

    function autocompleteSuccess(result) {
        if (input.parent().hasClass("has-success")) {
            autocompleteFailure();
        }
        input.addClass("form-control-success");
        input.parent().addClass("has-success");
        $("#add-" + entityName).attr("disabled", "disabled");
        $("#" + entityName + "-extraData").attr("hidden", "hidden").removeAttr("style");
        if (entityName == "translator") {//this is the only second-level autocomplete research
            $("#add_original_text_work_" + entityName + "_id").val(result.id);
        } else {
            //console.log(formId + "_" + entityName + "_id");
            $("#"+formId + "_" + entityName + "_id").val(result.id);
            //console.log($("#"+formId + "_" + entityName + "_id").val());

        }
        input.after("<div class='col-sm-12' id='found-" + entityName + "'><small class='green'>Found in database </small> </div>");
    }

    function autocompleteFailure() {
        input.parent().removeClass("has-success");
        input.removeClass("form-control-success");
        $("#found-" + entityName).remove();
        $("#add-" + entityName).removeAttr("disabled");
        $(formId + "_" + entityName + "_id").val(null);
    }

    var input = $(inputId);
    input.autocomplete({
        source: route,
        minLength: 1,
        select: function (event, ui) {
            autocompleteSuccess(ui.item);
        }
        ,
        search: function (event, ui) {
            $("#found-" + entityName).remove();
        },
        response: function (event, ui) {
            if (typeof ui.content[0] !== 'undefined') {

                if (input.val() === ui.content[0].value) {
                    autocompleteSuccess(ui.content[0]);
                } else {
                    autocompleteFailure();
                }
            } else {
                autocompleteFailure();
            }
        }
    });
    $("#add-" + entityName).on('click', function (e) {
        e.preventDefault();
        $("#" + entityName + "-extraData").toggle();
        $(this).attr('disabled', 'disabled');
    });

    input.on('change keyup copy paste cut', function () {
        if (!this.value) {
            autocompleteFailure();
            $("#add-" + entityName).attr("disabled", "disabled");
        }
    });
}
;

function autocomplete2Fields(formId, entityName, route, field1, field2) {
    function autocompleteSuccess(result) {
        if (input.parent().hasClass("has-success")) {
            autocompleteFailure();
        }
        var id = $(field1).parent().parent().parent().find('[id$=id]');
        input.addClass("form-control-success");
        $(field2).addClass("form-control-success");
        input.parent().addClass("has-success");
        $(field2).parent().addClass("has-success");
        $(id).val(result.id);
        $(field2).val(result.field2);
        $(field2).attr("readonly", "readonly");
        //input.after("<div class='col-sm-12' class='found'><small class='green'>Found in database </small> </div>");
    }

    function autocompleteFailure() {
        var id = $(field1).parent().parent().parent().find('[id$=id]');
        //var foundMessage = $(field1).parent().find(".found");
        input.parent().removeClass("has-success");
        $(field2).parent().removeClass("has-success");
        input.removeClass("form-control-success");
        $(field2).removeClass("form-control-success");
        //$(foundMessage).remove();
        id.val(null);
        $(field2).removeAttr("readonly");
        $(field2).val(null);
    }


    var input = $(field1);
    input.autocomplete({
        source: route,
        minLength: 1,
        select: function (event, ui) {
            autocompleteSuccess(ui.item);
        },
        search: function (event, ui) {
            $("#found-" + entityName).remove();
        },
        response: function (event, ui) {
            if (typeof ui.content[0] !== 'undefined') {

                if (input.val() === ui.content[0].value) {
                    autocompleteSuccess(ui.content[0]);
                } else {
                    autocompleteFailure();
                }
            } else {
                autocompleteFailure();
            }
        }
    });
    $("#add-" + entityName).on('click', function (e) {
        e.preventDefault();
        $("#" + entityName + "-extraData").toggle();
        $(this).attr('disabled', 'disabled');
    });

    input.on('change keyup copy paste cut', function () {
        if (!this.value) {
            autocompleteFailure();
            $("#add-" + entityName).attr("disabled", "disabled");
        }
    });
}


/**
 * Popup form to find ref in VIAF. If founded, it populates field from the main form where VIAF ID is to be reimplented. 
 * 
 * @returns {undefined} : new popup form to search in VIAF. 
 */
function openViaf() {
    dialog = $("#findInViaf").dialog({
        autoOpen: false,
        height: "auto",
        modal: true
    });

    $(".open-findInVIAF1").click(function (event) {
        event.preventDefault();
        dialog.dialog("open");
        myTrigger = this;
        $("#selectIdViaf").on("click", function (e) {
            e.preventDefault();
            var VIAFId = $("#VIAFId").val();
            var fieldToFill = $(myTrigger).parent().parent().find(".viaf");
            $(fieldToFill).val(VIAFId);
            dialog.dialog("close");
            $(this).off(); // We have to call off function on this button event because it is also used by a function from addField.js
        });
    });
}
;
