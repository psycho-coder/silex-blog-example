<?php

namespace Blog\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class AdminController
{
    public function indexAction(Request $request, Application $app)
    {
        //return $app['twig']->render('admin/index.html.twig');
        
        // temporarily just redirect to posts page
        return $app->redirect($app['url_generator']->generate('admin_post_index'));
    }

    public function loginAction(Request $request, Application $app)
    {
        return $app['twig']->render('admin/login/form.html.twig', array(
            'error'         => $app['security.last_error']($request),
            'last_username' => $app['session']->get('_security.last_username')
        ));
    }

    public function previewAction(Request $request, Application $app)
    {
        return $app['markdown']->transform(
            $request->get('content', $app['translator']->trans('app.controller.admin.preview.empty'))
        );
    }
}