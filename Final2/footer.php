
    <script>
		console.log();
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