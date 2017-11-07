<?php
//ini_set('display_errors', 'On');
//error_reporting(E_ALL | E_STRICT);

header("Content-type: application/json");

$double = true;
$mode = $_POST;
if (array_key_exists("subCategory", $mode)){
    if ($mode["subCategory"] == "single") $double = false;
    else if ($mode["subCategory"] == "double") $double = true;
}

$adjectives = ["bitterer","blander","darker","lazier","colder","cooler","dirtier","duller","softer","worse",
"sadder","lighter","kinder","shorter","quieter","nicer","newer","poorer","rougher","shallower",
"slower","smaller","dumber","shorter","thinner","uglier","weaker","narrower","younger"];

$antonyms = ["sweeter","spicier","lighter","busier","hotter","warmer","cleaner","sharper","harder","better",
"happier","heavier","meaner","longer","louder","nastier","older","richer","smoother","deeper",
"faster","larger","smarter","taller","thicker","pttier","stronger","wider","older"];

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


if (!$double) {
    $sentence_vector_values = array();
    $ANSWERS = array();

    $single_ans_patterns = [[1,3], [1,4], [1,5], [2,4], [2,5], [3,5]];
    shuffle($single_ans_patterns);

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
            $noun1 = "<mark class='word noun' data-type='noun' data-value='".$var2."' data-unset>".$var3."</mark>";
        }
        if (!in_array($var7, $usedNouns)){
            array_push($usedNouns, $var7);
            $noun2 = "<mark class='word noun' data-type='noun' data-value='".$var6."' data-unset>".$var7."</mark>";
        }
        $sentence = $var1."&nbsp;".$noun1."&nbsp;is&nbsp;".$var4."&nbsp;than&nbsp;".$var5."&nbsp;".$noun2;

        array_push($sentence_vector_values, [$nouns[$i], $nouns[$i + 1]]);

        array_push($SENTENCES, $sentence);
    }

    //GENERATE THE ANSWERS
    for ($i = 0; $i < 4; $i++) {
        if (in_array($nouns[$single_ans_patterns[$i][0]-1][0], $vowels)) $str1 = "an"; else $str1 = "a";
        if (in_array($nouns[$single_ans_patterns[$i][1]-1][0], $vowels)) $str2 = "an"; else $str2 = "a";

        $rand = mt_rand(0,1);

        $var1 = ($rand == 1) ? (($i == 0) ? $str1 : $str2) : (($i == 0) ? $str2 : $str1);
        $var2 = ($rand == 1) ? (($i == 0) ? $nouns[$single_ans_patterns[$i][0]-1] : $nouns[$single_ans_patterns[$i][1]-1]) : (($i == 0) ? $nouns[$single_ans_patterns[$i][1]-1] : $nouns[$single_ans_patterns[$i][0]-1]);
        $var3 = ($rand == 1) ? $antonym : $adjective;
        $var4 = ($rand == 1) ? (($i == 0) ? $str2 : $str1) : (($i == 0) ? $str1 : $str2);
        $var5 = ($rand == 1) ? (($i == 0) ? $nouns[$single_ans_patterns[$i][1]-1] : $nouns[$single_ans_patterns[$i][0]-1]) : (($i == 0) ? $nouns[$single_ans_patterns[$i][0]-1] : $nouns[$single_ans_patterns[$i][1]-1]);

        $sentence = $var1." ". $var2." is ".$var3." than ".$var4." ".$var5;

        $comp = ($rand == 1) ? ">" : "<";
        
        //if ($rand == 1 && $i == 0) $comp = '>';
        //else if ($rand == 0 && $i == 0) $comp = '<';
        //else if ($rand == 1 && $i != 0) $comp = '<';
        //else if ($rand == 0 && $i != 0) $comp = '>';

        $values = [$var2, $comp , $var5];
        
        $AnswerValues = ["sentence" => $sentence, "values" => $values];

        array_push($ANSWERS, $AnswerValues);
    }

    $CORRECT_ANSWER = $ANSWERS[0];
    shuffle($ANSWERS);
    shuffle($SENTENCES);

    $CORRECT_ANSWER = array_search($CORRECT_ANSWER, $ANSWERS, true);
    $RESPONSE = ["adjective" => $adjective, "antonym" => $antonym, "sentences" => $SENTENCES, "answers" => $ANSWERS, "correctAnswer" => $CORRECT_ANSWER, "sentenceVectorValues" => $sentence_vector_values];

    echo json_encode($RESPONSE);
}

/*
$all_patterns = [[0,1], [0,2], [0,3], [0,4], [1, 2], [1, 3], [1, 4], [2, 3], [2, 4], [3, 4]];

$sentence_patterns = [];

/*Generate Patterns* /
$set = [0, 1, 2, 3, 4];

//Step 1
$step1 = mt_rand(0,4);

//Step 2
$step2 = $set;
unset($step2[$step1]); //Remove the $step1 value from $step2 array;
sort($step2);//Reset the keys

$rand1 = mt_rand(0,3);
$rand2 = mt_rand(0,3);
while ($rand2 == $rand1) $rand2 = mt_rand(0,3);//$rand2 != $rand1

array_push($sentence_patterns, [$step1, $step2[$rand1]]);//Pair 1
array_push($sentence_patterns, [$step1, $step2[$rand2]]);//Pair 2

for ($i = 0; $i < 4; $i++) if ($step2[$i] == $sentence_patterns[0][1]) unset($step2[$i]);
sort($step2);
for ($i = 0; $i < 3; $i++) if ($step2[$i] == $sentence_patterns[1][1]) unset($step2[$i]);
sort($step2);

//Step 3
array_push($sentence_patterns, $step2);//Pair 3

//Step4
$step4_set = $set;
$step3_index = mt_rand(0,1);
unset($step4_set[$sentence_patterns[2][0]]);
sort($step4_set);

for ($i = 0; $i < 4; $i++) if ($step4_set[$i] == $sentence_patterns[2][1]) unset($step4_set[$i]);
sort($step4_set);
array_push($sentence_patterns, [$sentence_patterns[2][$step3_index], $step4_set[mt_rand(0,1)]]);//Pair 4
//print_r($sentence_patterns);

/*Expansion algorigthm* /
$deduced_patterns = $sentence_patterns;//[[2,3], [2,4], [0,1], [1,2]];
for ($i = 0; $i < count($deduced_patterns); $i++){
    for ($j = 0; $j < count($deduced_patterns); $j++) {
        if ($deduced_patterns[$i][1] == $deduced_patterns[$j][0]) {
            array_push( $deduced_patterns, [$deduced_patterns[$i][0], $deduced_patterns[$j][1]]);
        }
    }
}
//print_r($deduced_patterns);

$undeducable_patterns = [];
for ($i = 0; $i < count($all_patterns); $i++) {
    if (!in_array([$all_patterns[$i][0], $all_patterns[$i][1]], $deduced_patterns) && !in_array([$all_patterns[$i][1], $all_patterns[$i][0]], $deduced_patterns)) {
        array_push($undeducable_patterns, $all_patterns[$i]);
    }
}
*/
//print_r($undeducable_patterns);

//array_push($SENTENCES, "abc");
//$RESPONSE = ["sentence" => $SENTENCES];

//var_dump($RESPONSE);
?>

