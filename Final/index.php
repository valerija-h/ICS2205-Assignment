<!DOCTYPE html>
<html lang="en" >
<head>
    <meta charset="UTF-8">
    <title>D3 Force Directed Graph</title>
    <!-- Including the css for the node graph -->
    <link rel="stylesheet" href="css/node_style.css">
	<link rel="stylesheet" href="css/overlay_style.css">
	<link rel="stylesheet" href="css/word_style.css">
    <script src="https://d3js.org/d3.v3.min.js" charset="utf-8"></script>
	  <script src="include/d3.layout.cloud.js"></script>
</head>
<body>
  <div>
    <h2>Users Node Graph</h2>
	<!-- Trigger/Open The Modal -->
	<button id="myBtn">Open Modal</button>
    <div class="container"><div id="graphic"></div></div>
  </div>
  <?php require 'node_script.php'; ?>
 
   <!-- The Modal -->
<div id="myModal" class="modal">

  <!-- Modal content -->
  <div class="modal-content">
    <div class="modal-header">
      <span class="close">&times;</span>
      <h2>WordCloud</h2>
    </div>
    <div class="modal-body">
	  <head>
		<script>
		function goBack() {
			window.location.replace('index.php');
			}
		</script>
      <title>Word Cloud</title>
	</head>
	<body>
	 
      <h1 style="text-align:center">Word Cloud</h1>
	  <button onclick="goBack()">Go Back</button>
      <div class="container" style="text-align:center">
          <div class="child" style="display:inline-block">
				<script type="text/javascript" src="functions/functions.js"></script> 
				<script type="text/javascript" src="functions/functions_word.js"></script> 
          </div>
      </div>
	</body>
		
    </div>
    <div class="modal-footer">
      <h3>Modal Footer</h3>
    </div>
	<script src="functions/overlay_script.js"></script>
  </div>

</div>

</body>
</html>
