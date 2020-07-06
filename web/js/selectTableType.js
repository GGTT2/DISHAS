    
        $(document).ready(function () {
            //1. We need to void the options set each time a new table is selected
            function voidTableType() {
                $("#tamas_astrobundle_astronomicalobject_tableTypes").empty();
            }
            
            //2. We fix which field to listen ahead, before an other one was selected
            var valToListen = $("#tamas_astrobundle_astronomicalobject_objectName").val();
            
            //3. Fonction fillFields
            /**
             * 
             * @param {type} valToListen is the value of the field "astronomical object" on which depends this field.
             * @returns {html} it returns an "append" function on the html containg the list of table type possible option 
             */
            function fillFields(valToListen){
                 var route = Routing.generate('tamas_astro_autofill', {'entityName': 'tableType', 'option': valToListen});
                myAutofill(route, "tamas_astrobundle_astronomicalobject_tableTypes");
//                var route = Routing.generate('tamas_astro_autocomplete', {'entityName': 'tableType', 'term': valToListen}); //JSRoutingBundle method routing.
//                $.getJSON(route,
//                        function (data) {
//                            voidTableType();
//                            for (i = 0; i < data.length; i++) {
//                                $("#tamas_astrobundle_astronomicalobject_tableTypes").append($('<option>', {
//                                    value: data[i].id,
//                                    text: data[i].tableTypeName})
//                                        );
//                            }
//                        })
            }
            fillFields(valToListen);
           
            $("#tamas_astrobundle_astronomicalobject_objectName").on('change', function(){
              var valToListen = $(this).val();
              fillFields(valToListen);
            });
        })

