
    <script>
		var damping_factor = 0.825;
			var pgRank = [];
		//setting up communicators first
		for(var x = 0; x<temp_nodes.length; x++){
		var tos = [];
		var from = 0;
			for(var y = 0; y<temp_edges.length; y++){
				if(temp_nodes[x].id == temp_edges[y].from){					
					from = temp_edges[y].from;
					tos.push( temp_edges[y].to);		
				}
			}
			if(from == 0){
				from = x + 1;
			}	
			var pgrank = 0;
			if(tos.length){
			 pgrank = 1/tos.length;}else{pgrank = 0;
			 }
			pgRank.push({
				nodeFrom: from,
				to: tos,
				rank:pgrank,
			});		
		}

		//calculating the damping factor
		for(var x = 0; x<pgRank.length; x++){
			var y = (1-damping_factor)/pgRank.length;

			pgRank[x].rank = pgRank[x].rank + y;
		}

		console.log(pgRank);

		//now we need to find how many links go into a node since previously we saw how many links go out of a node.

		var pgRank2 = [];

		for(var x = 0; x<pgRank.length; x++){
			var froms = [];
			var rankOfNode = 0;
			var to =  pgRank[x].nodeFrom;
			for(var y = 0; y<pgRank.length; y++){
			
				for(var z = 0; z<pgRank[y].to.length; z++){
					if(to == pgRank[y].to[z]){
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
		}

		console.log(pgRank2);
		
		document.getElementById('nodeAmount').innerHTML += "Number of Nodes: " + nodes.length;
		document.getElementById('edgesAmount').innerHTML += "Number of Edges: " + edges.length;

        // Get the modal
        var modal = document.getElementById('modal-box');
        // Get the <span> element that closes the modal
        var span = document.getElementsByClassName("close")[0];

        // When the user clicks on <span> (x), close the modal
        span.onclick = function() {
            modal.style.display = "none";
            document.getElementsByClassName("word-graph")[0].innerHTML = "";
        };
        // When the user clicks anywhere outside of the modal, close it
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
                document.getElementsByClassName("word-graph")[0].innerHTML = "";
            }
        }
		
    </script>
    </body>
</html>