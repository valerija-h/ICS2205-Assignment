<!DOCTYPE html>
<html lang="en" >
<head>
    <meta charset="UTF-8">
    <title>Web Intelligence Assignment</title>
    <!-- CSS -->
    <link rel="stylesheet" href="includes/style.css">
    <!-- Libraries and Scripts -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/vis/4.21.0/vis.min.js"></script>
    <script src="https://d3js.org/d3.v3.min.js" charset="utf-8"></script>
    <script type="text/javascript" src="includes/d3.layout.cloud.js"></script>
    <?php
        //Including functions used for parsing the XML document.
        include 'functions/functions.php';
        //Loading the XML File
        $xml = simplexml_load_file("includes/data.xml") or die("Error: Cannot create object");
        $documents = []; //Where the document objects will be stored.
        //Parse the $xml file and populate the $documents array with document objects.
        parseDocument($xml);
    ?>
    <!-- Our Scripts -->
	<script type="text/javascript" src ="functions/dijkstra.js"></script>
    <script type="text/javascript" src="functions/functions.js"></script>
    <script type="text/javascript" src="functions/keyword.js"></script>
    <script type="text/javascript" src="functions/nodegraph.js"></script>
    <script>
        //Setting the PHP List of Objects to JS List of Objects.
        var documents = [];
        //Appending each document from php to the JS documents array.
        <?php foreach($documents as $document) { ?>
        var newDoc = {};
        newDoc["keywords"] = <?= json_encode($document->keywordsWeights) ?> ;
        newDoc["to"] = <?= json_encode($document->to) ?> ;
        newDoc["from"] = <?= json_encode($document->from) ?> ;
        newDoc["emailNo"] = <?= json_encode($document->emailNo) ?> ;
        documents.push(newDoc);
        <?php } ?>
    </script>
</head>
<!-- Function to load the NodeGraph -->
<body onload="createNodeGraph(documents)">