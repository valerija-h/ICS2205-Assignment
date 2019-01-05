var pgRank2 = [];

function findPageRankTo(temp_nodes, temp_edges) {
    var damping_factor = 0.825;
    var pgRank = [];
    //setting up communicators first
    for (var x = 0; x < temp_nodes.length; x++) {
        var tos = [];
        var from = 0;
        for (var y = 0; y < temp_edges.length; y++) {
            if (temp_nodes[x].id == temp_edges[y].from) {
                from = temp_edges[y].from;
                tos.push(temp_edges[y].to);
            }
        }
        if (from == 0) {
            from = x + 1;
        }
        var pgrank = 0;
        if (tos.length) {
            pgrank = 1 / tos.length;
        } else {
            pgrank = 0;
        }
        pgRank.push({
            nodeFrom: from,
            to: tos,
            rank: pgrank,
        });
    }
    //calculating the damping factor
    for (var x = 0; x < pgRank.length; x++) {
        var y = (1 - damping_factor) / pgRank.length;

        pgRank[x].rank = pgRank[x].rank + y;
    }
    return pgRank;
}

function findPageRankFrom(pgRank) {
    //now we need to find how many links go into a node since previously we saw how many links go out of a node.

    
    var maximumRankedNode = { nodeTo: 0, nodesFrom: [], rankOfNode: 0 };
    var maxRank = 0;
    for (var x = 0; x < pgRank.length; x++) {
        var froms = [];
        var rankOfNode = 0;
        var to = pgRank[x].nodeFrom;
        for (var y = 0; y < pgRank.length; y++) {

            for (var z = 0; z < pgRank[y].to.length; z++) {
                if (to == pgRank[y].to[z]) {
                    froms.push(pgRank[y].nodeFrom);
                    rankOfNode = rankOfNode + pgRank[y].rank;
                }
            }
        }
        pgRank2.push({
            nodeTo: to,
            nodesFrom: froms,
            rankOfNode: rankOfNode,
        });
        if (pgRank2[x].rankOfNode >= maxRank) {
            maxRank = pgRank2[x].rankOfNode;
            maximumRankedNode = { nodeTo: pgRank2[x].nodeTo, nodesFrom: pgRank2[x].nodesFrom, rankOfNode: pgRank2[x].rankOfNode };
        }
    }
    return maximumRankedNode;

}

function betweenCentrality(temp_edges, temp_nodes) {
    var betweenCentrality = [];
    var shortPathsPassingThru = [];
    
    
    for (var x = 0; x < temp_nodes.length; x++) {
        var allShortPaths = [];
        var froms = [];
        var tos = [];
        for (var y = 0; y < temp_edges.length; y++) {

            if (x == temp_edges[y].to) {
                froms.push(temp_edges[y].from);
            }
            else if (x == temp_edges[y].from) {
                tos.push(temp_edges[y].to);
            }
            else {
                allShortPaths.push({
                    to: temp_edges[y].to,
                    from: temp_edges[y].from
                });
            }

        }
        shortPathsPassingThru.push({
            node: x,
            to: tos,
            from: froms,
            allothers: allShortPaths
        });
    }

    var max = 0;
    var activeNode = { node:0, betweenCentrality:0 };
    for (var x = 0; x<shortPathsPassingThru.length; x++) {
        var pathsThru = shortPathsPassingThru[x].to.length + shortPathsPassingThru[x].from.length;
        var otherPaths = shortPathsPassingThru[x].allothers.length;
        var betweeness = pathsThru / otherPaths;
        
        betweenCentrality.push({
            node: shortPathsPassingThru[x].node,
            betweeness: betweeness,
        });

        if (betweeness >= max) {
            max = betweeness;
            activeNode = { node: betweenCentrality[x].node, betweenCentrality: betweenCentrality[x].betweeness };
        }
    }

    return activeNode;
    
}

function createNodeGraph(documents) {
    var temp_nodes = getNodes(documents);
    var temp_edges = getEdges(documents, temp_nodes);
    var NODES = [];
    var pgRank = findPageRankTo(temp_nodes, temp_edges);
    var maximumRankedNode = findPageRankFrom(pgRank);
    var activeNode = betweenCentrality(temp_edges, temp_nodes);
    //pushing the size according to the pg rank to each node
    for (var x = 0; x < temp_nodes.length; x++) {
        if (temp_nodes[x].id == activeNode.node) {
            NODES.push({
                id: temp_nodes[x].id,
                label: temp_nodes[x].label,
                size: pgRank2[x].rankOfNode * 2,
                color: 'red',
            });
        } else {
            NODES.push({
                id: temp_nodes[x].id,
                label: temp_nodes[x].label,
                size: pgRank2[x].rankOfNode * 2,
            });
        }
    }
    console.log(temp_edges);
    var nodes = new vis.DataSet(NODES);
    var edges = new vis.DataSet(temp_edges);
    var nodeAmount = getNodeAmount(nodes);

    document.getElementById('nodeAmount').innerHTML += "Number of Nodes: " + nodes.length;
    document.getElementById('edgesAmount').innerHTML += "Number of Edges: " + edges.length;
    document.getElementById('highestPageRank').innerHTML += "Node with the Highest Page Rank: " + maximumRankedNode.nodeTo + " with Rank of:" + maximumRankedNode.rankOfNode;
    document.getElementById('activeNode').innerHTML += "Most Active Node is: " + activeNode.node + " with betweeness centrality of: " + activeNode.betweenCentrality;
    // create a network
    var container = document.getElementById('graphic');
    var data = { nodes: nodes, edges: temp_edges };
    
    var options = {
        nodes:{
            shape: 'dot',
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
    network.setSize('1000', '650');
    
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



