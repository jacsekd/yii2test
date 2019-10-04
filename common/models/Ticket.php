<?php

namespace common\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "ticket".
 *
 * @property int $updated_at
 * @property string $text
 * @property int $id
 * @property string $title
 * @property int $status
 * @property int $author_id
 * @property int $created_at
 * @property int $last_comment_time
 * @property int $admin_id
 * @property string $secret_id
 *
 * @property Comment[] $comments
 * @property Images[] $images
 * @property User $admin
 * @property User $author
 */
class Ticket extends ActiveRecord
{

    public static $q = false;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%ticket}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }
    public function rules()
    {
        return [

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'updated_at' => 'Updated At',
            'text' => 'Text',
            'id' => 'ID',
            'title' => 'Title',
            'status' => 'Status',
            'author_id' => 'Author ID',
            'created_at' => 'Created At',
            'last_comment_time' => 'Last Comment Time',
            'admin_id' => 'Admin ID',
            'secret_id' => 'Secret ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getComments()
    {
        return $this->hasMany(Comment::className(), ['ticket_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAdmin()
    {
        return $this->hasOne(User::className(), ['id' => 'admin_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthor()
    {
        return $this->hasOne(User::className(), ['id' => 'author_id']);
    }

    /**
     * {@inheritdoc}
     * @return TicketQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TicketQuery(get_called_class());
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getImages()
    {
        return $this->hasMany(Images::className(), ['ticket_id' => 'id']);
    }
}
