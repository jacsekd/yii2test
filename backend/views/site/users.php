<?php

/* @var $this yii\web\View */
/* @var $searchModel common\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

use yii\helpers\Html;
use common\models\User;
use yii\grid\GridView;

$this->title = 'Users';
$this->params['breadcrumbs'][] = $this->title;
?>
<head>
</head>
<body>
<h1>Users</h1>
<div class="mobillock1">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'username',
                'filter' => true,
            ],
            'email',
            ['class' => 'yii\grid\DataColumn', 'label' => 'Admin', 'value' => function($i) {
                if ($i->admin == 1) {
                    return "Admin";
                }
                return "Not an admin";
            }],
            ['class' => 'yii\grid\DataColumn', 'label' => 'Active', 'value' => function($i) {
                if ($i->status == User::STATUS_ACTIVE) {
                    return "Active profile";
                }
                return "Inactive profile";
            }],
            ['class' => 'yii\grid\ActionColumn',
                'header' => 'View / Edit / Delete',
                'template' => '{view-tickets} {view-profile} {edit-profile} {delete}',
                'buttons' => [
                        'delete' => function ($url) {
                            return Html::a('<span style="padding: 5px" class="glyphicon glyphicon-trash"></span>', $url, [
					            'title' => Yii::t('yii', 'Delete user'),
					            'data-confirm' => Yii::t('yii', 'Are you sure to delete this profile?'),
					            'data-method' => 'post',
				            ]);
                        },
                        'view-profile' => function ($url) {
                            return Html::a('<span style="padding: 5px" class="glyphicon glyphicon-user"></span>', $url, [
					            'title' => Yii::t('yii', 'View user\'s profile'),
                                'target' => "_blank",
				            ]);
                        },
                        'view-tickets' => function ($url, $model, $key) {
                            $url = "view-tickets?id=".$key."&exact=true";
                            return Html::a('<span style="padding: 5px" class="glyphicon glyphicon-tags"></span>', $url, [
                                'title' => Yii::t('yii', 'View user\'s tickets'),
                                'target' => "_blank",
                            ]);
                        },
                        'edit-profile' => function ($url) {
                        return Html::a('<span style="padding: 5px" class="glyphicon glyphicon-pencil"></span>', $url, [
                            'title' => Yii::t('yii', 'Edit user\'s tickets'),
                            'target' => "_blank",
                        ]);
                    },
                    ]
            ],
        ]
]); ?>

</div>
</body>