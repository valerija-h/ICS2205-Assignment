
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
			var pgrank = 0;
			if(tos.length){
			 pgrank = 1/tos.length;}else{pgrank = 0;}
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