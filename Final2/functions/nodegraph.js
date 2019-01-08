function createNodeGraph(documents) {
    var temp_nodes = getNodes(documents);
    var temp_edges = getEdges(documents, temp_nodes);
    var NODES = [];
    var pgRank = findPageRankTo(temp_nodes, temp_edges);
    var maximumRankedNode = findPageRankFrom(pgRank);
    var activeNode = betweenCentrality(temp_edges, temp_nodes);
    var pgRank2 = getPgRank2();

    var activity = getActivity(temp_edges, temp_nodes);

    //pushing the size according to the pg rank to each node
    for (var x = 0; x < temp_nodes.length; x++) {
        if (temp_nodes[x].id == activeNode.node) {
            NODES.push({
                id: temp_nodes[x].id,
                label: temp_nodes[x].label,
                size: pgRank2[x].rankOfNode+5,
                color: 'red',
            });
        } else {
            NODES.push({
                id: temp_nodes[x].id,
                label: temp_nodes[x].label,
                size: activity[x] + 5,
            });
        }
    }
    var nodes = new vis.DataSet(NODES);
    var edges = new vis.DataSet(temp_edges);
    var nodeAmount = getNodeAmount(nodes);

    document.getElementById('nodeAmount').innerHTML += "Number of Nodes: " + nodes.length;
    document.getElementById('edgesAmount').innerHTML += "Number of Edges: " + edges.length;
    document.getElementById('highestPageRank').innerHTML += "Node with the Highest Page Rank: " + nodes.get(maximumRankedNode.nodeTo).label + " (Node ID:" + maximumRankedNode.nodeTo +") with Rank of: " + maximumRankedNode.rankOfNode;
    document.getElementById('activeNode').innerHTML += "Most Active Node is: " + nodes.get(activeNode.node).label + " (Node ID:" + activeNode.node +") with Betweeness Centrality of: " + activeNode.betweenCentrality;
    // create a network
    var container = document.getElementById('graphic');
    var data = { nodes: nodes, edges: temp_edges };
    
    var options = {
        nodes:{
            shape: 'dot',
            font: {size: 12, face: 'Tahoma'}
        },
        edges:{
            smooth: {enabled: true, type: "dynamic", roundness: 0.5},
            arrows:{to:{enabled: true, scaleFactor:1, type:'arrow'}},
            arrowStrikethrough: false
        },
        layout:{
            improvedLayout:false
        },
        physics:{
            enabled:true,
            stabilization: {iterations: 250}
        }
    };
    var network = new vis.Network(container, data, options);
    //Set size of the network.
    network.setSize('1000', '650');
    
    //Turn of physics so you can move nodes again.
    network.on("stabilizationIterationsDone", function () {
        network.setOptions( { physics: false } );
        document.getElementById("loading-box").style.display = "none";
    });

    //When clicking anything in the graph.
    
    network.on('selectNode', function (properties) {
        document.getElementById('avergePath').innerHTML = "";
        if (properties.nodes.length > 0) {
            var temp_edges = getEdges(documents, temp_nodes);
            
            for (var x = 0; x < temp_edges.length; x++) {
                if (properties.nodes == temp_edges[x].to) {
                   
                }
            }
           
                var childNodes = network.getConnectedNodes(properties.nodes);
                console.log("Iam the Selected Node : " + properties.nodes);
                for (var i = 0; i < childNodes.length; i++) {
                    console.log("this is childnode : " + childNodes[i] + " iam connected to Nodes : " + network.getConnectedNodes(childNodes[i]));
            }
            var avgPath = getAveragePaths();
            document.getElementById('avergePath').innerHTML += "From Node: " + properties.nodes[0] + " to: <br />";
            for (var i = 0; i < avgPath.length; i++) {
                if (properties.nodes[0] == avgPath[i].from) {
                    document.getElementById('avergePath').innerHTML += avgPath[i].to + "<br />";
                }
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



