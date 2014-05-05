<?php

namespace Blog\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

use Blog\Entity\Post;
use Blog\Form\Type\PostType;

class AdminPostController
{
    public function indexAction(Request $request, Application $app)
    {
        $pagination  = $app['pagination.post'];
        $currentPage = $request->get('page', 1);
        $app['session']->set('admin.post.page', $currentPage);

        $posts = $pagination->getPage($currentPage);

        return $app['twig']->render('admin/post/index.html.twig', array(
            'posts'       => $posts,
            'pagination'  => $pagination,
            'currentPage' => $currentPage
        ));
    }

    public function newAction(Request $request, Application $app)
    {
        $post = new Post();
        $form = $app['form.factory']->create(new PostType(), $post);

        $form->handleRequest($request);

        if ( $form->isValid() )
        {
            $app['repository.post']->save($post);
            $app['session']->getFlashBag()->add('success', 'controllers.admin.post.new.success');
            return $app->redirect(
                $app['url_generator']->generate('admin_post_edit', array('id' => $post->getId()))
            );
        }

        return $app['twig']->render('admin/post/new.html.twig', array(
            'form' => $form->createView()
        ));
    }

    public function editAction(Request $request, Application $app)
    {
        $post = $app['repository.post']->find($request->attributes->get('id'));
        if ( !$post ) {
            $app->abort(404, 'controllers.admin.post.edit.404');
        }

        $form = $app['form.factory']->create(new PostType(), $post);
        $form->handleRequest($request);

        if ( $form->isValid() ) 
        {
            $app['repository.post']->save($post);
            $app['session']->getFlashBag()->add('success', 'controllers.admin.post.edit.success');
            return $app->redirect($app['url_generator']->generate('admin_post_edit', array('id' => $post->getId())));
        }

        return $app['twig']->render('admin/post/edit.html.twig', array(
            'form' => $form->createView(),
            'post' => $post
        ));
    }

    public function deleteAction(Request $request, Application $app)
    {
        $affected = $app['repository.post']->delete($request->attributes->get('id'));
        if ( $affected ) {
            $app['session']->getFlashBag()->add('success', 'controllers.admin.post.delete.success');
        } else {
            $app['session']->getFlashBag()->add('error', 'controllers.admin.post.delete.error');
        }

        return $app->redirect(
            $app['url_generator']->generate('admin_post_index', array(
                'page' => $app['session']->get('post.page', 1)
            ))
        );
    }
}