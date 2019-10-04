<?php
namespace frontend\models;

/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 9/17/19
 * Time: 1:28 PM
 */

use Yii;
use yii\base\Model;
use common\models\User;
use common\models\Ticket;
use common\models\Comment;
use yii\db\Query;

class ViewTicket extends Model
{
    public $me;
    public $admin;
    public $ticket;
    public $author;
    public $comments;
    public $names;
    public $images;

    public $new_comment;

    public function rules()
    {
        return [
            ['new_comment', 'required'],
            ['verifyCode', 'captcha'],
        ];
    }


    public function attributeLabels()
    {
        return [

        ];
    }

    /**
     * Sets up the model's values with the ticket's data with the given id
     * $me User active record The current logged in user
     * $admin User active record The admin who is assigned to this ticket
     * $ticket Ticket active record The requested ticket
     * $author User active record The ticket's author
     * $comments[] Comment active record Holds all the comments this ticket has
     * $names[] string Holds all the usernames of a comment
     * $images[] Images active record Holds all the images this ticket has
     *
     * @param integer $id Ticket id
     * @return bool
     */
    public function setData($id)
    {
        $ticket = Ticket::findOne($id);
        if ($ticket != null) {
            $this->author = $ticket->getAuthor()->one();
            if ($this->author != null) {
                $this->me = USER::findOne(Yii::$app->user->id);
                $this->admin = $ticket->getAdmin()->one();
                $this->ticket = $ticket;
                $this->images = $ticket->getImages()->all();
                $this->comments = $ticket->getComments()->all();

                foreach ($this->comments as $comment) {
                    $this->names[$comment->author_id] = User::findOne($comment->author_id)->username;
                }

                return true;
            }
        }
        return false;
    }

    /**
     * Changes the ticket's status to closed or open according to the given parameters
     *
     * @param integer $i The ticket's id
     * @param integer $s The status it should be changed to, 1 = open, 0 = closed
     * @return bool
     */
    public static function changeTicket($i, $s)
    {
        $ticket = Ticket::findOne($i);
        if (self::verifyUser($ticket)) {
            $ticket->status = $s;
            $ticket->save();
            return true;
        }
        return false;
    }

    /**
     * Checks if the logged in user is the ticket's author or an admin
     *
     * @param Ticket $ticket
     * @return bool
     */
    public static function verifyUser($ticket)
    {
        if ($ticket != null && !Yii::$app->user->isGuest) {
            if (User::findOne(Yii::$app->user->id)->admin || Yii::$app->user->id == $ticket->author_id) {
                return true;
            }
        }
        return false;
    }

    /**
     * Adds a comment to a ticket
     *
     * @param string $sid Ticket secret_id
     * @param integer $tid Ticket id
     * @param integer $author_id Ticket author_id
     * @return bool
     */
    public function addComment($sid, $tid, $author_id)
    {
        $ticket = Ticket::findOne(['secret_id' => $sid]);
        if ($ticket->admin_id == $author_id || $ticket->author_id == $author_id) {
            if ($ticket == null || $ticket->id != $tid) {
                return false;
            }

            $comment = new Comment();
            $comment->ticket_id = $tid;
            $comment->author_id = $author_id;
            $comment->text = $this->new_comment;
            $comment->save();

            $ticket->last_comment_time = time();
            $ticket->status = 1;
            $ticket->save();

            return true;
        }
        return false;
    }


    /**
     * Assigns the user(admin) to the ticket
     *
     * @param integer $tid Ticket id
     * @param integer $aid User id, that is going to be set as the ticket's admin
     * @return int
     * @throws \yii\db\Exception
     */
    public static function setAdmin($tid, $aid)
    {
        $query = new Query();
        return $query
            ->createCommand()
            ->update('ticket', ['admin_id' => $aid], 'id = '.$tid)
            ->execute();
    }

    /**
     * Deletes a Comment active record with the given id
     *
     * @param integer $cid Comment id
     * @return bool
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public static function deleteComment($cid){
        $comment = Comment::findOne($cid);
        $comment->delete();
        return true;
    }

}
