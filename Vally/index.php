<?php
//Innclude Porter Stemmer Library by https://tartarus.org/martin/PorterStemmer/php.txt
include 'stemmer.php';

//Loading the XML File
$xml = simplexml_load_file("data.xml") or die("Error: Cannot create object");

//Stop words from https://gist.github.com/sebleier/554280
$stopWords = ["i", "me", "my", "myself", "we", "our", "ours", "ourselves", "you", "your", "yours", "yourself", "yourselves", "he", "him", "his", "himself", "she", "her", "hers", "herself", "it", "its", "itself", "they", "them", "their", "theirs", "themselves", "what", "which", "who", "whom", "this", "that", "these", "those", "am", "is", "are", "was", "were", "be", "been", "being", "have", "has", "had", "having", "do", "does", "did", "doing", "a", "an", "the", "and", "but", "if", "or", "because", "as", "until", "while", "of", "at", "by", "for", "with", "about", "against", "between", "into", "through", "during", "before", "after", "above", "below", "to", "from", "up", "down", "in", "out", "on", "off", "over", "under", "again", "further", "then", "once", "here", "there", "when", "where", "why", "how", "all", "any", "both", "each", "few", "more", "most", "other", "some", "such", "no", "nor", "not", "only", "own", "same", "so", "than", "too", "very", "s", "t", "can", "will", "just", "don", "should", "now"];

$documents = [];

class Document{
    public $senders=[];
    public $emails=[];
    public $final=[];
}
class Keyword{
    public $str;
    public $freq;
}

//Iterating through each thread.
foreach($xml->thread as $thread) {
    //Iterating through each email.
    foreach($thread->DOC as $email){
        // -------------- Obtaining Keywords -------------- /
        //The messages in each email - not Quotes.
        $text = $email->Text->content;
        //Parse the messages by delimiters and store them in temporary array.
        $temp = [];
        //Tokenizer to parse text.
        $tok = strtok($text, " ,-()\n<>");
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

        foreach($to as $receiver) {
            $reciever = str_replace(array('>','<'), '',$receiver);
            //Create first object in documents array else append object.
            if(empty($documents)) {
                $tempDoc = new Document();
                $tempDoc->senders = [$from,$receiver];
                $tempDoc->emails = $temp;
                array_push($documents,$tempDoc);
                print_r($documents);
            }
        }



        //Testing print final array.
        //print_r($temp);

        //Add the PHP array to a JS Array ?>
        <!-- <script>
            var object = <?php echo json_encode($temp) ?>;
            console.log(object);
        </script> -->
<?php
    }
}
?>
