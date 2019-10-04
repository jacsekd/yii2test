<?php

/* @var $this yii\web\View */
/* @var $model \frontend\models\ViewTicket */


use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'View Ticket - '.$model->ticket->title;
$this->params['breadcrumbs'][] = $this->title;
?>
<head>
    <style>
        #ticket-open{
            background-color: #EE9999;
            font-size: 20px;
            padding: 10px;
            border-radius: 15px;
        }
        #ticket-closed{
            background-color: #AAFFAA;
            font-size: 20px;
            padding: 10px;
            border-radius: 15px;
        }
        #ticket-text-open{
            border-radius: 10px;
            padding: 10px;
            background-color: #FF9999;
            font-size: 14px;
            margin-top: 40px;
        }
        #ticket-text-closed{
            border-radius: 10px;
            padding: 10px;
            background-color: #99FF99;
            font-size: 14px;
            margin-top: 40px;
        }
        .a-name{
            color: blue;
        }
        .a-name:hover{
            color: white;
        }
        #comment-au{
            margin-left: 30%;
            width: 70%;
            background-color: #EEEEEE;
            border-radius: 15px;
            padding: 10px;
            margin-top: 5px;
            margin-bottom: 5px;
        }
        #comment-ad{
            width: 70%;
            background-color: #DEDEDE;
            border-radius: 15px;
            padding: 10px;
            margin-top: 5px;
            margin-bottom: 5px;
        }
        .image{
            margin: 10px;
            width: 31.5%;
            border-radius: 10%;
            box-shadow: 0 0 2px 1px rgba(0, 140, 186, 0.5);
        }
        .image:hover{
            box-shadow: 0 0 6px 4px rgba(0, 140, 255, 0.5);
        }
        .right{
            float:right;
        }
        @media screen and (max-width: 1200px){
            .image{
                width: 30%;
            }
            .ticket{
                  margin-bottom :20px;
                  font-size: 16px;
            }
        }
        @media screen and (max-width: 670px){
            .image{
                width: 29%;
            }
            .ticket{
                margin-bottom :8px;
                font-size: 14px;
            }
        }
        @media screen and (max-width: 530px){
            .ticket{
                margin-bottom :10px;
                font-size: 12px;
            }
            .right{
                float:left;
            }
            .image{
                width: 25%;
            }
            .comment{
                font-size: 12px;
            }
            h5{
                font-size: 13px;
            }
            h4{
                font-size: 14px;
            }
        }

    </style>
</head>
<body>
<div id="ticket-<?=$model->ticket->status ? 'open' : 'closed'?>">
    <h1 ><?= $model->ticket->title ?></h1>
    <div class="ticket">
    <div style="float: left">
        Ticket author: <?php
        if ($model->me != null && $model->me->admin) {
            echo '<a class="a-name" target="_blank"href=view-profile?uname='.$model->author->username.'>'.$model->author->username.'</a>.';
        } else {
            echo $model->author->username;
        }
        echo '<br>';
        $t = false;
        if ($model->me != null && $model->author->id == Yii::$app->user->id) {
            echo ' You made this ticket, ';
            $t = true;
        } else if($model->me != null && $model->me->admin) {
            echo 'You are an admin, ';
            $t = true;
        }
        if ($model->ticket->status == 1 && $t) {
            echo 'you can close this by <a class="a-name" href="change-ticket?tid=' . $model->ticket->id . '&st=0"> clicking here.</a>';
        } else if ($model->ticket->status == 0 && $t) {
            echo 'you can reopen this by <a class="a-name" href="change-ticket?tid=' . $model->ticket->id . '&st=1"> clicking here.</a>';
        }
        ?>
        <br>
        <?php
            if ($model->admin != null) {
                if ($model->me != null && $model->me->admin) {
                    echo '<a class="a-name" target="_blank" href=view-profile?uname=' . $model->admin->username . '>' . $model->admin->username . '</a>';
                    if($model->ticket->admin_id == $model->me->id){
                        echo ' (you)';
                    }
                    echo ' is working on this problem.';
                } else {
                    echo $model->admin->username . ' is working on this problem.';
                }
            } else {
                echo 'No admin is working on this problem yet.';
                if ($model->me != null && $model->me->admin && $model->author->id != $model->me->id) {
                    echo ' <a class="a-name" href="/site/assign-admin?id=' . $model->ticket->id . '">Assign yourself</a>';
                }
            }
        if (!$model->ticket->status) {
            echo PHP_EOL.'<br>The ticket is already closed.';
        }
        ?>
    </div>
    <br>
    <div class="right">
        Created at: <?= Yii::$app->formatter->format($model->ticket->created_at, 'datetime')?>
        <br>
        Last comment: <?= Yii::$app->formatter->format($model->ticket->last_comment_time, 'datetime')?>
    </div>
    </div>
    <br>
    <br>
    <div id="ticket-text-<?= $model->ticket->status ? 'open' : 'closed'?>">
        <?= Yii::$app->formatter->asNtext(Html::encode($model->ticket->text))?>
        <?php
        if ($model->images != null) {
            echo '<div><br>';
            foreach ($model->images as $image) {
                echo '<a target="_blank" href="/uploads/'.$image->file_path.'.'.$image->extension.'" ><img class="image" src="/uploads/'.$image->file_path.'.'.$image->extension.'" alt="Image not found"></a>';
            }
            echo '</div>';
        }
        ?>
    </div>
</div>
<div>
<?php
foreach ($model->comments as $comment) {
    ?>
    <div class="comment" id="comment-<?php
    if ($comment->author_id == $model->author->id)
        echo 'au';
    else
        echo 'ad';
    ?>
">
        <h4 style="">Comment author:
            <?php
            if ($model->me != null && $model->me->admin) {
                ?>
                <a target="_blank" class="a-name" href="view-profile?uname=<?=$model->names[$comment->author_id]?>">
                <?= $model->names[$comment->author_id] ?>
                </a>
                <div style="opacity: 0.5; float: right"><a href="delete-comment?cid=<?=$comment->id ?>&tid=<?=$model->ticket->id?>">X</a></div>
            <?php
            } else {
                if ($model->me != null && $comment->author_id == $model->me->id) {
                    echo '<div style="opacity: 0.5; float: right"><a href="delete-comment?cid='.$comment->id.'&tid='.$model->ticket->id.'">X</a></div>';
                }
                echo $model->names[$comment->author_id];
            }
            ?>
        </h4>
        <h5>Comment created: <?= Yii::$app->formatter->format($comment->created_at,'datetime')?></h5>
        <br>
        <?= Html::encode($comment->text) ?>
    </div>

<?php
}
?>
</div>
<?php
if ($model->me != null && $model->admin != null && $model->me->id == $model->admin->id || $model->me != null && $model->author->id == $model->me->id) {
    ?>
        <div style="margin-top: 20px">
            <?php
            $form = ActiveForm::begin(['id' => 'new_comment-form']);
            $form->action = "new-comment";
            ?>

            <?= $form->field($model, 'new_comment')->textarea(['rows' => 6]) ?>
            <div hidden disabled>
                <input name="id" type="text" value=<?= $model->ticket->id?>>
                <input name="sid" type="text" value=<?= $model->ticket->secret_id?>>
            </div>

            <div style="padding-top: 10px" align="right">
                <?= Html::submitButton('Submit', ['class' => 'btn btn-primary', 'name' => 'new_comment-button']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    <?php
}
?>
</body>