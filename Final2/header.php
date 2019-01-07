<!DOCTYPE html>
<html lang="en" >
<head>
    <meta charset="UTF-8">
    <title>D3 Force Directed Graph</title>
    <!-- CSS -->
    <link rel="stylesheet" href="includes/style.css">
    <!-- External Files -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/vis/4.21.0/vis.min.js"></script>
    <script src="https://d3js.org/d3.v3.min.js" charset="utf-8"></script>
    <script type="text/javascript" src="includes/d3.layout.cloud.js"></script>
    <?php
        //Innclude Porter Stemmer Library by https://tartarus.org/martin/PorterStemmer/php.txt
        include 'includes/stemmer.php';
        include 'functions/functions.php';
        //Loading the XML File
        $xml = simplexml_load_file("includes/data.xml") or die("Error: Cannot create object");
        //Where the document objects will be stored.
        $documents = [];
        //Parse the $xml file and documents is stored with an array of documents objects.
        parseDocument($xml);
    ?>
    <!-- Internal Files -->
    <script type="text/javascript" src="functions/functions.js"></script>
    <script type="text/javascript" src="functions/keyword.js"></script>
    <script type="text/javascript" src="functions/nodegraph.js"></script>
    <script>
        //Setting the PHP List of Objects to JS List of Objects.
        var documents = [];
        //Appending each document from php to the JS documents array.
        <?php foreach($documents as $document) { ?>
        var keywords = <?= json_encode($document->keywordsWeights) ?> ;
        var newDoc = {};
        newDoc["keywords"] = keywords;
        newDoc["to"] = <?= json_encode($document->to) ?> ;
        newDoc["from"] = <?= json_encode($document->from) ?> ;
        newDoc["emailNo"] = <?= json_encode($document->emailNo) ?> ;
        documents.push(newDoc);
        <?php } ?>
        console.log(documents);	 

    </script>
</head>
<body onload="createNodeGraph(documents)">