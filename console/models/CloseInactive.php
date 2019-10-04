<?php
namespace  console\models;

use yii\base\Model;
use Yii;
use common\models\Ticket;
use yii\db\Query;

class CloseInactive extends Model
{
    public $closedTickets;
    public $i;


    /**
     * Gets all the data from the database which are needed and checks if the last comment was made more than $time ago
     * If so, it closes that ticket
     *
     * @param integer $time The time in seconds, set to two weeks if nothing given
     * @return bool
     * @throws \yii\db\Exception
     */
    public function close($time)
    {
        if ($time == null) {
            $time = 1209600;
        }

        $query = new Query();
        $asd = $query
            ->select("ticket.id AS `id`, ticket.status, ticket.title, ticket.admin_id, ticket.last_comment_time, comment.created_at AS `Comment Created`, comment.author_id AS `Comment Author`")
            ->from("ticket")
            ->leftJoin("comment", "ticket.id=comment.ticket_id")
            ->andWhere(["ticket.status" => 1])
            ->andWhere('`last_comment_time` = `comment`.`created_at`')
            ->andWhere('`admin_id` = `comment`.`author_id`')
            ->createCommand()
            ->queryAll();

        $this->i = 0;
        foreach($asd as $a){
            $ticket = Ticket::findOne($a['id']);
            if($a['last_comment_time'] < time()-$time){
                $ticket->status = 0;
                $this->closedTickets[$this->i] = $ticket->title;
                $this->i++;
                $ticket->save();
            }
        }
        return true;
    }

}
