//Documentation : https://citeproc-js.readthedocs.io/en/latest/deployments.html
function citeData(data, locale, csl) {
    //locate and csl must be escaped for js. 
    //Data exemple (see more : https://github.com/citation-style-language/schema/blob/master/csl-data.json
//    var data = {'items': [{
//                "id": "123456",
//                "type": "book",
//                "title": "I love books",
//                "author": [
//                    {
//                        "family": "De la BelleVille",
//                        "given": "Taos"
//                    }],
//                "issued": {
//                    "raw": "2000-3-15/2000-3-17"
//                }
//            }
//        ]
//    };
//=================================
//  var chosenStyleID = "archive-for-history-of-exact-sciences";
    var citations = {};
    var itemIDs = [];
    for (var i = 0; i < data.items.length; i++) {
        item = data.items[i];
//if (!item.issued)
//        continue;
        if (item.URL)
            delete item.URL;
        var id = item.id;
        citations[id] = item;
        itemIDs.push(id);
    }
    //console.log(itemIDs);
    citeprocSys = {
        retrieveLocale: function (lang) {
            return locale;
        },
        retrieveItem: function (id) {
            return citations[id];
        }
    };
    function getProcessor(styleID) {
        var styleAsText = csl;
        var citeproc = new CSL.Engine(citeprocSys, styleAsText);
        return citeproc;
    }
    ;
    function processorOutput() {
        ret = '';
        var citeproc = getProcessor();
        citeproc.updateItems(itemIDs);
        var result = citeproc.makeBibliography();
        return result[1].join('\n');
    }

    $('#biblio').html(processorOutput());
}