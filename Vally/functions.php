<?php
/**
 * Created by PhpStorm.
 * User: valerija
 * Date: 27/12/2018
 * Time: 14:24
 */

//Innclude Porter Stemmer Library by https://tartarus.org/martin/PorterStemmer/php.txt
include 'stemmer.php';

//Stop words from https://gist.github.com/sebleier/554280
$stopWords = ["i", "me", "my", "myself", "we", "our", "ours", "ourselves", "you", "your", "yours", "yourself", "yourselves", "he", "him", "his", "himself", "she", "her", "hers", "herself", "it", "its", "itself", "they", "them", "their", "theirs", "themselves", "what", "which", "who", "whom", "this", "that", "these", "those", "am", "is", "are", "was", "were", "be", "been", "being", "have", "has", "had", "having", "do", "does", "did", "doing", "a", "an", "the", "and", "but", "if", "or", "because", "as", "until", "while", "of", "at", "by", "for", "with", "about", "against", "between", "into", "through", "during", "before", "after", "above", "below", "to", "from", "up", "down", "in", "out", "on", "off", "over", "under", "again", "further", "then", "once", "here", "there", "when", "where", "why", "how", "all", "any", "both", "each", "few", "more", "most", "other", "some", "such", "no", "nor", "not", "only", "own", "same", "so", "than", "too", "very", "s", "t", "can", "will", "just", "don", "should", "now"];

//Some objects that will be needed.
class Document{
    public $senders=[];
    public $keywords=[];
    public $keywordsFreqs=[];
    public $keywordsWeights=[];
}

//In the keywordsWeights state, each keyword will be stored using this.
class Keyword{
    public $word;
    public $weight;
}


//Appends the keywords to an existing document or creates a new one and appends it.
function appendDoc($from, $receiver, $keywords){
    global $documents;
    //Check if senders already exist in a doc and merge that to the array.
    foreach($documents as $document){
        //If the senders already have a document.
        if(in_array($from,$document->senders) && in_array($receiver,$document->senders)){
            //Append them to the current documents and return.
            $document->keywords = array_merge($document->keywords, $keywords);
            return;
        }
    }
    //Otherwise make a new Document and add it to the global array.
    $newDoc = new Document();
    $newDoc->senders = [$from,$receiver];
    $newDoc->keywords = $keywords;
    array_push($documents, $newDoc);
}

function createDoc($from, $receivers, $keywords){
    global $documents;
    foreach($receivers as $receiver) {
        $receiver = str_replace(array('>','<'), '', $receiver);
        //Create first object in documents array else appends/merges a document object.
        if(empty($documents)) {
            $tempDoc = new Document();
            $tempDoc->senders = [$from,$receiver];
            $tempDoc->keywords = $keywords;
            array_push($documents, $tempDoc);
        } else {
            //Either creates a new doc or adds to a previous one.
            appendDoc($from, $receiver, $keywords);
        }
    }
}

//Returns an array of clean key words - without stop words, unwanted characters and stemmed.
function cleanUpMsg($message){
    global $stopWords;
    $keywords = [];
    //Tokenizer to parse text.
    $tok = strtok($message, " ,-()\n<>");
    while ($tok !== false) {
        //Make word lowercase and strip '.' from end of string!
        $tok = rtrim(strtolower($tok),'.');
        //Check if it is a stop word - if not add to the array!
        if (!in_array($tok, $stopWords)) {
            //Porter Stemmer Library - gets the stem of the word.
            $tok = PorterStemmer::Stem($tok);
            array_push($keywords, $tok);
        }
        $tok = strtok(" ,-()\n<>");
    }
    return $keywords;
}

//Parses the xml file and adds Document class objects to the global Documents array.
function parseDocument($xml){
    //Iterating through each thread.
    foreach($xml->thread as $thread) {
        //Iterating through each email.
        foreach($thread->DOC as $email){
            //Obtains the messages in each email - and cleans it up into an array of keywords.
            $text = $email->Text->content;
            $keywords = cleanUpMsg($text);

            //Gets the To, From AND CC.
            $from = str_replace(array('>','<'), '', $email->From);
            //Split them by , and strip for ' ' then append them to an array of recievers.
            $to = explode(',',$email->To);
            //If CC exists split and strip then merge with the current recievers.
            if(isset($email->Cc)){
                $cc = explode(',',$email->Cc);
                $to = array_merge($to,$cc);
            }
            createDoc($from, $to, $keywords);
        }
    }
    //Adds the keywords with frequencies and then keywords with weights to each document.
    addFreq();
    addWeights();
}

//Adds keywords with frequencies to each document, key-value array of word and frequency in document.
function addFreq(){
    global $documents;
    foreach($documents as $document){
        $keywords = $document->keywords;
        $document->keywordsFreqs = array_count_values($keywords);
    }
}

//Adds keywords with weights to each document, the keywords are keyword class objects.
function addWeights(){
    global $documents;
    $totalDoc = sizeof($documents);
    foreach($documents as $document){
        //For each word in the document calculate the weights.
        foreach($document->keywordsFreqs as $key => $value){
            $TFIDF = round($value * log($totalDoc/getDocNo($key)));
            $weighted = new keyWord();
            $weighted->word = $key;
            $weighted->weight = $TFIDF;
            array_push($document->keywordsWeights,$weighted);
        }
    }
}

//Returns number of documents containing the word.
function getDocNo($word){
    $docNumber = 0;
    global $documents;
    foreach($documents as $document){
        if(array_key_exists($word,$document->keywordsFreqs)){
            $docNumber++;
        }
    }
    return $docNumber;
}

?>