<?php

/* @var $this yii\web\View */
/* @var $titlee string */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchModel common\models\TicketSearch */

use yii\grid\GridView;
use common\models\User;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use common\models\Ticket;
$titlee = "";
if(isset($_GET['TicketSearch']['author.username']) && $_GET['TicketSearch']['author.username'] != ""){
    $titlee = " - ".$_GET['TicketSearch']['author.username'];
}

$this->title = 'Tickets'.$titlee;
$this->params['breadcrumbs'][] = $this->title;
?>
<head>
    <style>
        #open {
            background-color: #FFEEEE;
        }
        #closed {
            background-color: #EEFFEE;
        }
        #closed1 {
            background-color: #DDFFDD;
        }
        #open1 {
            background-color: #FFDDDD;
        }
        .mobillock2{
            display: none;
        }
        @media screen and (max-width: 500px){
            .mobillock1{
                display: none;
            }
            .mobillock2{
                display: block;
            }
        }
    </style>
</head>
<body>
<h1>Tickets<?= $titlee?></h1>

<div class="mobillock1">

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'rowOptions'=>function($model){
        Ticket::$q = !Ticket::$q;
        if($model->status == 1){
            return ['id' => 'open'.Ticket::$q];
        } else {
            return ['id' => 'closed'.Ticket::$q];
        }
    },
    'columns' => [
         [
             'attribute' => 'author.username',
             'format' => 'text',
             'label' => 'Author',
         ],
        'title',
        ['class' => 'yii\grid\DataColumn', 'label' => 'Status', 'value' => function($i) {
            if($i->status == 1){
                return "Ticket is still open";
            }
            return "Ticket is closed";
        }],
        [
            'attribute' => 'last_comment_time',
            'format' => 'datetime',
            'filter' => false,
        ],
        'admin.username:text:Admin',
        ['class' => 'yii\grid\ActionColumn',
            'header' => 'View',
            'template' => '{view-ticket} {view-profile}',
            'buttons' => [
                'view-profile' => function ($url,$model) {
                    $url = "view-profile/".$model->author->id;
                    return Html::a('<span style="padding: 5px" class="glyphicon glyphicon-user"></span>', $url, [
                        'title' => Yii::t('yii', 'View author\'s profile'),
                        'target' => "_blank",
                    ]);
                },
                'view-ticket' => function ($url) {
                    return Html::a('<span style="padding: 5px" class="glyphicon glyphicon-tag"></span>', $url, [
                        'title' => Yii::t('yii', 'View this ticket'),
                        'target' => "_blank",
                    ]);
                },
            ]
        ],
    ],
]); ?>

</div>
<div class="mobillock2">
    You can't view this admin page on a mobile device with this little resolution, please use a computer or a device with big enough screen!
</div>
</body>