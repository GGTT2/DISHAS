//web\js\addContextualInfo.js

function publisherInfo() {
    $("#locDate>i ").remove();
    $("#locDate").empty();
    var place = $("#add_original_text_place_placeName").val();
    var dateTPQ = $("#add_original_text_tpq").val();
    var dateTAQ = $("#add_original_text_taq").val();
    var dateMessage = dateTPQ + " - " + dateTAQ;
    (dateTPQ === "") ? dateMessage = "? - " + dateTPQ : true;
    (dateTAQ === "") ? dateMessage = dateTPQ : true;
    var message = place + " -- " + dateMessage;
    $("#locDate").append(message);
}