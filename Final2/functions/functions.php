<?php
//Include Porter Stemmer Library by https://tartarus.org/martin/PorterStemmer/php.txt
include 'includes/stemmer.php';
//Stop words from https://gist.github.com/sebleier/554280.
$stopWords = ["i", "me", "my", "myself", "we", "our", "ours", "ourselves", "you", "your", "yours", "yourself", "yourselves", "he", "him", "his", "himself", "she", "her", "hers", "herself", "it", "its", "itself", "they", "them", "their", "theirs", "themselves", "what", "which", "who", "whom", "this", "that", "these", "those", "am", "is", "are", "was", "were", "be", "been", "being", "have", "has", "had", "having", "do", "does", "did", "doing", "a", "an", "the", "and", "but", "if", "or", "because", "as", "until", "while", "of", "at", "by", "for", "with", "about", "against", "between", "into", "through", "during", "before", "after", "above", "below", "to", "from", "up", "down", "in", "out", "on", "off", "over", "under", "again", "further", "then", "once", "here", "there", "when", "where", "why", "how", "all", "any", "both", "each", "few", "more", "most", "other", "some", "such", "no", "nor", "not", "only", "own", "same", "so", "than", "too", "very", "s", "t", "can", "will", "just", "don", "should", "now"];

//Object storing the recipients, keywords and number of emails.
class Document{
    public $to=[];
    public $from=[];
    public $keywords=[];
    public $keywordsFreqs=[];
    public $keywordsWeights=[];
    public $emailNo=0;
}

//Object storing the name and weight of a keyword.
class Keyword{
    public $word;
    public $weight;
}

//Returns a string containing just an email from a given string.
function getEmail($string){
    //Split by spaces and ( ).
    $tokens = multiexplode(array(" ",")","("), $string);
    $email = "";
    //Loop through each token to try find the email.
    foreach($tokens as $token){
        //Check if @ exists and last and beginning character isnt "
        if (strpos($token, '@') !== false && substr($token, -1) !== '"' && substr($token, 0, 1) !== '"') {
            $email = $token;
            break;
        }
    }
    //strip and return email and convert to lower case
    return strtolower(trim($email,"\"'"));
}

//Returns an array of valid emails given an array of strings.
function getEmails($strings){
    $emails = [];
    //For each given string, retrieve the email and push into array of emails.
    foreach($strings as $string){
        $email = getEmail($string);
        //If the string containing the email is empty, don't push it.
        if($email != ""){
            array_push($emails,$email);
        }
    }
    return $emails;
}

//Appends the keywords to an existing document or creates a new one and appends them.
function appendDoc($from, $receiver, $keywords){
    global $documents;
    //Check if there a document with the same recipients exist.
    foreach($documents as $document){
        if($document->from == $from && $document->to == $receiver){
            //Append the keywords to the current document and return.
            $document->keywords = array_merge($document->keywords, $keywords);
            $document->emailNo++;
            return;
        }
    }
    //Otherwise make a new Document and add it to the global array.
    $newDoc = new Document();
    $newDoc->to = $receiver;
    $newDoc->from = $from;
    $newDoc->keywords = $keywords;
    $newDoc->emailNo++;
    array_push($documents, $newDoc);
}

//Function to create or update documents.
function createDoc($from, $receivers, $keywords){
    global $documents;
    //For each receiver create or append a document.
    foreach($receivers as $receiver) {
        //Remove the < > characters if present.
        $receiver = str_replace(array('>','<'), '', $receiver);
        //Create the first object in documents array or appends/merges a document object.
        if(empty($documents)) {
            $tempDoc = new Document();
            $tempDoc->to = $receiver;
            $tempDoc->from = $from;
            $tempDoc->keywords = $keywords;
            $tempDoc->emailNo++;
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
    $tok = strtok($message, " \n\r");
    while ($tok !== false) {
        //Make word lowercase and strip '.' from end of string!
        $tok = trim(strtolower($tok),'.,:;\'"()?!-#<>*&%|');
        //Check if it is a stop word - if not add to the array!
        if (!in_array($tok, $stopWords) && !empty($tok)) {
            //Porter Stemmer Library - gets the stem of the word.
            $tok = PorterStemmer::Stem($tok);
            array_push($keywords, $tok);
        }
        $tok = strtok(" \n\r");
    }
    return $keywords;
}

//Parses the SimpleXMLElement object and adds Document class objects to the global Documents array.
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
            //If CC exists split and strip the string then merge with the current recievers.
            if(isset($email->Cc)){
                $cc = explode(',',$email->Cc);
                $to = array_merge($to,$cc);
            }
            $to = getEmails($to);
            $from = getEmail($from);
            //Adds a document to the global document array with given keywords and recipients.
            createDoc($from, $to, $keywords);
        }
    }
    //Adds keywords with frequencies and then keywords with TF-IDF weights to each document object.
    addFreq();
    addWeights();
}

//Adds keywords with frequencies to each document, key-value array of word and frequency in document.
function addFreq(){
    global $documents;
    //For each document, count the frequencies of words in keyword array and create a separate keyword array.
    foreach($documents as $document){
        $keywords = $document->keywords;
        $document->keywordsFreqs = array_count_values($keywords);
    }
}

//Adds keywords with weights to each document, the keywords are keyword class objects.
function addWeights(){
    global $documents;
    $totalDoc = sizeof($documents); //Total number of documents.
    foreach($documents as $document){
        //For each word in the document calculate the weights.
        foreach($document->keywordsFreqs as $key => $value){
            //Calculates the TF-IDF weight for a keyword.
            $TFIDF = round($value * log($totalDoc/getDocNo($key)));
            $weighted = new keyWord();
            $weighted->word = $key;
            $weighted->weight = $TFIDF;
            //Adds a keyword and it's TF-IDF weight to the keyword weight array of the current document.
            array_push($document->keywordsWeights,$weighted);
        }
    }
}

//Returns number of documents containing a keyword.
function getDocNo($word){
    $docNumber = 0;
    global $documents;
    //Goes through each document and checks whether the keyword is present.
    foreach($documents as $document){
        if(array_key_exists($word,$document->keywordsFreqs)){
            $docNumber++;
        }
    }
    return $docNumber;
}

//Splits a string by multiple delimiters.
function multiexplode ($delimiters,$string) {
    $temp = str_replace($delimiters, $delimiters[0], $string);
    return explode($delimiters[0], $temp);
}

?>
