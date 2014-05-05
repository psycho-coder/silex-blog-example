<?php

use Silex\Application;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\SecurityServiceProvider;
use Silex\Provider\RememberMeServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Silex\Provider\FormServiceProvider;
use Symfony\Component\Finder\Finder;

use Michelf\MarkdownExtra;

$app = new Application();
$app->register(new UrlGeneratorServiceProvider());
$app->register(new ValidatorServiceProvider());
$app->register(new ServiceControllerServiceProvider());
$app->register(new TwigServiceProvider());
$app->register(new DoctrineServiceProvider());
$app->register(new SessionServiceProvider());
$app->register(new SecurityServiceProvider());
$app->register(new RememberMeServiceProvider());
$app->register(new TranslationServiceProvider());
$app->register(new FormServiceProvider());

$app['twig'] = $app->share($app->extend('twig', function($twig, $app) {
    // add custom globals, filters, tags, ...

    $twig->addFilter(new Twig_SimpleFilter('markdown2html', function($string) use ($app){
        return $app['markdown']->transform($string);
    }, array('is_safe' => array('html'))));

    return $twig;
}));

/*
 * **************************************
 * Security
 * **************************************
 */
$app['security.firewalls'] = array(
    'login' => array(
        'pattern' => '^/admin/login$'
    ),
    'secured' => array(
        'pattern' => '^/admin',
        'form'    => array(
            'login_path' => '/admin/login', 
            'check_path' => '/admin/login_check',
            'default_target_path' => '/admin'
        ),
        'logout'  => array('logout_path' => '/admin/logout', 'target_url' => '/admin'),
        'users'   => $app->share(function() use ($app){
            return new Blog\Repository\UserRepository($app['db']);
        }),
        'remember_me' => array(
            'key' => 'Choose_A_Unique_Random_Key',
            'always_remember_me' => true,
        )
    )
);

/*
 * **************************************
 * Repositories
 * **************************************
 */

$app['repository.user'] = $app->share(function() use ($app){
    return new Blog\Repository\UserRepository($app['db']);
});

$app['repository.post'] = $app->share(function() use ($app){
    $repository = new Blog\Repository\PostRepository($app['db']);
    $repository->setSlugify($app['slugify']);
    return $repository;
});

/*
 * **************************************
 * Translation
 * **************************************
 */

$app['translator'] = $app->share($app->extend('translator', function($translator, $app){
    // Fallback locales for the translator. It will be used when the current locale has no messages set.
    $translator->setFallbackLocale('ru');

    $finder = new Finder();
    // Translations directory structure example:
    //   - translations
    //     -- ru
    //       --- messages.ru.xlf
    //       
    $finder->files()->depth('== 1')->name('*.xlf')->in($app['translations.path']);
    foreach ($finder as $file) {
        $translator->addResource('xliff', $file->getRealpath(), $file->getRelativePath());
    }

    return $translator;
}));

/*
 * **************************************
 * Pagination
 * **************************************
 */

$app['pagination.post'] = $app->share(function() use ($app){
    $pagination = new Blog\Util\Pagination($app['repository.post']->getPaginationQueryBuilder());
    return $pagination;
});

/*
 * **************************************
 * Utils
 * **************************************
 */

$app['slugify'] = $app->share(function() use ($app){
    $slugify = new Cocur\Slugify\Slugify();

    // add custom rules here

    $slugify->addRule('ь', '');

    return $slugify;
});

$app['faker'] = $app->share(function(){
    return Faker\Factory::create('ru_RU');
});

$app['markdown'] = $app->share(function(){
    $parser = new MarkdownExtra();
    // add settings here
     
    $parser->code_class_prefix = 'language-';

    return $parser;
});

return $app;
