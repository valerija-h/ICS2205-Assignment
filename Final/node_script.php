<?php
include 'functions/functions.php';

//Loading the XML File
$xml = simplexml_load_file("include/data.xml") or die("Error: Cannot create object");

//Where the document objects will be stored.
$documents = [];

//Parse the $xml file and documents is stored with an array of documents objects.
parseDocument($xml);

$keywords = [];

setcookie('your_cookie_name', json_encode($keywords), time()+3600);

?>
<script type="text/javascript" src="functions/functions.js"></script>
 <script type="text/javascript">
	 var modal = document.getElementById('myModal');
     //Setting the PHP List of Objects to JS List of Objects.
      var documents = [];
      var words = [];
      //Appending each document from php to the JS documents array.
      <?php foreach($documents as $document) { ?>
      var senders = <?= json_encode($document->senders) ?> ;
      var keywords = <?= json_encode($document->keywordsWeights) ?> ;
      var newDoc = {};
      newDoc["senders"] = senders;
      newDoc["keywords"] = keywords;
      documents.push(newDoc);
      <?php } ?>

     //From function.js
     var emails = getEmails(documents);
     var communicators = getCommunicators(documents);
     var node = getNode(emails,communicators);
     var nodes = getNodes(node);
     var links = getLinks(nodes);
     var numNodes = node.length;
     createGraph(nodes,links,numNodes);
	 
	 
 </script>
