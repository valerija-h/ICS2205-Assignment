<?php
include 'functions.php';

//Loading the XML File
$xml = simplexml_load_file("data.xml") or die("Error: Cannot create object");

//Where the document objects will be stored.
$documents = [];

//Parse the $xml file and documents is stored with an array of documents objects.
parseDocument($xml);
//printSenders();
getLinks();
//print_r($documents);
//echo sizeof($documents);


?>

<script>
    var documents = [];

    //Appending eahc document from php to the JS documents array.
    <?php foreach($documents as $document){?>
    var senders = <?= json_encode($document->senders) ?> ;
    var keywords = <?= json_encode($document->keywordsWeights) ?> ;
    var newDoc = {};
    newDoc["senders"] = senders;
    newDoc["keywords"] = keywords;
    documents.push(newDoc);
    <?}?>

    console.log(documents);
</script>
