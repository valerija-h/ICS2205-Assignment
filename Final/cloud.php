


<!DOCTYPE html>  
<html>
  <script src="http://d3js.org/d3.v3.min.js"></script>
  <script src="Gabe/WordCloud/d3.layout.cloud.js"></script>
    
  
  <head>
		<script>
function goBack() {
 window.location.replace('index.php');
}
</script>
      <link rel="stylesheet" href="Gabe/WordCloud/css/style.css">
      <title>Word Cloud</title>
  </head>

  <body>
      <h1 style="text-align:center">Word Cloud</h1>
	  <button onclick="goBack()">Go Back</button>
      <div class="container" style="text-align:center">
          <div class="child" style="display:inline-block">
             <?php require 'Gabe/WordCloud/scripts/node_script.php'; ?>
          </div>
      </div>
  </body>

</html>
