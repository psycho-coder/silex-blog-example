<?php

namespace Blog\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class PostController
{
    public function indexAction(Request $request, Application $app)
    {
        $pagination  = $app['pagination.post'];
        $currentPage = $request->get('page', 1);
        $app['session']->set('post.page', $currentPage);

        $posts = $pagination->getPage($currentPage);

        return $app['twig']->render('post/index.html.twig', array(
            'posts'       => $posts,
            'pagination'  => $pagination,
            'currentPage' => $currentPage
        ));
    }

    public function showAction(Request $request, Application $app)
    {
        $post = $app['repository.post']->findBySlug($request->get('slug'));
        if ( !$post ) {
            $app->abort(404, 'controllers.post.edit.404');
        }

        return $app['twig']->render('post/show.html.twig', array(
            'post' => $post
        ));
    }
}