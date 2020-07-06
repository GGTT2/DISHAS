//
//$(".edit-reference").click(function (e) {
//    e.preventDefault();
//    console.log($(this).attr('user'));
//    if (checkIfAllowed($(this).attr('user'))) {
//        $("#edit-confirm").removeClass("hidden");
//        $("#edit-confirm #user-name").html(($(this).attr('userName')));
//        $("#edit-confirm")
//                .data('link', this)
//                .dialog('open');
//    } else {
//        $("#no-access").removeClass("hidden");
//        $("#no-access").dialog('open');
//    }
//});
//$("#edit-confirm").dialog({
//    autoOpen: false,
//    resizable: false,
//    height: "auto",
//    width: 400,
//    modal: true,
//    buttons: {
//        "Edit this item": function () {
//            $(this).dialog("close");
//            var path = $(this).data('link').href; // Get the stored result
//            $(location).attr('href', path);
//        },
//        Cancel: function () {
//            $(this).dialog('close');
//        }
//    }
//});
//$("#no-access").dialog({
//    autoOpen: false,
//    resizable: false,
//    height: "auto",
//    width: 400,
//    modal: true,
//    buttons: {
//        Cancel: function () {
//            $(this).dialog('close');
//        }
//    }
//});
//$("#delete-reference").click(function (e) {
//    e.preventDefault();
//    if (checkIfAllowed($(this).attr('user'))) {
//        $("#delete-confirm").removeClass("hidden");
//        $("#delete-confirm")
//                .data('link', this)
//                .dialog('open');
//    } else {
//        $("#no-access").removeClass("hidden");
//        $("#no-access").dialog('open');
//    }
//});
//$("#delete-confirm").dialog({
//    autoOpen: false,
//    resizable: false,
//    height: "auto",
//    width: 400,
//    modal: true,
//    buttons: {
//        "Delete this item": function () {
//            $(this).dialog("close");
//            var path = $(this).data('link').href; // Get the stored result
//            $(location).attr('href', path);
//        },
//        Cancel: function () {
//            $(this).dialog("close");
//        }
//    }
//});
