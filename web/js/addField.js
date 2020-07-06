
/**
 *  This function allows to add as many fields as needed in array collections
 *  It also takes care of adding autocompletion to these fields and to the subfields : 
 *  - geocomplete
 *  - auto-selection of the VIAF ID
 * @param {type} div_id
 * @param {type} buton_id
 * @param {type} label
 * @param {type} route
 * @returns {undefined}
 */
var addField = function (div_id, buton_id, label, route) {
    var container = $('div' + div_id);              //defining the main container : the div containing the prototype
    var index = container.find(':input').length;    //counting number of inputs (pre-generated)
    $(buton_id).click(function (e) {
        addNewField(container, route);              //add a new section ; route is compulsory for autocomplete function
        e.preventDefault();
        return false;
    });
    if (index == 0) {
        addNewField(container, route);              //if no field was pre-generated (showing stored entity from DB), we create a first one
    } else {
        container.children('div').each(function () {//else we add buttons addDeleteLink and addFindinViaf
            addDeleteLink($(this));
            addFindInVIAF($(this));

        });
    }

    /**
     * Main function; allows to focus on the created field.
     * 
     * @param {type} container
     * @param {type} route
     * @returns {addField.addNewField}
     */
    function addNewField(container, route) {
        var template = container.attr('data-prototype')
                .replace(/__name__label__/g, label + ' n°' + (index + 1))
                .replace(/__name__/g, index)
                ;
        var prototype = $(template);
        addDeleteLink(prototype);
        addFindInVIAF(prototype);
        container.append(prototype);

        /*____________________________________________ Autocomplete Actor Name _________*/

        /**
         * This block is roughly equivalent to myAutocomplete() function, except that it deals with dynamically generated fields. 
         * Instead of being able to designate fields by id, we have to designate them by relative DOM info 
         * Furthermore, passing field as argument doesn't work: every fields that have been activated by the function react ; we need to declare global variable for the sake of the dynamically increment fields
         * 
         */
        var myNewField = $("#add_original_text_work_historicalActor").find($("[id$=actorName]"));

        /**
         * Autocmplete Success : used by autocomplete function in case of - correct selected item / - correct entered string
         * 
         * @param {type} result
         * @returns {undefined}
         */
        function autocompleteSuccess(result) {
            if (myField.parent().hasClass("has-success")) {
                autocompleteFailure();
            }
            var add = myField.parent().parent().find($(".add-work_historicalActors"));
            var ID = myField.parent().parent().find($("[id$=id"));
            var extraData = myField.parent().parent().find($("[class$=extraData]"));
            myField.addClass("form-control-success");
            myField.parent().addClass("has-success");
            $(add).attr("disabled", "disabled");
            $(ID).val(result.id);
            $(extraData).attr("hidden", "hidden").removeAttr("style");
            myField.after("<div class='col-sm-12 foundInDB'><small class='green'>Found in database </small> </div>");
            //console.log(result.id);

        }

        /**
         * Autocomplete failure : used by autocomplete function in case of failure : enables to click on add new X and open hidden div
         * 
         * @returns {undefined}
         */
        function autocompleteFailure() {
            var add = myField.parent().parent().find($(".add-work_historicalActors"));
            var ID = myField.parent().parent().find($("[id$=id"));
            myField.parent().removeClass("has-success");
            myField.removeClass("form-control-success");
            myField.next().remove();
            $(add).removeAttr("disabled");
            $(ID).val(null);
        }

        /*_______________________________Jquery autocomplete___________________________*/
        $(myNewField).autocomplete(
                {
                    source: route,
                    minLength: 1,
                    select: function (event, ui) {
                        myField = $(this); //We need to declare a global variable here in order to disambiguate created fields in the process. Otherwise, every fields react to the autocomplete function at once.
                        autocompleteSuccess(ui.item);
                    },
                    search: function (event, ui) {
                        myField = $(this);
                        $(this).next().remove();
                    },
                    response: function (event, ui) {
                        myField = $(this);
                        if (typeof ui.content[0] !== 'undefined') {

                            if ($(this).val() === ui.content[0].value) {
                                autocompleteSuccess(ui.content[0]);
                            } else {
                                autocompleteFailure();
                            }
                        } else {
                            autocompleteFailure();
                        }
                    }
                }
        );

        $(myNewField).on('change keyup copy paste cut', function () {
            if (!this.value) {
                autocompleteFailure();
                var add = myField.parent().parent().find($(".add-work_historicalActors"));
                $(add).attr("disabled", "disabled");
            }
        });

        /*___________________________________ Autocomplete Geographical name __________*/

        var myGeocompletedField = $(myNewField).parent().parent().find('.geoName');
        $(myGeocompletedField).on('focus', function () {
            //console.log(this);
            myGeocomplete(this);
        }
        );


        /*____________________________________ Add Viaf ID ____________________________*/
        myNewFindInViaf = $("#add_original_text_work_historicalActor").find($(".open-findInVIAF"));
        $(myNewFindInViaf).click(function (event) {
            event.preventDefault();
            fillVIAF();
            var findInViafButton = this;
            fieldToFill = $(findInViafButton).parent().parent().parent().find(".viaf"); // We have to make it global. Otherwise, if we pass it as a param, the click function populates every field that already have been activated.
        });

        function fillVIAF() {
            dialog.dialog("open");
            var findInViafButton = this;
            fieldToFill = $(findInViafButton).parent().parent().parent().find(".viaf"); // We have to make it global. Otherwise, if we pass it as a param, the click function populates every field that already have been activated.
            $("#selectIdViaf").click(function (e) {
                e.preventDefault();
                var id = $("#VIAFId").val();
                fieldToFill.val(id);
                dialog.dialog("close");
                $(this).off(); // We put the listener off so that it is not used by other instance of Viaf research onclick function.
            });
        }

        /*___________________________open next div when click on button________________*/
        $("#add_original_text_work_historicalActor").on("click", ".add-work_historicalActors", function (e) {
            e.preventDefault();
            $(this).next().removeAttr('hidden');
            $(this).attr('disabled', 'disabled');
        });
        index++;
    }

    /**
     * Add a "delete" button to prototype, and function to delete related div 
     * Adding button directly in prototype definition doesn't work as well, button are not naturally connected to their "onClick" action.
     * 
     * @param {type} prototype
     * @returns html button and method 
     */
    function addDeleteLink(prototype) {
        var deleteLink = $('<a href="#" class="btn btn-danger btn-lg buttom-marge"><span class="glyphicon glyphicon-remove-sign"></span></a>');
        prototype.find('.deletePosition').append(deleteLink);
        deleteLink.click(function (e) {
            prototype.remove();
            e.preventDefault();
            return false;
        });
    }

    /**
     * Add a Find in Viaf button. Method is described higher
     * 
     * @param {type} prototype
     * @returns {undefined}
     */
    function addFindInVIAF(prototype) {
        var findInViaf = $('<a class="btn btn-default open-findInVIAF buttom-marge" href="#"> Find ID in VIAF </a> ');
        prototype.find('.findInVIAFLocation').append(findInViaf);
    }

};



