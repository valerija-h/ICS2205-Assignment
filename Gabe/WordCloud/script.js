<? php
include '../../Vally/functions.php';

//Loading the XML File
$xml = simplexml_load_file("../../Vally/data.xml") or die("Error: Cannot create object");

//Where the document objects will be stored.
$documents = [];

//Parse the $xml file and documents is stored with an array of documents objects.
parseDocument($xml);


?>