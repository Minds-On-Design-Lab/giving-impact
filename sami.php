<?php

$i = Symfony\Component\Finder\Finder::create()
    ->files()
    ->name('*.php')
    ->exclude('third_party')
    ->exclude('libraries')
    ->in(__DIR__.'/application');

return new Sami\Sami($i, array(
    'title'                => 'Giving Impact',
    'build_dir'            => __DIR__.'/docs',
    'cache_dir'            => __DIR__.'/cache',
    'default_opened_level' => 2,
));
