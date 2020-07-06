function fillVis(dependanceTree, htmlGraph, specificNodeId){
	var graphFromDB = new TreeGraph();//This graph reflects the state of the edition and original text in the database before the loading of the page.  
    graphFromDB.loadJSONTree(dependanceTree);
    if (specificNodeId)
    	var graphFromDB = graphFromDB.descendantGraph([graphFromDB.nodes[specificNodeId]]);
    var network = createVisNetwork(graphFromDB, htmlGraph, null);
}
function createVisNetwork(graph, div, thisEditionId, positions) {
    var visGraph = graph.generateVisGraph(positions);
    //console.log(visGraph);
    var nodes = new vis.DataSet(visGraph.nodes);
    // create an array with edges
    var edges = new vis.DataSet(visGraph.edges);
    // create a network
    //var container = document.getElementById("view-graph");
    var container = $(div)[0];
    //console.log(container);
    // provide the data in the vis format

    for (var key in nodes._data) {
        if (key.startsWith("o")) {
            nodes._data[key].color = "#449D44";
        }
        if (thisEditionId !== undefined || thisEditionId !== null) {
            if (key === thisEditionId) {
                nodes._data[key].color = "#f0ad4e";
                nodes._data[key].font = {
                    color: "#474747"
                };
            }
        }
       
    }
    var data = {
        nodes: nodes,
        edges: edges
    };

    var options = {
        edges: {
            arrows: 'from',
            smooth: {
                enabled: false
            }
        },
        physics: {
            enabled: false
            //stabilization: true,
            //solver: 'hierarchicalRepulsion'
        },
        layout: {
            randomSeed: undefined,
            improvedLayout: true,
            hierarchical: {
                enabled: false
            }
        },
        nodes: {
            shape: 'box',
            shadow: {
                enabled: true
            },
            color: "#428BCA",
            font: {
                color: "#ffffff"
            },
            widthConstraint:{
            	maximum:250
            }
            
        }
    };
    // initialize your network!
      //console.log(data);
      //console.log(options);
    network = new vis.Network(container, data, options);
    //network.physics.physicsEnabled = false;
     
//    for (var key in positions) {
//        if (key in nodes._data) {
//            network.moveNode(key, positions[key].x, positions[key].y);
//        }
//    }
    return network;
}