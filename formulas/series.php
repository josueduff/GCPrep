<?php
header("Content-type: application/json");
$SEQUENCE = array();
$ANSWERS = array();

function SimpleLinear1() {
    global $SEQUENCE, $ANSWERS;
    
    $Alpha = range('A', 'Z');
    $ExtendedAlpha = array();
    foreach ($Alpha as $key => $str){ array_push($ExtendedAlpha, $Alpha[$key] . $Alpha[$key]); }
    $ExtendedAlpha = array_merge($Alpha, $ExtendedAlpha);

    $x = mt_rand(3,5);
    $c = mt_rand(0,20);

    while ((($x * $c + 6) > 20) && ((($x * $c + 6) + 2 * $x + 1) > 51)) {
        $x = mt_rand(3,5);
        $c = mt_rand(0,20);
    }
    for ($i = 1; $i < 7; $i++) { array_push($SEQUENCE, $ExtendedAlpha[$x * $i + $c]); }
    $ANSWERS = [$ExtendedAlpha[$x * 7 + $c],
                $ExtendedAlpha[($x * 6 + $c) + $x +1 ],
                $ExtendedAlpha[($x * 6 + $c) + $x +2 ],
                $ExtendedAlpha[($x * 6 + $c) + 2 * $x + 1]];
}

function SimpleLinear2() {
    global $SEQUENCE, $ANSWERS;

    function LT($num, $denom) {
        for ($j = $num; $j > 0; $j--){
            if (($num % $j == 0) && ($denom % $j == 0)){ return $j; }
        }
     }
        
    $x = mt_rand(2,19);
    $Numerator = array();
    $Denominator = array();
    
    for ($i = 1; $i < 10; $i++){
        $k = LT($i, $x*4);
        if ($i < 7) {
            array_push($Numerator, $i / $k);
            array_push($Denominator, ($x * 4) / $k);
            array_push($SEQUENCE, ($i / $k) . "/" . (($x * 4) / $k));
        }
        else {            
            array_push($ANSWERS, ($i / $k) . "/" . (($x * 4) / $k));
        }
    }
    $k = LT(($Numerator[4]+$Numerator[5]), ($Denominator[4]+$Denominator[5]));
    array_push($ANSWERS, ($Numerator[4]+$Numerator[5])/$k ."/". ($Denominator[4]+$Denominator[5])/$k);
}

function AlternatingLinear(){
    global $SEQUENCE, $ANSWERS;
    
    $y = mt_rand(2,19);
    $y2 = mt_rand(2,19); if (mt_rand(0,1) == 1) { $y2 *= -1;}
    $k = mt_rand(0,9); if (mt_rand(0,1) == 1) { $k *= -1;}
    $x = $y2 + $k;
    $d = mt_rand(6, 2999);
    $l = mt_rand(0,99); if (mt_rand(0,1) == 1) { $l *= -1;}
    $c = abs($d + $l);
    
    for ($i = 1; $i < 4; $i++)
    {
        array_push($SEQUENCE, $x * $i + $c);
        array_push($SEQUENCE, $y * $i + $d);
    }
    
    $ANSWERS = [$x * 4 + $c, $SEQUENCE[5]-$SEQUENCE[4]+$SEQUENCE[5], $SEQUENCE[4]+$y, $SEQUENCE[5]+$y];
}

function Quadratic(){
    global $SEQUENCE, $ANSWERS;
    $x = mt_rand(1,9);
    $y = mt_rand(6,24);
    
    while ((($x % 2 != 0) xor ($y % 2 != 0)) || (($x % 2 == 0) xor ($y % 2 == 0))){
        $x = mt_rand(1,9);
        $y = mt_rand(6,24);      
    }

    $c = mt_rand (100,4999);
  	if (mt_rand(0,1) == 1) { $y *= -1; }
    
    for ($i = 1; $i < 8; $i++){
        if($i == 7) {
        	array_push($ANSWERS, ($x * ($i * $i) + $y * $i)/2+$c);
        }
        else {
        	array_push($SEQUENCE, ($x * ($i * $i) + $y * $i)/2+$c);
        }        
    }
    
    array_push($ANSWERS, $SEQUENCE[1]-$SEQUENCE[0]+$SEQUENCE[5]);
    array_push($ANSWERS, $SEQUENCE[4]-$SEQUENCE[3]+$SEQUENCE[5]);
    array_push($ANSWERS, $SEQUENCE[5]-$SEQUENCE[4]+$SEQUENCE[5]);
}

