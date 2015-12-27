<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\assets\HandlebarsAsset;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\ActiveForm;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

AppAsset::register($this);
HandlebarsAsset::register($this);

$this->registerJs('
var STEAMID = ' . Yii::$app->user->identity->steamid . ';
var USERNAME = "' . Yii::$app->user->identity->username . '";
');
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => 'My Company',
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'encodeLabels' => false,
        'items' => [
            ['label' => 'Home', 'url' => ['/site/index']],
            Yii::$app->user->isGuest ?
                ['label' => 'Login', 'url' => ['/site/login']] :
                [
                    'label' => Html::img(Yii::$app->user->identity->avatar_md, [
                        'class' => 'avatar img-circle'
                    ]),
                    'linkOptions' => [
                        'class' => 'hasAvatar'
                    ],
                    'items' => [
                        [
                            'label' => 'Logout',
                            'url' => ['/site/logout'],
                            'linkOptions' => ['data-method' => 'post']
                        ],
                    ]
                ],
        ],
    ]);
    NavBar::end();
    ?>

    <div class="container">
        <div class="row">
            <div class="col-sm-6 col-sm-offset-3">
                <div class="chat panel panel-default">
                    <div class="panel-heading">
                        Чат
                    </div>
                    <div class="panel-body">
                        <div id="chat">
                            <?php foreach (\app\models\Message::find()->limit(15)->all() as $msg): ?>
                                <div class="chat-post" data-steamid="<?= $msg->steamid ?>">
                                    <a href="<?= $msg->user->profile_url ?>" target="_blank">
                                        <img class="chat-avatar img-circle" src="<?= $msg->user->avatar_md ?>">
                                    </a>
                                    <dl class="chat-body">
                                        <dt class="username"><a href="<?= $msg->user->profile_url ?>"
                                                                target="_blank"><?= $msg->user->username ?></a></dt>
                                        <dd class="message"><?= \yii\helpers\Html::encode($msg->message) ?></dd>
                                    </dl>
                                </div>
                            <?php endforeach ?>
                        </div>
                    </div>

                    <div class="panel-footer">
                        <form id="send">
                            <div class="input-group">
                                <input type="text" class="form-control" id="message" placeholder="Message..."
                                       autocomplete="off">
                        <span class="input-group-btn">
                            <button class="btn btn-default" type="submit">Send</button>
                        </span>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>


        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; My Company <?= date('Y') ?></p>

        <p class="pull-right"><?= Yii::powered() ?></p>
    </div>
</footer>

<script id="chat-post" type="text/x-handlebars-template">
    <div class="chat-post" data-steamid="{{steamid}}">
        <a href="{{profile_url}}" target="_blank"><img class="chat-avatar" src="{{avatar}}"></a>
        <dl class="chat-body">
            <dt class="username"><a href="{{profile_url}}" target="_blank">{{username}}</a></dt>
            <dd class="message">{{message}}</dd>
        </dl>
    </div>
</script>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
