//creates node graph
function createNodeGraph(documents) {
    var temp_nodes = getNodes(documents);                               //extracted nodes from the documents 
    var temp_edges = getEdges(documents, temp_nodes);                   //extracted edges from the documents
    var NODES = [];                                                     //Nodes with size according to the activity
    var pgRank = findPageRankTo(temp_nodes, temp_edges);              //Pgrank given off from nodes                
    var maximumRankedNode = findPageRankFrom(pgRank);                     
    var activeNode = betweenCentrality(temp_edges, temp_nodes);            
    var pgRank2 = getPgRank2();                                         //result pgrank of each node
    var activity = getActivity(temp_edges, temp_nodes);
    console.log(activity);
    //pushing the size according to the pg rank to each node
    for (var x = 0; x < temp_nodes.length; x++) {
        //the most active node is coloured red
        if (temp_nodes[x].id == activeNode.node) {
            NODES.push({
                id: temp_nodes[x].id,
                label: temp_nodes[x].label,
                size: activity[x] + 5,
                color: 'red',
            });
        }
        //otherwise push in a normal coloured node
        else {
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
   
    //display the results in index.php
    document.getElementById('nodeAmount').innerHTML += "Number of Nodes: " + nodes.length;
    document.getElementById('edgesAmount').innerHTML += "Number of Edges: " + edges.length;
    document.getElementById('highestPageRank').innerHTML += "Node with the Highest Page Rank: " + nodes.get(maximumRankedNode.nodeTo).label + " (Node ID:" + maximumRankedNode.nodeTo +") with Rank of: " + maximumRankedNode.rankOfNode;
    document.getElementById('activeNode').innerHTML += "Most Active Node is: " + nodes.get(activeNode.node).label + " (Node ID:" + activeNode.node +") with Betweeness Centrality of: " + activeNode.betweenCentrality;

    // create a network
    var container = document.getElementById('graphic');
    var data = { nodes: nodes, edges: temp_edges };

    //other options on how to render the force graph
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

    //selecting a node
    network.on('selectNode', function (properties) {
        document.getElementById('avergePath').innerHTML = "";
        if (properties.nodes.length > 0) {

                //outputs the children of the child nodees
                var childNodes = network.getConnectedNodes(properties.nodes);
                console.log("Iam the Selected Node : " + properties.nodes);
                for (var i = 0; i < childNodes.length; i++) {
                    console.log("this is childnode : " + childNodes[i] + " iam connected to Nodes : " + network.getConnectedNodes(childNodes[i]));
            }
            //outputs the shortest paths from the selected nodes to other possible nodes
            var avgPath = getAveragePaths();
            document.getElementById('avergePath').innerHTML += "From Node: " + properties.nodes[0] + " to: <br />";
            for (var i = 0; i < avgPath.length; i++) {
                if (properties.nodes[0] == avgPath[i].from) {
                    
                   // for (var j = 0; j < temp_nodes.length; j++) {
                        //if (temp_nodes[j].label == avgPath[i].to) {
                            document.getElementById('avergePath').innerHTML += avgPath[i].to + "<br />";
                       // }
                   // }
                    
                }
            }
                
            }
    });
    //selecting an edge
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



