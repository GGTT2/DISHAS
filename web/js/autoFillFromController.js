(function ( $ ) {
   //plug-in to initialize the reload buttons.
            		 
    $.fn.makeReloader = function( options ) {
         // This is the easiest way to have default options.
        var that = this;
        var settings = $.extend({
            // These are the defaults.
            // source: if changes depend on a particular source
            // url : base request
            parameters : {},
            // onSourceChange: function(source){ update(data)}
            // target: is compulsory for reload with graphic change
            // backgroundColor: "white",
            synchronous: false, 
            activate: true,
            reload: function(){
            	var url = Routing.generate(this.route, this.parameters);
            	myAutofill(url, this.target, this.synchronous );
            },
            doBefore: function(){true;},
            doAfter: function(){true;}
        }, options );
        
        this.click(function(e){
        	e.preventDefault();
        	settings.doBefore();
        	if(settings.activate){
        		settings.reload();
        	}
        	settings.doAfter();
	        
	        });

        // return reload button
        return {
				update:  function(){
						settings.reload();
						return this;
				},
				htmlObject : that,
				settings: settings, 
				changeDoAfter: function(action){
					settings.doAfter = action;
					return this;

					}, 
				changeDoBefore: function(action){
						settings.doAfter = action;
						return this;

						}, 
				changeParameters: function(parameters){
					settings.parameters = $.extend(settings.parameters, parameters) ;
					return this;

					},
				changeRoute: function(route){
					settings.route = route;
				}, 
				changeSynchronous: function(synchronous){
					settings.synchronous = synchronous;
				},
				changeActivate: function(a){
					settings.activate = a;
				}
           };
    };
}( jQuery ));


reloaders = {};

$("[reloader]").each(function(){
	var id = "#refresh-"+$(this).attr("id");
	var entityName = $(this).attr("reloader");
	var reloader = $(id).makeReloader({
            route:'tamas_astro_autofill',
            parameters: {'entityName': entityName},
            target: $(this).attr('id')
		});
	reloaders[id] = reloader;
});


function myAutofill(path, selectId, synchronous) {
    if (synchronous !== undefined && synchronous) {
        $.ajaxSetup({
            async: false
        });
    }
    $.getJSON(path, function (result) {
        var select = $("#" + selectId);
        var selectedValue = null;
        if (select.val()) {
            selectedValue = select.val();
        }
        select.empty();
        var items = [];
        var attr = select.attr('multiple');
        if (typeof attr !== typeof undefined && attr !== false) {
        } else {
            items.push("<option value></option>");
        }
        $.each(result, function (key, val) {
            var additional = "";
            var attr = "";
            var draft = '';
            //in case of additional attributes
            if (typeof val.attr !== 'undefined'){
                for (key in val.attr ){
                    attr+=" "+key+"="+val.attr[key];
                }
            }
            // TODO : il serait mieux de passer tout ça dans les attr
            if (typeof val.tableTypeId !== 'undefined') {
                additional = "tableType-id = '" + val.tableTypeId + "'";
            } 
            if (typeof val.draft !== 'undefined' && val.draft === 'draft'){
            	draft = "draft = '"+val.draft+"'";
            }
            if(val.jsonData !== undefined) {
                additional += " data-json='" + val.jsonData + "'";
            }
            var tag;
            if (selectedValue !== null && (selectedValue === val.id || selectedValue.indexOf(val.id.toString()) > -1 || selectedValue.indexOf(val.id) > -1)) {
                tag = "<option value='" + val.id + "' " + additional +draft+ attr + " selected='selected'>" + val.title + "</option>";
            } else {
                tag = "<option value='" + val.id + "' " + additional + draft + attr + ">" + val.title + "</option>";
            }
            items.push(tag);
        });
        select.append(items);
    });
    if (synchronous !== undefined && synchronous) {
        $.ajaxSetup({
            async: true
        });
    }
}

function myAutofillTable(path) {
    $('#visualize-data').empty();
    $(document).ajaxStart(function () {
        $(".loading").removeClass("hidden");
        $("#loading").removeClass("hidden");
    }).ajaxStop(function () {
        $("#loading").addClass("hidden");
    });

    $.ajax({
        url: path,
        context: document.body,
        success: function (response) {
            $('#visualize-data').html(response.view);
            let data = response.data;
            fillTable(data.listObjects, data.spec);
            $.fn.dataTable.tables({visible: true, api: true}).columns.adjust().draw();

            $(".loading").addClass("hidden");
            editionAuthorization();
        },
        error: (response) => {
            console.log('%c Error 😰! ', 'font-size: 24px; background: #222; color: #bada55; font-weight: bold');
            console.log("Response:",response);
        }
    });
}

$(".add-entity").click(function(e){
	e.preventDefault();
	var route = $(this).attr("href");
	route +="?quit=true";
	window.open(route);
});