<?php
//Innclude Porter Stemmer Library by https://tartarus.org/martin/PorterStemmer/php.txt
include 'stemmer.php';

//Loading the XML File
$xml = simplexml_load_file("data.xml") or die("Error: Cannot create object");

//Stop words from https://gist.github.com/sebleier/554280
$stopWords = ["i", "me", "my", "myself", "we", "our", "ours", "ourselves", "you", "your", "yours", "yourself", "yourselves", "he", "him", "his", "himself", "she", "her", "hers", "herself", "it", "its", "itself", "they", "them", "their", "theirs", "themselves", "what", "which", "who", "whom", "this", "that", "these", "those", "am", "is", "are", "was", "were", "be", "been", "being", "have", "has", "had", "having", "do", "does", "did", "doing", "a", "an", "the", "and", "but", "if", "or", "because", "as", "until", "while", "of", "at", "by", "for", "with", "about", "against", "between", "into", "through", "during", "before", "after", "above", "below", "to", "from", "up", "down", "in", "out", "on", "off", "over", "under", "again", "further", "then", "once", "here", "there", "when", "where", "why", "how", "all", "any", "both", "each", "few", "more", "most", "other", "some", "such", "no", "nor", "not", "only", "own", "same", "so", "than", "too", "very", "s", "t", "can", "will", "just", "don", "should", "now"];

//Where the document objects will be stored.
$documents = [];

//Some objects that will be needed.
class Document{
    public $senders=[];
    public $emails=[];
    public $freqs=[];
    public $final=[];
}
class Keyword{
    public $str;
    public $freq;
}

parseDocument($xml);
addFreq();
getWeights();
print_r($documents);


//Appends the keywords to an existing document or creates a new one and appends it.
function appendToDoc($from,$receiver,$temp){
    global $documents;
    //Check if senders already exist in a doc and merge that to the array.
    foreach($documents as $document){
        //If the senders already have a document.
        if(in_array($from,$document->senders) && in_array($receiver,$document->senders)){
            //Append them to the current documents and return.
            $document->emails = array_merge($document->emails,$temp);
            return;
        }
    }
    //Otherwise make a new Document and add it to the global array.
    $tempDoc = new Document();
    $tempDoc->senders = [$from,$receiver];
    $tempDoc->emails = $temp;
    array_push($documents,$tempDoc);
}

function addToDoc($to,$from,$temp){
    global $documents;
    foreach($to as $receiver) {
        $receiver = str_replace(array('>','<'), '',$receiver);
        //Create first object in documents array else appends/merges a document object.
        if(empty($documents)) {
            $tempDoc = new Document();
            $tempDoc->senders = [$from,$receiver];
            $tempDoc->emails = $temp;
            array_push($documents,$tempDoc);
        } else {
            //Either creates a new doc or adds to a previous one.
            appendToDoc($from,$receiver,$temp);
        }
    }
}

//Returns an array of clean key words - without stop words, unwanted characters and stemmed.
function cleanUpMsg($message){
    global $stopWords;
    $temp = [];
    //Tokenizer to parse text.
    $tok = strtok($message, " ,-()\n<>");
    while ($tok !== false) {
        //Make word lowercase and strip '.' from end of string!
        $tok = rtrim(strtolower($tok),'.');
        //Check if it is a stop word - if not add to the array!
        if (!in_array($tok, $stopWords)) {
            //Porter Stemmer Library - gets the stem of the word.
            $tok = PorterStemmer::Stem($tok);
            array_push($temp, $tok);
        }
        $tok = strtok(" ,-()\n<>");
    }
    return $temp;
}

function parseDocument($xml){
    //Function that returns a document object.
    //Iterating through each thread.
    foreach($xml->thread as $thread) {
        //Iterating through each email.
        foreach($thread->DOC as $email){
            // -------------- Obtaining Keywords -------------- /
            //The messages in each email - not Quotes.
            $text = $email->Text->content;
            $temp = cleanUpMsg($text);

            // ---------------  Choosing Appropriate Document ---------------
            //Get the To, From AND CC
            $from = str_replace(array('>','<'), '', $email->From);
            //Split them by , and strip for ' ' then append them to an array of recievers.
            $to = explode(',',$email->To);
            //If CC exists split and strip then merge with to.
            if(isset($email->Cc)){
                $cc = explode(',',$email->Cc);
                $to = array_merge($to,$cc);
            }
            addToDoc($to,$from,$temp);
        }
    }
}

//Adds frequencies to each document, kye-value array of word and frequency in document.
function addFreq(){
    global $documents;
    foreach($documents as $document){
        $keywords = $document->emails;
        $document->freqs = array_count_values($keywords);
    }
}

//Returns number of documents containing the word.
function getOverallFreq($word){
    $docNumber = 0;
    // $totalFreq = 0;
    global $documents;
    foreach($documents as $document){
        //If word exists ind ocument, add it's frequnecy to total frequency.
        if(array_key_exists($word,$document->freqs)){
            // $totalFreq += $document->freqs[$word];
            $docNumber++;
        }
    }
    return $docNumber;
}

function  getWeights(){
    global $documents;
    $totalDoc = sizeof($documents);
    foreach($documents as $document){
        //For each word in the document calculate the weights.
        foreach($document->freqs as $key => $value){
            $weighted = new keyWord();
            $weighted->str = $key;
            $TFIDF = round($value * log($totalDoc/getOverallFreq($key)));
            $weighted->freq = $TFIDF;
            array_push($document->final,$weighted);
        }
    }
}
?>
