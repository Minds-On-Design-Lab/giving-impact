<?php
/**
 * Number helper extension
 *
 * @extends  number_helper
 */

/**
 * Output int, float or string as properly formatted to decimal float. Will
 * properly round extra decimal places
 * @example
 *     echo format_money('2'); // 2.00
 *     echo format_money(1.33); // 1.33
 *     echo format_money(43); // 43.00
 *     echo format_money(43, false); // 43
 *     echo format_money(34.56789); // 34.57
 * @param  Mixed  $float  Can be int, string or float
 * @param  boolean $dec   If FALSE will not append '.00' to while INT
 * @return float
 */
function format_money($float, $dec = false) {

    $float = round($float, 2);

    if( strpos($float, '.') === false ) {
        $parts = array($float, '00');
    } else {
        $parts = explode('.', $float);
    }
    $dollars = $parts[0];

    $dollars = strrev($dollars);
    $out = '';
    for( $i=0; $i<strlen($dollars); $i++ ) {
        $out .= $dollars{$i};
        if( ($i+1)%3 === 0 ) {
            $out .= ',';
        }
    }

    $out = strrev(trim($out, ','));
    if( strlen($parts[1]) == 1 ) {
        $parts[1] .= '0';
    }
    if( $parts[1] != '00' ) {
        return $out.'.'.$parts[1];
    }

    if( $dec == true ) {
        return $out.'.00';
    }

    return $out;
}

/**
 * Force both string and integer dates into different format. Useful
 * if you're unsure of the date format
 *
 * @example
 *     echo format_date('Monday March 11th 2013', 'm/d/Y'); //03/11/2013
 *     echo format_date(1363056229, 'm/d/Y'); // 03/11/2013
 * @param  Mixed $str
 * @param  String $fmt
 * @return String
 */
function format_date($str, $fmt) {
    if( is_int($str) ) {
        return date($fmt, $str);
    }

    $int = strtotime($str);

    return date($fmt, $int);
}
