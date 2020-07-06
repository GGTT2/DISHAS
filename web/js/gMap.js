function myGeocomplete(field) {
    var details = $(field).parent().parent();
    var map = details.next().find('.map');
    var detailsAttribute = "data-geo";
    opt = {
        map: map,
        details: details,
        detailsAttribute: detailsAttribute,
        types: ["geocode", "establishment"],
        mapOptions: {
            zoom:10, // this is not working fine...
            scrollwheel: true
        },
        markerOptions: {
        }
    };
    $(field).geocomplete(opt);
}

$('.geoName').click(function () {
    myGeocomplete(this);

});