<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

//Request::setTrustedProxies(array('127.0.0.1'));

/**
 * Simple function to shorten controller names
 * @param  String $alias Alias to action (Controller/Action)
 * @return String        Full action name 
 */
function controller( $alias ) {
    $classNames = explode('/', $alias);
    $methodName = array_pop($classNames);

    $controllerName = implode('', array_map('ucfirst', $classNames));

    return sprintf('Blog\Controller\%sController::%sAction', $controllerName, $methodName);
}

/**
 * ************************************************************
 * Main routes
 * ************************************************************
 */
$app->get('/', controller('post/index'))->bind('homepage');

$app->get('/posts/page/{page}', controller('post/index'))
    ->bind('post_index')
    ->assert('id', '\d+');

$app->get('/posts/{slug}', controller('post/show'))
    ->bind('post_show');

/**
 * ************************************************************
 * Auth route
 * ************************************************************
 */
$app->get('/admin/login', controller('admin/login'));

/**
 * ************************************************************
 * Admin routes
 * ************************************************************
 */
$app->get('/admin', controller('admin/index'))->bind('admin_index');

$app->get('/admin/posts', controller('admin/post/index'))
    ->bind('admin_post_index')
    ->before(function (Request $request) use ($app){
        // /admin/posts?page=1 => /admin/posts
        if ( $request->get('page') == 1 ) {
            return $app->redirect($app['url_generator']->generate('admin_post_index'));
        }
    });

$app->match('/admin/posts/new', controller('admin/post/new'))->bind('admin_post_new');

$app->match('/admin/posts/{id}/edit', controller('admin/post/edit'))
    ->bind('admin_post_edit')
    ->assert('id', '\d+');

$app->get('/admin/posts/{id}/delete', controller('admin/post/delete'))
    ->bind('admin_post_delete')
    ->assert('id', '\d+');

$app->post('/admin/markdown-preview', controller('admin/preview'))
    ->bind('admin_markdown_preview');

/**
 * ************************************************************
 * Errors
 * ************************************************************
 */
$app->error(function (\Exception $e, $code) use ($app) {
    if ($app['debug']) {
        return;
    }

    // 404.html, or 40x.html, or 4xx.html, or error.html
    $templates = array(
        'errors/'.$code.'.html.twig',
        'errors/'.substr($code, 0, 2).'x.html.twig',
        'errors/'.substr($code, 0, 1).'xx.html.twig',
        'errors/default.html.twig',
    );

    return new Response($app['twig']->resolveTemplate($templates)->render(array('code' => $code)), $code);
});
