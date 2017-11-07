<?php
header("Content-type: application/json");
$mode = $_POST;

$double = false;
if (array_key_exists("subCategory", $mode)){
    if ($mode["subCategory"] == "single") $double = false;
    else if ($mode["subCategory"] == "double") $double = true;
}

$adjectives = ["bitterer","blander","darker","lazier","colder","cooler","dirtier","duller","softer","worse",
"sadder","lighter","kinder","shorter","quieter","nicer","newer","poorer","rougher","shallower",
"slower","smaller","dumber","shorter","thinner","uglier","weaker","narrower","younger"];

$antonyms = ["sweeter","spicier","lighter","busier","hotter","warmer","cleaner","sharper","harder","better",
"happier","heavier","meaner","longer","louder","nastier","older","richer","smoother","deeper",
"faster","larger","smarter","taller","thicker","prettier","stronger","wider","older"];

$vowels = [ 'A', 'E', 'I', 'O', 'U' ];

$SENTENCES = array();

function generate_noun(){
    $alpha = range('A', 'Z');
    $noun = "";
    for($i = 0; $i < mt_rand(3,4); $i++) $noun .= $alpha[mt_rand(0,25)];
    return $noun;
}
$nouns = array();
for ($i = 0; $i < 5; $i++) array_push($nouns, generate_noun());

$adj_rand = rand(0, count($adjectives)-2);

$adjective = $adjectives[$adj_rand];
$antonym = $antonyms[$adj_rand];

//Generate the sentences
$second_set = [0,1,2,3];
shuffle($second_set);

$usedNouns = array();
$usedNounsB = array();

$sentence_vector_values = array();
//GENERATE THE SENTENCES
for ($i = 0; $i < 4; $i++) {
    if (in_array($nouns[$i][0], $vowels)) $str1 = "an"; else $str1 = "a";
    if (in_array($nouns[$i+1][0], $vowels)) $str2 = "an"; else $str2 = "a";
    
    $rand = mt_rand(0,1);

    $var1 = ($rand == 1) ? $str1        :   $str2;
    $var2 = ($rand == 1) ? $i           :   $i + 1;
    $var3 = ($rand == 1) ? $nouns[$i]   :   $nouns[$i + 1];
    $var4 = ($rand == 1) ? $antonym     :   $adjective;
    $var5 = ($rand == 1) ? $str2        :   $str1;
    $var6 = ($rand == 1) ? $i+1         :   $i;
    $var7 = ($rand == 1) ? $nouns[$i+1] :   $nouns[$i];

    $noun1 = $var3;
    $noun2 = $var7;
    
    if (!in_array($var3, $usedNouns)) {
        array_push($usedNouns, $var3);
        $noun1 = "<mark class='word noun' data-type='noun' ".(($double) ? "data-side='a'" : "")." data-value='".$var2."' data-unset>".$var3."</mark>";
    }
    if (!in_array($var7, $usedNouns)){
        array_push($usedNouns, $var7);
        $noun2 = "<mark class='word noun' data-type='noun' ".(($double) ? "data-side='a'" : "")." data-value='".$var6."' data-unset>".$var7."</mark>";
    }
    $sentence = $var1."&nbsp;".$noun1."&nbsp;is&nbsp;".$var4."&nbsp;than&nbsp;".$var5."&nbsp;".$noun2;
    
    array_push($sentence_vector_values, [$nouns[$i], $nouns[$i + 1]]);
    
    //DOUBLE
    if($double) {
    
        if (in_array($nouns[$second_set[$i]][0], $vowels)) $str1 = "an"; else $str1 = "a";
        if (in_array($nouns[$second_set[$i+1]][0], $vowels)) $str2 = "an"; else $str2 = "a";

        $rand = mt_rand(0,1);

        $var1 = ($rand == 1) ? $str1                    :   $str2;
        $var2 = ($rand == 1) ? $second_set[$i]          :   $second_set[$i] + 1;
        $var3 = ($rand == 1) ? $nouns[$second_set[$i]]  :   $nouns[$second_set[$i]+1];
        $var4 = ($rand == 1) ? $antonyms[$adj_rand + 1] :   $adjectives[$adj_rand + 1];
        $var5 = ($rand == 1) ? $str2                    :   $str1;
        $var6 = ($rand == 1) ? $second_set[$i]+1        :   $second_set[$i];
        $var7 = ($rand == 1) ? $nouns[$second_set[$i]+1]:   $nouns[$second_set[$i]];

        $noun1 = $var3;
        $noun2 = $var7;

        /*
        if (!in_array($var3, $usedNounsB)) {
            array_push($usedNounsB, $var3);
            $noun1 = "<mark class='word noun' data-type='noun' ".(($double) ? "data-side='b'" : "")." data-value='".$var2."' data-unset>".$var3."</mark>";
        }
        if (!in_array($var7, $usedNounsB)){
            array_push($usedNounsB, $var7);
            $noun2 = "<mark class='word noun' data-type='noun' ".(($double) ? "data-side='b'" : "")." data-value='".$var6."' data-unset>".$var7."</mark>";
        }*/
        $sentence .= ",&nbsp;but&nbsp;".$var1."&nbsp;".$noun1."&nbsp;is&nbsp;".$var4."&nbsp;than&nbsp;".$var5."&nbsp;".$noun2;
    }
    
    array_push($SENTENCES, $sentence);
}

