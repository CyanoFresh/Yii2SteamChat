<?php

namespace app\controllers;

use app\models\User;
use nodge\eauth\ErrorException;
use nodge\eauth\openid\ControllerBehavior;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\VarDumper;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

class SiteController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'token'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
            'eauth' => [
                'class' => ControllerBehavior::className(),
                'only' => ['login'],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionLogin()
    {
        /** @var $eauth \nodge\eauth\ServiceBase */
        $eauth = Yii::$app->get('eauth')->getIdentity('steam');

        $eauth->setRedirectUrl(Yii::$app->getUser()->getReturnUrl());
        $eauth->setCancelUrl(Yii::$app->getUrlManager()->createAbsoluteUrl('site/login'));

        try {
            if ($eauth->authenticate()) {
                $identity = User::findByEAuth($eauth);

                $user = User::findOne(['steamid' => $identity->steamid]);

                if (!$user) {
                    $user = new User();
                }

                $user->username = $identity->username;
                $user->steamid = $identity->steamid;
                $user->profile_url = $identity->profile_url;
                $user->avatar = $identity->avatar;
                $user->avatar_md = $identity->avatar_md;
                $user->avatar_lg = $identity->avatar_lg;
                $user->generateAuthKey();

                $user->save();

                Yii::$app->getUser()->login($identity);

                $eauth->redirect();
            } else {
                $eauth->cancel();
            }
        } catch (ErrorException $e) {
            Yii::$app->getSession()->setFlash('error', 'EAuthException: ' . $e->getMessage());

            $eauth->redirect($eauth->getCancelUrl());
        }
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionToken()
    {
        if (!Yii::$app->request->isAjax) {
            return 'Permission denied';
        }

        $steamid = Yii::$app->user->identity->steamid;

        if (!$steamid) {
            return 'nologin';
        }

        $user = User::findOne(['steamid' => $steamid]);

        if (!$user) {
            return 'nologin';
        }

        $user->generateAuthKey();
        $token = $user->getAuthKey();

        $user->save();

        return '?steamid=' . $steamid . '&time=' . time() . '&token=' . $token;
    }
}
