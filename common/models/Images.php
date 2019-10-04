<?php
namespace common\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;


/**
 * Comment model
 * @property integer updated_at
 * @property integer $id
 * @property integer $ticket_id
 * @property string $file_path
 * @property integer $created_at
 * @property string $extension
 */
class Images extends ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%images}}';
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
