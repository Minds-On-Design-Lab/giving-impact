<?php

$i = Symfony\Component\Finder\Finder::create()
    ->files()
    ->name('*.php')
    ->in(__DIR__.'/MODL');

return new Sami\Sami($i, array(
    'title'                => 'Giving Impact API',
    'build_dir'            => __DIR__.'/docs',
    'cache_dir'            => __DIR__.'/cache',
    'default_opened_level' => 2
));
