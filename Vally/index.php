<?php
//Loading the XML File
$xml = simplexml_load_file("data.xml") or die("Error: Cannot create object");

//Stop words from https://gist.github.com/sebleier/554280
$stopWords = ["i", "me", "my", "myself", "we", "our", "ours", "ourselves", "you", "your", "yours", "yourself", "yourselves", "he", "him", "his", "himself", "she", "her", "hers", "herself", "it", "its", "itself", "they", "them", "their", "theirs", "themselves", "what", "which", "who", "whom", "this", "that", "these", "those", "am", "is", "are", "was", "were", "be", "been", "being", "have", "has", "had", "having", "do", "does", "did", "doing", "a", "an", "the", "and", "but", "if", "or", "because", "as", "until", "while", "of", "at", "by", "for", "with", "about", "against", "between", "into", "through", "during", "before", "after", "above", "below", "to", "from", "up", "down", "in", "out", "on", "off", "over", "under", "again", "further", "then", "once", "here", "there", "when", "where", "why", "how", "all", "any", "both", "each", "few", "more", "most", "other", "some", "such", "no", "nor", "not", "only", "own", "same", "so", "than", "too", "very", "s", "t", "can", "will", "just", "don", "should", "now"];

$words = [];

//Iterating through each thread.
foreach($xml->thread as $thread) {
    //Iterating through each email.
    foreach($thread->DOC as $email){
        //The messages in each email - not Quotes.
        $text = $email->Text->content;
        //Parse the messages by space and store them in temporary array.
        $temp = [];
        //Tokenizer to parse text.
        $tok = strtok($text, " ,-()\n");
        while ($tok !== false) {
            //Make word lowercase and strip '.' from end of string!
            $tok = rtrim(strtolower($tok),'.');
            //Check if it is a stop word - if not add to the array!
            if (!in_array($tok, $stopWords)) {
                array_push($temp, $tok);
            }
            $tok = strtok(" ,-()\n");
        }
        //Testing print final array.
        print_r($temp);
    }
}

/* OLD ATTEMPT
$temp = explode(" ", $text);
print_r($temp);
echo "<br><br><br>";
//If it is one of stop words, ignore.
$temp = array_diff($temp, $stopWords);
print_r($temp);
echo "<br><br><br>"; */
?>