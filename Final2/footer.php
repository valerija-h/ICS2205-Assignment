
    <script>
        //Closes the modal box when the user presses the cross.
        document.getElementsByClassName("close")[0].onclick = function() {
            document.getElementById('modal-box').style.display = "none";
            document.getElementsByClassName("word-graph")[0].innerHTML = "";
        };
        //Closes the modal box when the user clicks on area outside the box.
        window.onclick = function(event) {
            if (event.target == document.getElementById('modal-box')) {
                document.getElementById('modal-box').style.display = "none";
                document.getElementsByClassName("word-graph")[0].innerHTML = "";
            }
        }
    </script>
    </body>
</html>