function getNodes(documents){
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
