<?php

/* @var $this yii\web\View */
/* @var $model \common\models\ViewProfile */

use common\models\User;

$this->title = 'View profile - '.$model->user->username;
$this->params['breadcrumbs'][] = $this->title;
?>
<head>
    <style>
        table{
            width:100%;
        }
        td{
            height:35px;
        }
        tr:nth-child(odd) {
            background-color:#f5f5f5;
        }
        th,td{
            padding: 15px;
            text-align: left;
        }
        .ticket-1{
            color: red;
        }
        .ticket-1:hover{
            color: #FF9900;
        }
        .ticket-0{
            color: green;
        }
        .ticket-0:hover{
            color: #779900;
        }
    </style>
</head>
<body>

<h1><?= $model->user->username ?></h1>
<?=  $model->user->admin ? 'Admin' : '' ?>
<?=  $model->user->status == User::STATUS_INACTIVE ? 'Inactive' : '' ?>
<br>
<br>
<table>
    <tr>
        <td>Username:</td>
        <td><?= $model->user->username ?></td>
    </tr>
    <tr>
        <td>
            E-mail:
        </td>
        <td>
            <?= $model->user->email ?>
        </td>
    </tr>
    <tr>
        <td>
            Last login:
        </td>
        <td>
            <?= Yii::$app->formatter->format($model->user->last_login,'datetime') ?>
        </td>
    </tr>
    <tr>
        <td>
            Last update time:
        </td>
        <td>
            <?= Yii::$app->formatter->format($model->user->updated_at,'datetime') ?>
        </td>
    </tr>
    <tr>
        <td>
            Registration time:
        </td>
        <td>
            <?= Yii::$app->formatter->format($model->user->created_at,'datetime') ?>
        </td>
    </tr>
</table>

<h3>Tickets:</h3>
<?php
if ($model->tickets != null) {
    echo '<ul>';
    foreach ($model->tickets as $ticket) {
        ?>
        <li style="font-size:20px">
            <a class="ticket-<?=$ticket->status?>" href="view-ticket?id=<?=$ticket->id?>"><?=$ticket->title?></a>
        </li>
<?php
    }
    echo '</ul>';
} else {
    echo 'There are no tickets for this user.';
}
?>
</body>