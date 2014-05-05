<?php

// configure your app for the production environment


// Default locale
$app['locale'] = 'ru';

// Path to translation files
$app['translations.path'] = realpath(__DIR__.'/../translations');

// Application name
$app['app.name'] = 'Silex Blog Example';

$app['twig.path'] = array(__DIR__.'/../views');
$app['twig.options'] = array('cache' => __DIR__.'/../var/cache/twig');

// Database settings
$app['db.options'] = array(
    'driver' => 'pdo_sqlite',
    'path'   => __DIR__.'/../var/db/app.db'
);

$app['db.schema'] = array(
    'tables_path' => __DIR__.'/db/tables.php'
);
