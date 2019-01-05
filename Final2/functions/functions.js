function findPageRankTo(temp_nodes,temp_edges) {
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

function findPageRankFrom() {
    //now we need to find how many links go into a node since previously we saw how many links go out of a node.

    var pgRank2 = [];
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


}

function getNodes(documents) {
    var nodes = [];
    var counter = 1;
    //For each document if sender is not in array send it!
    for (var x = 0; x < documents.length; x++) {
        //For each document check if sender exists in emails, if not, add it.
        if (!ifExists(nodes,documents[x].senders[0])) {
            nodes.push({
                id: counter,
                label: documents[x].senders[0]
            });
            counter++;
        }
        if (!ifExists(nodes,documents[x].senders[1])) {
            nodes.push({
                id: counter,
                label: documents[x].senders[1]
            });
            counter++;
        }
    }
    return nodes;
}

function getIndex(nodes, string){
    for(var i = 0; i < nodes.length; i++) {
        if (nodes[i].label == string) {
            return i+1;
        }
    }
    return 0;
}
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

function getEdges(documents, nodes){
    var edges = [];
    //For each document, push the from and to
    for (var x = 0; x < documents.length; x++) {
        edges.push({
            from: getIndex(nodes, documents[x].senders[0]),
            to: getIndex(nodes, documents[x].senders[1]),
            keywords: documents[x].keywords
        })
    }
    return edges;
}

function getNodeAmount(nodes) {
    var nodeAmount = 0;
    for (var x = 0; x < nodes.length; x++) {
        nodeAmount = nodeAmount + 1;
    }
    console.log(nodeAmount);
    return nodeAmount;
}

