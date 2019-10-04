<?php
namespace common\models;

/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 9/16/19
 * Time: 12:42 PM
 */
use yii\base\Model;

class ViewProfile extends Model{

    public $user;
    public $tickets;


    /**
     * Sets the model's values with the given user's data
     * $user User active record with the given id
     * $tickets[] Ticket active record the user's all tickets
     *
     * @param integer $id User id
     * @return bool
     */
    public function getData($id)
    {
        $us = USER::ofUsername($id);
        if ($us != null) {
            $this->user = $us;

            $this->tickets = Ticket::find()
                ->andWhere(['author_id' => $this->user->id])
                ->addOrderBy(['status' => SORT_DESC])
                ->addOrderBy(['last_comment_time' => SORT_DESC])
                ->all();

            return true;
        }

        return false;
    }

}
