function getNodes(documents) {
    var nodes = [];
    var counter = 1;
    //For each document if sender is not in array send it!
    for (var x = 0; x < documents.length; x++) {
        //For each document check if sender exists in emails, if not, add it.
        if (!ifExists(nodes,documents[x].to)) {
            nodes.push({
                id: counter,
                label: documents[x].to
            });
            counter++;
        }
        if (!ifExists(nodes,documents[x].from)) {
            nodes.push({
                id: counter,
                label: documents[x].from
            });
            counter++;
        }
    }
    return nodes;
}

//return index of node
function getIndex(nodes, string){
    for(var i = 0; i < nodes.length; i++) {
        if (nodes[i].label == string) {
            return i+1;
        }
    }
    return 0;
}

//Check if string exists in the nodes array.
function ifExists(nodes, string){
    var found = false;
    for(var i = 0; i < nodes.length; i++) {
        if (nodes[i].label == string) {
            found = true;
            break;
        }
    }
    return found;
}

//get the edges between nodes
function getEdges(documents, nodes){
    var edges = [];
    console.log(documents);
    //For each document, push the from and to
    for (var x = 0; x < documents.length; x++) {
        //each edge will contain from to nodes, the keywords between those two nodes and the number of emails sent
        edges.push({
            from: getIndex(nodes, documents[x].from),
            to: getIndex(nodes, documents[x].to),
            keywords: documents[x].keywords,
            width: documents[x].emailNo
        })
    }
    return edges;
}

//get the amount of nodes
function getNodeAmount(nodes) {
    var nodeAmount = 0;
    for (var x = 0; x < nodes.length; x++) {
        nodeAmount = nodeAmount + 1;
    }
    return nodeAmount;
}

//tells how many email a node sent and received, showing which node was the most active
function getActivity(temp_edges,temp_nodes){
    var activity = [];
    //For each node, get the number of emails from each surrounding edge.
    for (var x = 0; x < temp_nodes.length; x++) {
        var total = 0;
        for(var y = 0; y < temp_edges.length; y++){
            if(temp_edges[y].to == temp_nodes[x].id || temp_edges[y].from == temp_nodes[x].id){
                total +=  parseInt(temp_edges[y].width, 10);
            }
        }
        activity.push(total);
    }
    return activity;
}

var averagePaths = [];
function getAveragePaths() {
    return averagePaths;
}


//calculates the between centrality of each node
function betweenCentrality(temp_edges, temp_nodes) {
  
    var betweenCentrality = [];
    var shortPathsPassingThru = [];
    //create graph
    var graph = {};

    var layout = {};

    for (var x = 0; x < temp_nodes.length; x++) {
        var allPossiblePaths = []; // disjktra
        var allPossibleShortPaths = [];
        var allShortPaths = [];
        var froms = [];
        var tos = [];
        for (var y = 0; y < temp_edges.length; y++) {

            if (x == temp_edges[y].to) {
                froms.push(temp_edges[y].from);
                //for dijkstra
                allPossiblePaths.push('' + (temp_edges[y].from));
            }
            else if (x == temp_edges[y].from) {
                tos.push(temp_edges[y].to);
                //for dijkstra
                allPossiblePaths.push('' + (temp_edges[y].to));
            }
            else {
                allShortPaths.push({
                    to: temp_edges[y].to,
                    from: temp_edges[y].from
                });

            }

        }

        layout[x] = allPossiblePaths;



        shortPathsPassingThru.push({
            node: x,
            to: tos,
            from: froms,
            allothers: allShortPaths
        });
    }

    for (var id in layout) {
        if (!graph[id])
            graph[id] = {};
        layout[id].forEach(function (aid) {
            graph[id][aid] = 1;
            if (!graph[aid])
                graph[aid] = {};
            graph[aid][id] = 1;
        });
    }

    for (var x = 0; x<temp_nodes.length; x++) {
        //choose start node
        var start = '' + x;
        //get all solutions
        var solutions = solve(graph, start);
        
        var label = "";
        //console.log("From '" + start + "' to");
        //display solutions
        for (var s in solutions) {
            
            if (!solutions[s]) continue;
            for (var y = 0; y < temp_nodes.length; y++) {
                if (temp_nodes[y].id == s) {
                    label = temp_nodes[y].label;
                }
            }
            averagePaths.push({

                from: x,
                to: " -> " + s + " [ " + label + " ] " + ": [" + solutions[s].join(", ") + "]   (dist:" + solutions[s].dist + ")"
            });
            
        }
    }

    var max = 0;
    //finding the most active node
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

function findPageRankTo(temp_nodes, temp_edges) {
    var pgRank = [];
    //setting up communicators first
    for (var x = 0; x < temp_nodes.length; x++) {
        var tos = [];
        var from = 0;
        var width = 0;
        for (var y = 0; y < temp_edges.length; y++) {
            if (temp_nodes[x].id == temp_edges[y].from) {
                
                from = temp_edges[y].from;
                tos.push({
                    to: temp_edges[y].to,
                    width: temp_edges[y].width
                });
            }
        }
        if (from == 0) {
            from = x + 1;
        }
        var pgrank = 0;
        if (tos.length) {
            pgrank = (1 / tos.length) ;
        } else {
            pgrank = 0;
        }
        pgRank.push({
            nodeFrom: from,
            to: tos,
            rank: pgrank,
        });
    }
    return pgRank;
}

//GLOBAL VARIABLE
var pgRank2 = [];
//Returning the global variable.
function getPgRank2(){
    return pgRank2;
}

function findPageRankFrom(pgRank) {
    var damping_factor = 0.825;
    //now we need to find how many links go into a node since previously we saw how many links go out of a node.
    var maximumRankedNode = { nodeTo: 0, nodesFrom: [], rankOfNode: 0 };
    var maxRank = 0;
    //add up all the ranks going in a node to establish the result pgrank of a node
    for (var x = 0; x < pgRank.length; x++) {
        var froms = [];
        var rankOfNode = 0;
        var to = pgRank[x].nodeFrom;
        for (var y = 0; y < pgRank.length; y++) {

            for (var z = 0; z < pgRank[y].to.length; z++) {
                if (to == pgRank[y].to[z].to) {
                    froms.push(pgRank[y].nodeFrom);
                    rankOfNode = rankOfNode + (pgRank[y].rank * pgRank[y].to[z].width);
                }
            }
        }
        pgRank2.push({
            nodeTo: to,
            nodesFrom: froms,
            rankOfNode: rankOfNode,
        });
        //finding the node with the highest pgrank
        if (pgRank2[x].rankOfNode >= maxRank) {
            maxRank = pgRank2[x].rankOfNode;
            maximumRankedNode = { nodeTo: pgRank2[x].nodeTo, nodesFrom: pgRank2[x].nodesFrom, rankOfNode: pgRank2[x].rankOfNode };
        }
    }

    //calculating the damping factor for nodes that do not have any incoming edges they wont end up with a rank of 0
    for (var x = 0; x < pgRank2.length; x++) {
        var y = (1 - damping_factor) / pgRank2.length;

        pgRank2[x].rankOfNode = pgRank2[x].rankOfNode + y;
    }
    return maximumRankedNode;

}