$ANSWERS = array();





$Patterns = [[1,3], [1,4], [1,5], [2,4], [2,5], [3,5]];
shuffle($Patterns);

//GENERATE THE ANSWERS
for ($i = 0; $i < 4; $i++) {
    if (in_array($nouns[$Patterns[$i][0]-1][0], $vowels)) $str1 = "an"; else $str1 = "a";
    if (in_array($nouns[$Patterns[$i][1]-1][0], $vowels)) $str2 = "an"; else $str2 = "a";
    
    $rand = mt_rand(0,1);

    $var1 = ($rand == 1) ? (($i == 0) ? $str1 : $str2) : (($i == 0) ? $str2 : $str1);
    $var2 = ($rand == 1) ? (($i == 0) ? $nouns[$Patterns[$i][0]-1] : $nouns[$Patterns[$i][1]-1]) : (($i == 0) ? $nouns[$Patterns[$i][1]-1] : $nouns[$Patterns[$i][0]-1]);
    $var3 = ($rand == 1) ? $antonym : $adjective;
    $var4 = ($rand == 1) ? (($i == 0) ? $str2 : $str1) : (($i == 0) ? $str1 : $str2);
    $var5 = ($rand == 1) ? (($i == 0) ? $nouns[$Patterns[$i][1]-1] : $nouns[$Patterns[$i][0]-1]) : (($i == 0) ? $nouns[$Patterns[$i][0]-1] : $nouns[$Patterns[$i][1]-1]);

    $sentence = $var1." ". $var2." is ".$var3." than ".$var4." ".$var5;
    
    if ($rand == 1 && i == 0) $comp = '>';
    else if ($rand == 0 && i == 0) $comp = '<';
    else if ($rand == 1 && i != 0) $comp = '<';
    else if ($rand == 0 && i != 0) $comp = '>';
    
    $values = [$var2, $comp , $var5];
    
    if($double) {
    
        if (in_array($nouns[$second_set[$i]][0], $vowels)) $str1 = "an"; else $str1 = "a";
        if (in_array($nouns[$second_set[$i+1]][0], $vowels)) $str2 = "an"; else $str2 = "a";

        $rand = mt_rand(0,1);

        $var1 = ($rand == 1) ? $str1                    :   $str2;
        $var2 = ($rand == 1) ? $second_set[$i]          :   $second_set[$i] + 1;
        $var3 = ($rand == 1) ? $nouns[$second_set[$i]]  :   $nouns[$second_set[$i]+1];
        $var4 = ($rand == 1) ? $antonyms[$adj_rand + 1] :   $adjectives[$adj_rand + 1];
        $var5 = ($rand == 1) ? $str2                    :   $str1;
        $var6 = ($rand == 1) ? $second_set[$i]+1        :   $second_set[$i];
        $var7 = ($rand == 1) ? $nouns[$second_set[$i]+1]:   $nouns[$second_set[$i]];

        $noun1 = $var3;
        $noun2 = $var7;

        $sentence .= ", but ".$var1." ".$noun1." is ".$var4." than ".$var5." ".$noun2;
    }
    
    $AnswerValues = ["sentence" => $sentence, "values" => $values];
    
        
    array_push($ANSWERS, $AnswerValues);
    
    
}

$CORRECT_ANSWER = $ANSWERS[0];
shuffle($ANSWERS);
shuffle($SENTENCES);

$CORRECT_ANSWER = array_search($CORRECT_ANSWER, $ANSWERS, true);
$RESPONSE = ["adjective" => $adjective, "antonym" => $antonym, "sentences" => $SENTENCES, "answers" => $ANSWERS, "correctAnswer" => $CORRECT_ANSWER, "sentenceVectorValues" => $sentence_vector_values];

echo json_encode($RESPONSE);
//var_dump($RESPONSE);
?>