function Recursive1() {
    global $SEQUENCE, $ANSWERS;

    array_push($SEQUENCE, mt_rand(5,15));
    array_push($SEQUENCE, $a[0] + mt_rand(2,12));
    
    for ($i = 2; $i < 6; $i++) {
        array_push($SEQUENCE, round($SEQUENCE[$i-1]/$SEQUENCE[$i-2], 3));
    }

    $ANSWERS = [round($SEQUENCE[5]/$SEQUENCE[4], 3),
                round($$SEQUENCE[1] + $SEQUENCE[5], 3),
                round($SEQUENCE[3]/$SEQUENCE[5], 3), 
                round($SEQUENCE[0]+$SEQUENCE[1]+$SEQUENCE[2]+$SEQUENCE[3]+$SEQUENCE[4]+$SEQUENCE[5], 3)];
}

function Recursive2() {
    global $SEQUENCE, $ANSWERS;
    
    array_push($SEQUENCE, mt_rand(1,500));
    array_push($SEQUENCE, mt_rand(1,500));
    array_push($SEQUENCE, mt_rand(1,500));
    
    $sign = 1;    
    if (mt_rand(0,1) == 0) { $sign = -1;}    

    for ($i = 3; $i < 6; $i++) {
        array_push($SEQUENCE, $SEQUENCE[$i-3] + ($SEQUENCE[$i-2] * $sign) + ($SEQUENCE[$i-1] * $sign));
    }
    
    array_push($ANSWERS, $SEQUENCE[6-3] + ($SEQUENCE[6-2] * $sign) + ($SEQUENCE[6-1] * $sign));
    array_push($ANSWERS, $SEQUENCE[5] - $SEQUENCE[4] + $SEQUENCE[5]);
    array_push($ANSWERS, $SEQUENCE[1] - $SEQUENCE[0] + $SEQUENCE[5]);
    array_push($ANSWERS, mt_rand(1,1000));    
}

//SimpleLinear1();

//PROBABILITY FOR TEST MODE ONLY
/*
$probability = [0, 1,
                2, 2, 2, 2, 2, 2, 2, 2,
                3, 3, 3, 3, 3, 3,
                4, 5];

$prob_select = mt_rand(0,17);

if ($probability[$prob_select] == 0) { SimpleLinear1(); }
elseif ($probability[$prob_select] == 1) { SimpleLinear2(); }
elseif ($probability[$prob_select] == 2) { AlternatingLinear(); }
elseif ($probability[$prob_select] == 3) { Quadratic(); }
elseif ($probability[$prob_select] == 4) { Recursive1(); }
elseif ($probability[$prob_select] == 5) { Recursive2(); }
*/
$subCategory = $_POST["subCategory"];

if ($subCategory == "simple"){
    if (mt_rand(0,1) == 1)
        SimpleLinear1();
    else
        SimpleLinear2();
}
else if ($subCategory == "alternating")
    AlternatingLinear();
else if ($subCategory == "quadratic")
    Quadratic();
else if ($subCategory == "recursive"){
    if (mt_rand(0,1) == 1)
        Recursive1();
    else
        Recursive2();
}


//$SEQUENCE = [$_POST["subCategory"], "a", null, null, null, null];
//$ANSWERS = ["Answer1", "Answer2", "Answer3", "Answer4"];

$CORRECT_ANSWER = $ANSWERS[0];
shuffle($ANSWERS);
$CORRECT_ANSWER = array_search($CORRECT_ANSWER, $ANSWERS, true);

$RESPONSE = ["sequence" => $SEQUENCE, "answers" => $ANSWERS, "correctAnswer" => $CORRECT_ANSWER];
echo json_encode($RESPONSE);
?>