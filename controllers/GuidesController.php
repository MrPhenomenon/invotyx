<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class GuidesController extends Controller
{
    /**
     * Renders the list of all guides.
     * URL: /guides
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Renders a single guide based on the slug.
     * URL: /guides/slug-name
     * @param string $slug
     * @return string
     */
    public function actionView($slug)
    {
        return $this->render('view', [
            'slug' => $slug,
        ]);
    }
}