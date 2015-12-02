<?php defined('BASEPATH') OR exit('No direct script access allowed');
if (!$config) {
    $config = array();
}
call_user_func(function() use (&$config) {

    $config['gi-user-agent']    = 'gi-official-2.0';

    $config['gi-api-key']       = false;

    $path = '';
    if ($_SERVER['PHP_SELF']) {
        $path = trim(str_replace('index.php', '', $_SERVER['PHP_SELF']), '/');
        $path .= '/api';
    }

    $config['gi-api-location']  = 'http://'.$_SERVER['HTTP_HOST'].$path;

    if ($_SERVER['REMOTE_ADDR'] == '10.0.2.2') {
        $config['gi-api-location']  = str_replace($_SERVER['HTTP_HOST'], '10.0.2.2:'.$_SERVER['SERVER_PORT'], $config['gi-api-location']);
    }
});