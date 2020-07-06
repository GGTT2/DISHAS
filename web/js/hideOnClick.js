var addHidden = function (field_class) {
    $(field_class).addClass('hidden');
};

var removeHidden = function (field_class) {
    $(field_class).removeClass('hidden');
};



/**
 * 
 * @param {type} field_to_disable : must be the ID of the field to disable, like '#myField'
 * @param {type} field_to_listen : fiel to listen with its ID like '#myField2'. Disabling the first field depend on this second. 
 * @param {type} arg_to_listen : if arg is selected, the field is back to 'able'; 
 * @param {type} disabled_by_default : boolean, TRUE if the field must be disabled first.
 * @returns {undefined}
 */
var switchHidden = function (field_to_listen) {
    $(document).ready(function () {
        //console.log($(field_to_listen));
        $(field_to_listen).each(function () {
            field_true = $(this).closest(".parameter").find($("div.parameter-intSexa"));
            field_false = $(this).closest(".parameter").find($("div.parameter-decimal"));

            if ($(this).is(':checked')) {
                //console.log("checked");
                addHidden(field_false); //we hide decimal
                removeHidden(field_true);
            } else {
                //console.log("unchecked");
                addHidden(field_true);
                removeHidden(field_false);
            }
        });




        $(field_to_listen).change(function () {
            field_true = $(this).closest(".parameter").find($("div.parameter-intSexa"));
            field_false = $(this).closest(".parameter").find($("div.parameter-decimal"));
            if ($(this).is(':checked')) {
                //console.log("bye bye");
                addHidden(field_false); //we hide decimal
                removeHidden(field_true);
            } else {
                addHidden(field_true);
                removeHidden(field_false);
            }
        });
    });
};



