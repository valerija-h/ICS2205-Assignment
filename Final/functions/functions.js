//Creating an array of senders.
function getEmails(documents) {
    var emails = [];
    for (var x = 0; x < documents.length; x++) {
        //For each document check if sender exists in emails, if not, add it.
        if (!emails.includes(documents[x].senders[0])) {
            emails.push(documents[x].senders[0]);
        }
        if (!emails.includes(documents[x].senders[1])) {
            emails.push(documents[x].senders[1]);
        }
    }
    return emails;
}

//Creating an array of document objects.
function getCommunicators(documents){
    var communicators = [];
    for(var x = 0; x < documents.length; x++){
        communicators.push({
            senders: documents[x].senders,
            keywords: documents[x].keywords
        });
    }
    return communicators;
}

function getNode(emails,communicators){
    var node = [];
    //For each person, go through each document.
    for(x = 0; x < emails.length; x++){
        var email = emails[x]; //Current person.
        var targets = [];
        for(y = 0; y<communicators.length; y++){
            //If the current person is one of the senders in the current document.
            //Add the person he talks to to the targets array.
            if(email == communicators[y].senders[0]){
                targets.push(communicators[y].senders[1]);
            } else if (email == communicators[y].senders[1]){
                targets.push(communicators[y].senders[0]);
            }
        }
        node.push({
            sender: email,
            target: targets
        })
    }
    //Node = a sender and all the people they talked to.
    return node;
}

function getNodes(node){
    // Generate test data
    var nodes = [];
    var numNodes = node.length;
    for (var x = 0; x < numNodes; x++) {
        var targetAry = [];
        var connections = node[x].target.length;
        for (var y = 0; y < connections; y++) {
            targetAry.push( node[x].target[y])
        }
        var ids = node[x].sender.replace(/\s/g,'');
        nodes.push({
            id: node[x].sender,
            name: x,
            target: targetAry
        })
    }
    return nodes;
}

function getLinks(nodes){
    // Create the links array from the generated data
    var links = [];
    status = 0;
    status2 = 0;
    //loops through all the nodes
    for (var i = 0; i < nodes.length; i++) {
        status = 0;
        if (nodes[i].target !== undefined) {
            //loops through the array of target of each node
            for (var j = 0; j < nodes[i].target.length; j++) {

                //add the target node to the node as link
                for(var k = 0; k < nodes.length; k++){
                    //console.log(node[k]);
                    //for(var l = 0; l < links)
                    if(nodes[i].target[j]==nodes[k].id && status == 0){
                        status = 1
                        links.push({
                            source: nodes[i],
                            target: nodes[k]
                        })
                    }
                }

            }
        }
    }
    return links;
}

function createGraph(nodes,links,numNodes){
    // Configure graphics
    var width = 1000,
        height = 1000;

    var circleWidth = 5,
        charge = -75,
        gravity = 0.1;

    var palette = {
        "lightgray": "#D9DEDE",
        "gray": "#C3C8C8",
        "mediumgray": "#536870",
        "orange": "#BD3613",
        "purple": "#595AB7",
        "yellowgreen": "#738A05"
    }

    var force = d3.layout.force()
        .gravity(.05)
        .distance(100)
        .charge(-100)
        .size([width, height]);

    // Create SVG
    var fdGraph = d3.select('#graphic')
        .append('svg')
        .attr('width', width)
        .attr('height', height)

    // Create the force layout to calculate and animate node spacing
    var forceLayout = d3.layout.force()
        .nodes(nodes)
        .links([])
        .gravity(gravity)
        .charge(charge)
        .size([width, height])

    // Create the SVG lines for the links
    var link = fdGraph
        .selectAll('line').data(links).enter()
        .append('line')
        .attr('stroke', palette.gray)
        .attr('stroke-width', 1)
        .attr('class', function(d, i) {
            // Add classes to lines to identify their from's and to's
            var theClass ='line_' + d.source.id + ' line';
            if(d.target !== undefined) {
                theClass += ' to_' + d.target.id
            }
            return  theClass
        })
        .on("click", function (d) {
            console.log(d);
            alert("You clicked on the link from node " + d.source.id + " to " + d.target.id);
            //passing the array of keywords between the two communicators over here
            for(var x = 0; x< communicators.length; x++){
                if((d.source.id == communicators[x].senders[0] || d.source.id == communicators[x].senders[1] ) && (d.target.id == communicators[x].senders[0] || d.target.id == communicators[x].senders[1])){
                    //console.log(communicators[x].keywords);
                    words = communicators[x].keywords;
                    var params = JSON.stringify(words);

                    setCookie('myCookie',params,365);

                    console.log(document.cookie);
                    window.open("index2.php");
                    //window.location.assign('../WordCloud/index.php' + '?words=' + JSON.stringify(words));
                }
            }
        });

    // Create the SVG groups for the nodes and their labels
    node = fdGraph
        .selectAll('circle').data(nodes).enter()
        .append('g')
        .attr('id', function(d) { return 'node_' + d.id })
        .attr('class', 'node')
        .on("click", function (d) {
            alert("You clicked on node " + d.id);
        })
        .on('mouseover', function(d) {
            // When mousing over a node, make the label bigger and bold
            // and revert any previously enlarged text to normal
            d3.selectAll('.node').selectAll('text')
                .attr('font-size', '12')
                .attr('font-weight', 'normal')

            // Highlight the current node
            d3.select(this).select('text')
                .attr('font-size', '50')
                .attr('font-weight', 'bold')

            // Reset and fade-out the unrelated links
            d3.selectAll('.line')
                .attr('stroke', palette.lightgray)
                .attr('stroke-width', 1)

            forceLayout.start();
        })

    // Create the SVG circles for the nodes
    node.append('circle')
        .attr('cx', function(d) {
            return d.x
        })
        .attr('cy', function(d) {
            return d.y
        })
        .attr('r', circleWidth)
        .attr('fill', function(d, i) {
            // Color 1/3 of the nodes each color
            // Depending on the data, this can be made more meaningful
            if (i < (numNodes / 3)) {
                return palette.orange
            } else if (i < (numNodes - (numNodes / 3))) {
                return palette.orange
            }
            return palette.orange

        })

    // Create the SVG text to label the nodes
    node.append('text')
        .text(function(d) {
            return d.name
        })
        .attr('font-size', '12')

    // Animate the layout every time tick
    forceLayout.on('tick', function(e) {
        // Move the nodes base on charge and gravity
        node.attr('transform', function(d, i) {
            return 'translate(' + d.x + ', ' + d.y + ')'
        })

        // Adjust the lines to the new node positions
        link
            .attr('x1', function(d) {
                return d.source.x
            })
            .attr('y1', function(d) {
                return d.source.y
            })
            .attr('x2', function(d) {
                if (d.target !== undefined) {
                    return d.target.x
                } else {
                    return d.source.x
                }
            })
            .attr('y2', function(d) {
                if (d.target !== undefined) {
                    return d.target.y
                } else {
                    return d.source.y
                }
            })
    })

    // Start the initial layout
    forceLayout.start();

}