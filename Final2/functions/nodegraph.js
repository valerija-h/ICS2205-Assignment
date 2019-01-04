function createNodeGraph(documents){
    var temp_nodes = getNodes(documents);
    var temp_edges = getEdges(documents,temp_nodes);
    var nodes = new vis.DataSet(temp_nodes);
    var edges = new vis.DataSet(temp_edges);
    var nodeAmount = getNodeAmount(nodes);
    var pageRank = [];

    // create a network
    var container = document.getElementById('graphic');
    var data = { nodes: nodes, edges: edges };
    
    var options = {
        nodes:{
            shape: 'dot',
            scaling: {min: 10, max: 20},
            size:16,
            font: {size: 12, face: 'Tahoma'}
        },
        edges:{
            smooth: {enabled: true, type: "dynamic", roundness: 0.5}
        },
        layout:{
            improvedLayout:false
        },
        physics:{
            enabled:true,
            stabilization: {iterations: 200},
        }
    };
    var network = new vis.Network(container, data, options);
    //Set size of the network.
    network.setSize('1000','650');

    //Turn of physics so you can move nodes again.
    network.on("stabilizationIterationsDone", function () {
        network.setOptions( { physics: false } );
        document.getElementById("loading-box").style.display = "none";
    });

    //When clicking anything in the graph.
    
    network.on('selectNode', function (properties) {
        

        if (properties.nodes.length > 0) {
            var temp_edges = getEdges(documents, temp_nodes);
            console.log(temp_edges);
            for (var x = 0; x < temp_edges.length; x++) {
                if (properties.nodes == temp_edges[x].to) {
                   
                }
            }


                var childNodes = network.getConnectedNodes(properties.nodes);
                console.log("Iam the Selected Node : " + properties.nodes);
                for (var i = 0; i < childNodes.length; i++) {
                    console.log("this is childnode : " + childNodes[i] + " iam connected to Nodes : " + network.getConnectedNodes(childNodes[i]));
                }
                
            }
        });
    network.on('selectEdge', function (properties) {
        if (properties.edges.length > 0) {
            console.log('clicked id ' + properties.edges);
            console.log('clicked node ' + edges.get(properties.edges));
            var edge = edges.get(properties.edges);
            var id = properties.edges;
            var edge_to = edge[0].to;
            var edge_from = edge[0].from;
            console.log(edge[0].keywords);
            document.getElementById('modal-box').style.display = "block";
            //Find a way to display keywords below.
            createKeyCloud(edge[0].keywords);
        }
    });
}



