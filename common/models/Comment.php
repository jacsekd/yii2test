<?php
namespace common\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;


/**
 * Comment model
 * @property integer updated_at
 * @property integer $id
 * @property integer $ticket_id
 * @property integer $author_id
 * @property integer $created_at
 * @property string $text
 */
class Comment extends ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%comment}}';
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

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [

        ];
    }
}
