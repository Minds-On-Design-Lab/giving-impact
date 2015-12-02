<?php
/**
 * Authentication helpers
 */


/**
 * Fetch the currently logged in user. If no valid user is logged in, or no
 * user is logged in, FALSE is returned
 *
 * @return Object User_model or FALSE
 */
function authorized_user() {
    $CI = get_instance();

    return $CI->auth->authorized_user();
}