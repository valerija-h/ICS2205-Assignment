<?php
include 'Vally/functions.php';

//Loading the XML File
$xml = simplexml_load_file("Vally/data.xml") or die("Error: Cannot create object");

//Where the document objects will be stored.
$documents = [];

//Parse the $xml file and documents is stored with an array of documents objects.
parseDocument($xml);

$keywords = [];

setcookie('your_cookie_name', json_encode($keywords), time()+3600);

?>

 <script>

 function setCookie(cname, cvalue, exdays) {
  var d = new Date();
  d.setTime(d.getTime() + (exdays*24*60*60*1000));
  var expires = "expires="+ d.toUTCString();
  document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}
        var documents = [];
        var words = [];
        //Appending eahc document from php to the JS documents array.
        <?php foreach($documents as $document) { ?>
        var senders = <?= json_encode($document->senders) ?> ;
        var keywords = <?= json_encode($document->keywordsWeights) ?> ;
        var newDoc = {};
        newDoc["senders"] = senders;
        newDoc["keywords"] = keywords;
        documents.push(newDoc);
        <?php } ?>
		
	
	    try {
			//console.log(documents);
			// removing any redunndant emails
			var emails = [];
			var status = 0
			var status2 = 0;
			for(var x = 0; x < documents.length; x++){
				if(x == 0){
					emails.push(documents[x].senders[0]);
					emails.push(documents[x].senders[1]);
				}
				else{
					for(var y = 0; y < emails.length; y++){
						if(documents[x].senders[0] == emails[y]){
							status = 1;
							break;
						}
						else{
							status = 0;
						}
						
					}
					for(var y = 0; y < emails.length; y++){
						if(documents[x].senders[1] == emails[y]){
							status2 = 1;
							break;
						}
						else{
							status2 = 0;
						}
						
					}
					if(status == 0){
						emails.push(documents[x].senders[0]);
					}
					else{
						//nothing
					}
					if(status2 == 0){
						emails.push(documents[x].senders[1]);
					}
					else{
						//nothing
					}

				}
			}

			
		  // array of communicators needed for the wordcloud !!!
		  var communicators = [];
		  var array1 = [];
		  for(var x = 0; x < documents.length; x++){
			communicators.push({
				senders: documents[x].senders,
				keywords: documents[x].keywords
				
				});
		  }
		  console.log(communicators);

		  var node = [];
		  
		  status = 0;
		  status2 = 0;
		  for(var x = 0; x<emails.length; x++){
		  
			var email = emails[x];
			var targets = [];
			for(var y = 0; y<communicators.length; y++){
				if(emails[x]==communicators[y].senders[0] || emails[x]==communicators[y].senders[1]){
					if(emails[x]==communicators[y].senders[0]){
						targets.push(communicators[y].senders[1]);
					}
					else{
						targets.push(communicators[y].senders[0]);
					}
				}
			}
			node.push({
				sender: email,
				target: targets,
			})
		  }
		  
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
		  
	      // Create the links array from the generated data
	      var links = [];
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

		  console.log(links);
		  
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
				  //changing webpage
			      

		      });

	      // Create the SVG groups for the nodes and their labels
	      var node = fdGraph
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
      
		      // Hightlight the nodes that the current node connects to
		      for(var i = 0; i < d.target.length; i++) {
      		    d3.select('#node_' + d.target[i]).select('text')
			    .attr('font-size', '14')
			    .attr('font-weight', 'bold')        
		      }
      
		      // Reset and fade-out the unrelated links
		      d3.selectAll('.line')
    			    .attr('stroke', palette.lightgray)
      		    .attr('stroke-width', 1)
      
		      for(var x = 0; x < links.length; x++) {
			    if(links[x].target !== undefined) {
			      if(links[x].target.id === d.id) {
				    // Highlight the connections to this node
				    d3.selectAll('.to_' + links[x].target.id)//
				      .attr('stroke', palette.purple)
				      .attr('stroke-width', 20)
            
				    // Highlight the nodes connected to this one
				    d3.select('#node_' + links[x].source.id).select('text')
         				    .attr('font-size', '14')
        				    .attr('font-weight', 'bold')              
			      }
			    }
		      }
			
		      // Highlight the connections from this node
		      d3.selectAll('.line_' + d.id)
    			    .attr('stroke', palette.purple)
      		    .attr('stroke-width', 20)

		      // When mousing over a node, 
		      // make it more repulsive so it stands out
		      forceLayout.charge(function(d2, i) {
			    if (d2 != d) {
          
			      // Make the nodes connected to more repulsive
			      for(var i = 0; i < d.target.length; i++) {
				    if(d2.id == d.target[i]) {
				      return charge * 8
				    }
			      }
          
			      // Make the nodes connected from more repulsive
			      for(var x = 0; x < links.length; x++) {
				    if(links[x].source.id === d2.id) {
				      if(links[x].target !== undefined) {
					    if(links[x].target.id === d.id) {
					      return charge * 8
					    }
				      }
				    }
			      }
          
			      // Reset unrelated nodes
			      return charge;
          
			    } else {
			      // Make the selected node more repulsive
			      return charge * 10;
			    }
		      });
		      forceLayout.start();
		    })
		    .call(forceLayout.drag)

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

	    } catch (e) {
	      alert(e)
	    }

    </script>