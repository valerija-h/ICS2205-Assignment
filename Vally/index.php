<?php
//Loading the XML File
$xml = simplexml_load_file("data.xml") or die("Error: Cannot create object");

//Iterating through each thread.
foreach($xml->thread as $thread) {
    //Testing
    echo $thread->DOC->To;
}
?>