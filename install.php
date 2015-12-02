<?php

require_once 'vendor/autoload.php';
$phinx = require 'vendor/robmorgan/phinx/app/phinx.php';
$license = file_get_contents(__DIR__.'/LICENSE.txt');

$logo = <<<END
   ___  _       _               ___                         _
  / __|(_)__ __(_) _ _   __ _  |_ _| _ __   _ __  __ _  __ | |_
 | (_ || |\ V /| || ' \ / _` |  | | | '  \ | '_ \/ _` |/ _||  _|
  \___||_| \_/ |_||_||_|\__, | |___||_|_|_|| .__/\__,_|\__| \__|
                        |___/              |_|



END;

class Colors {
    private $foreground_colors = array();
    private $background_colors = array();

    public function __construct() {
        // Set up shell colors
        $this->foreground_colors['black'] = '0;30';
        $this->foreground_colors['dark_gray'] = '1;30';
        $this->foreground_colors['blue'] = '0;34';
        $this->foreground_colors['light_blue'] = '1;34';
        $this->foreground_colors['green'] = '0;32';
        $this->foreground_colors['light_green'] = '1;32';
        $this->foreground_colors['cyan'] = '0;36';
        $this->foreground_colors['light_cyan'] = '1;36';
        $this->foreground_colors['red'] = '0;31';
        $this->foreground_colors['light_red'] = '1;31';
        $this->foreground_colors['purple'] = '0;35';
        $this->foreground_colors['light_purple'] = '1;35';
        $this->foreground_colors['brown'] = '0;33';
        $this->foreground_colors['yellow'] = '1;33';
        $this->foreground_colors['light_gray'] = '0;37';
        $this->foreground_colors['white'] = '1;37';

        $this->background_colors['black'] = '40';
        $this->background_colors['red'] = '41';
        $this->background_colors['green'] = '42';
        $this->background_colors['yellow'] = '43';
        $this->background_colors['blue'] = '44';
        $this->background_colors['magenta'] = '45';
        $this->background_colors['cyan'] = '46';
        $this->background_colors['light_gray'] = '47';
    }

    // Returns colored string
    public function getColoredString($string, $foreground_color = null, $background_color = null) {
        $colored_string = "";

        // Check if given foreground color found
        if (isset($this->foreground_colors[$foreground_color])) {
            $colored_string .= "\033[" . $this->foreground_colors[$foreground_color] . "m";
        }
        // Check if given background color found
        if (isset($this->background_colors[$background_color])) {
            $colored_string .= "\033[" . $this->background_colors[$background_color] . "m";
        }

        // Add string and end coloring
        $colored_string .=  $string . "\033[0m";

        return $colored_string;
    }

    // Returns all foreground color names
    public function getForegroundColors() {
        return array_keys($this->foreground_colors);
    }

    // Returns all background color names
    public function getBackgroundColors() {
        return array_keys($this->background_colors);
    }
}

$colorizer = new Colors;
\cli\Colors::enable();

$errorDump = function($message) use ($colorizer) {
    \cli\line($colorizer->getColoredString(' DANG ', 'white', 'red'));
    \cli\line($message);
    \cli\line("\n");
    exit(1);
};

\cli\out_padded($logo);
\cli\line("");

\cli\line("This product is licensed under The MIT License (MIT)");
\cli\line("");
\cli\line($license);
\cli\line("");

if (strtolower(\cli\choose('Do you agree to the license terms?', 'Yn', 'n')) !== 'y') {
    exit(1);
}

\cli\line("\n\n");


\cli\line('Checking requirements...');

$checks = array(
    'mysql   '  => function_exists('mysql_connect') ? true : false,
    'openssl '  => function_exists('openssl_sign') ? true : false,
    'gd      '  => function_exists('gd_info') ? true : false,
    'curl    '  => function_exists('curl_init') ? true : false
);

$failed = false;

foreach ($checks as $k => $v) {
    $str = $colorizer->getColoredString(' OK ', 'white', 'green');
    if (!$v) {
        $str = $colorizer->getColoredString(' Not found ', 'white', 'red');
        $failed = true;
    }

    \cli\line($k.' '.$str);
}
\cli\line("\n");

if ($failed) {
    $errorDump('Please check the requirements and try again.');
}

\cli\line("Let's create your config files...");

$db_host = false;
$db_user = false;
$db_pass = false;
$db_name = false;

$db_host = \cli\prompt('Database host', 'localhost', ': ');
$db_user = \cli\prompt('Database user', 'dbuser', ': ');
$db_pass = \cli\prompt('Database password', 'dbpass', ': ');
$db_name = \cli\prompt('Database name', 'gi', ': ');

\cli\line('');
\cli\out('Checking database connection... ');
$link = mysql_connect($db_host, $db_user, $db_pass);
if (!$link) {
    \cli\line('');
    $errorDump('Could not connect to database');
}

\cli\out($colorizer->getColoredString(' OK ', 'white', 'green'));
\cli\line("\n");

if (mysql_select_db($db_name, $link)) {
    \cli\line("Looks like that database already exists.");
    if (strtolower(\cli\choose('Should I continue', 'Yn', 'n')) !== 'y') {
        exit(1);
    }
} else {
    \cli\out("Creating database... ");
    $q = 'create database '.$db_name.' character set utf8 collate utf8_general_ci';
    if (mysql_query($q, $link)) {
        \cli\out($colorizer->getColoredString(' OK ', 'white', 'green'));
    } else {
        $errorDump('Unable to create database');
    }
}

\cli\line('');
\cli\line('Running migrations... ');

$_SERVER['PHINX_DBHOST'] = $db_host;
$_SERVER['PHINX_DBNAME'] = $db_name;
$_SERVER['PHINX_DBUSER'] = $db_user;
$_SERVER['PHINX_DBPASS'] = $db_pass;
$_SERVER['PHINX_CONFIG_DIR'] = __DIR__;

$phinxWrapper = new Phinx\Wrapper\TextWrapper($phinx, array('configuration' => __DIR__.'/phinx.yml', 'parser' => 'yaml'));

$phinxWrapper->getMigrate();

mysql_close($link);

\cli\line('');

$buildDbConfig = true;
if (file_exists('application/config/database.php')) {
    $buildDbConfig = false;
    \cli\line("Your database config already exists!");
    if (strtolower(\cli\choose('Should I replace it', 'yn', 'n')) === 'y') {
        $buildDbConfig = true;
    }

}

if ($buildDbConfig) {
    \cli\out("Building database config... ");
    $config_file = file_get_contents('application/config/database-example.php');

    $replacements = array(
        'HOSTNAME'  =>  $db_host,
        'USERNAME'  =>  $db_user,
        'PASSWORD'  =>  $db_pass,
        'DATABASE'  =>  $db_name
    );

    $config_file = str_replace(array_keys($replacements), $replacements, $config_file);

    file_put_contents('application/config/database.php', $config_file);
    \cli\out($colorizer->getColoredString(' OK ', 'white', 'green'));
}

\cli\line("\n");

$buildSystemConfig = true;
if (file_exists('application/config/config.php')) {
    $buildSystemConfig = false;
    \cli\line("Your system config already exists!");
    if (strtolower(\cli\choose('Should I replace it', 'yn', 'n')) === 'y') {
        $buildSystemConfig = true;
    }

}

do {
    $continue = true;
    if ($buildSystemConfig) {
        $config_file = file_get_contents('application/config/config-example.php');

        $url = sprintf('http://%s/', gethostname());

        if (array_key_exists('USER', $_SERVER) && $_SERVER['USER'] == 'vagrant') {
            $url = sprintf('http://%s/', '192.168.10.11');
        }

        $questions = array(
            'Stripe public key'                         => array(false, 'STRIPE_PK'),
            'Stripe secret key'                         => array(false, 'STRIPE_SK'),
            'Site Base URL'                             => array($url, 'GI_BASE_URL'),
            'S3 bucket name (blank to disable)'         => array('', 'S3_BUCKET'),
            'S3 access key (blank to disable)'          => array('', 'S3_ACCESS'),
            'S3 secret key'                             => array('', 'S3_SECRET'),
            'Default "from" email'                      => array('', 'NOTIFY@EXAMPLE.COM'),
        );

        $replacements = array(
            'STRIPE_PK' => false,
            'STRIPE_SK' => false,
            'S3_BUCKET' => false,
            'S3_ACCESS' => false,
            'S3_SECRET' => false,
            'NOTIFY@EXAMPLE.COM' => false,
            'GI_BASE_URL' => false,
            'THIS_IS_RANDOM' => md5((time()+microtime()).uniqid())
        );

        foreach ($questions as $q => $val) {
            $replacements[$val[1]] = \cli\prompt($q, $val[0], ': ');
        }

        // make sure it's complete
        $url = $replacements['GI_BASE_URL'];
        if (!parse_url($url, PHP_URL_SCHEME)) {
            $url = 'http:/'.parse_url($url, PHP_URL_HOST).'/'.parse_url($url, PHP_URL_PATH);
        }
        if (substr($url, -1) !== '/') {
            $url .= '/';
        }

        $replacements['GI_BASE_URL'] = $url;

        if (!$replacements['STRIPE_PK']) {
            $replacements['STRIPE_PK'] = \cli\prompt('Stripe public key is required', false, ': ');
        }
        if (!$replacements['STRIPE_SK']) {
            $replacements['STRIPE_SK'] = \cli\prompt('Stripe secret key is required', false, ': ');
        }
        if (!$replacements['NOTIFY@EXAMPLE.COM']) {
            $replacements['NOTIFY@EXAMPLE.COM'] = \cli\prompt('I cannot send email without a reply-to address', false, ': ');
        }

        \cli\line('');
        foreach ($questions as $q => $val) {
            \cli\line($q.': '.$replacements[$val[1]]);
        }

        \cli\line('');
        if (strtolower(\cli\choose('Is this OK', 'yn', 'n')) === 'n') {
            $continue = false;
            continue;
        }

        \cli\out("Building system config... ");

        $config_file = str_replace(array_keys($replacements), $replacements, $config_file);

        file_put_contents('application/config/config.php', $config_file);

        \cli\out($colorizer->getColoredString(' OK ', 'white', 'green'));
    }
} while (!$continue);

\cli\line("\n");

if (strtolower(\cli\choose("Should I install the default htaccess file", "yn", 'n')) === 'y') {
    copy('./www/htaccess.txt', './www/.htaccess');
    // unlink('./www/htaccess.txt');
}

\cli\line('');
\cli\line('All done!');
