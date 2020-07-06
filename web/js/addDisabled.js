var addDisabled = function (field_id) {
    $(field_id).attr('disabled', 'disabled');
};

var removeDisabled = function (field_id) {
    $(field_id).removeAttr('disabled');
};

var addHidden = function (field_id) {
    $(field_id).addClass('hidden');
};

var removeHidden = function (field_id) {
    $(field_id).removeClass('hidden');
};

var deleteContent = function (field_class) {
    $(field_class).find("input, select").val("");
};


/**
 * 
 * @param {type} field_to_disable : must be the ID of the field to disable, like '#myField'
 * @param {type} field_to_listen : fiel to listen with its ID like '#myField2'. Disabling the first field depend on this second. 
 * @param {type} arg_to_listen : if arg is selected, the field is back to 'able'; 
 * @param {type} disabled_by_default : boolean, TRUE if the field must be disabled first.
 * @returns {undefined}
 */
var switchDisabled = function (field_to_disable, field_to_listen, arg_to_listen, disabled_by_default) {
    if ((disabled_by_default) && ($(field_to_listen).val !== arg_to_listen)) {
        addDisabled(field_to_disable);
    }
    $(field_to_listen).on('change', function () {
        if ($(this).val() === arg_to_listen) {
            removeDisabled(field_to_disable);
        } else {
            (addDisabled(field_to_disable));
        }
    });

};