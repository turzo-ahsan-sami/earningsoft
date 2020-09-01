<?php


function convert_number_to_words($number)
{

    //$no = round($number);
    //$point = round($number - $no, 2) * 100;
    //number_format('11234.50',2,'.','');
    $no = number_format($number, 2, '.', '');
    $point = explode('.', $no);
    if ((int)$point[1] > 0)
        $point = $point[1];
    else
        $point = '';

    $hundred = null;
    $digits_1 = strlen($no);
    $i = 0;
    $str = array();
    $words = array('0' => '', '1' => 'one', '2' => 'two',
        '3' => 'three', '4' => 'four', '5' => 'five', '6' => 'six',
        '7' => 'seven', '8' => 'eight', '9' => 'nine',
        '10' => 'ten', '11' => 'eleven', '12' => 'twelve',
        '13' => 'thirteen', '14' => 'fourteen',
        '15' => 'fifteen', '16' => 'sixteen', '17' => 'seventeen',
        '18' => 'eighteen', '19' => 'nineteen', '20' => 'twenty',
        '30' => 'thirty', '40' => 'forty', '50' => 'fifty',
        '60' => 'sixty', '70' => 'seventy',
        '80' => 'eighty', '90' => 'ninety');
    $digits = array('', 'hundred', 'thousand', 'lac', 'crore');
    while ($i < $digits_1) {
        $divider = ($i == 2) ? 10 : 100;
        $number = floor($no % $divider);
        $no = floor($no / $divider);
        $i += ($divider == 10) ? 1 : 2;
        if ($number) {
            $plural = (($counter = count($str)) && $number > 9) ? '' : null;
            //$plural='';
            if ($point != '')
                $hundred = null;
            else
                $hundred = ($counter == 1 && $str[0]) ? 'and ' : null;
            $str [] = ($number < 21) ? $words[$number] .
                " " . $digits[$counter] . $plural . " " . $hundred :
                $words[floor($number / 10) * 10]
                . " " . $words[$number % 10] . " "
                . $digits[$counter] . $plural . " " . $hundred;
        } else
            $str[] = null;
    }
    $str = array_reverse($str);
    $result = implode('', $str);

    /* for paisa */
    $hundred_2 = null;
    $afterPoint = $point;
    $digits_2 = strlen($point);
    $i_2 = 0;
    $str_2 = array();
    while ($i_2 < $digits_2) {
        $divider_2 = ($i_2 == 2) ? 10 : 100;
        $number_2 = floor($point % $divider_2);
        $point = floor($point / $divider_2);
        $i_2 += ($divider_2 == 10) ? 1 : 2;
        if ($number_2) {
            $plural_2 = (($counter_2 = count($str_2)) && $number_2 > 9) ? '' : null;
            //$plural='';
            $hundred_2 = ($counter_2 == 1 && $str_2[0]) ? 'and ' : null;
            $str_2 [] = ($number_2 < 21) ? $words[$number_2] .
                " " . $digits_2[$counter_2] . $plural_2 . " " . $hundred_2 :
                $words[floor($number_2 / 10) * 10]
                . " " . $words[$number_2 % 10] . " "
                . $digits_2[$counter_2] . $plural_2 . " " . $hundred_2;
        } else
            $str_2[] = null;
    }
    $str_2 = array_reverse($str_2);
    $paisa = implode('', $str_2);
    /* for paisa */

    /* $points = ($point) ?
      $words[$point / 10] . " " .
      $words[$point = $point % 10] : ''; */
    $ff = $result . " ";
    if ((int)$afterPoint > 0) {
        $ff = $ff . " and " . $paisa . " Paisa";
    }
    /* if(trim($points)!=''){
      $ff = $ff." and ".$points . " Paisa";
      } */
    $ff .= ' Only';
    return ucwords($ff);
}

?>
