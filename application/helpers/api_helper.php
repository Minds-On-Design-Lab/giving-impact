<?php
/**
 * API Helpers
 *
 */

/**
 * Generate proper API tokens. Because of backward compatibility, this
 * function ensures that tokens contain both numbers and characters.
 *
 * @return String
 */
function api_generate_token() {
    $tok = substr(sha1(uniqid().time().rand(0,99)), 0, 10);

    if( !preg_match('/[^0-9]/', $tok) ) {
        $alpha = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $letter = $alpha{(rand(0,strlen($alpha)-1))};

        $split = rand(0, strlen($tok)-2);

        $tok = sprintf(
            '%s%s%s',
            substr($tok, 0, $split),
            $letter,
            substr($tok, $split+1)
        );
    }

    return $tok;
}
