<?php
namespace frontend\models;

use common\models\Images;
use Yii;
use yii\base\Model;
use common\models\Ticket;
use yii\db\Query;
use yii\web\UploadedFile;

/**
 * Signup form
 */
class CreateTicketForm extends Model
{
    /**
     * @var UploadedFile[]
     */

    public $title;
    public $text;
    public $imageFiles;

    public $image_path;
    public $verifyCode;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['title', 'required'],
            ['title', 'string', 'min' => 2, 'max' => 255],

            ['text', 'required'],
            ['text', 'string', 'max' => 65500],

            ['verifyCode', 'captcha'],

            [['imageFiles'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg', 'maxFiles' => 9],
        ];
    }

    public function attributeLabels()
    {
        return [
            'file' => 'Images',
            'verifyCode' => 'Verification Code',
        ];
    }

    /**
     * Creates a new Ticket active record, sets it's values
     * If there were uploaded images, it saves them to the server and creates new Images active record which stores the images file paths
     * If everything is saved properly, it returns true
     *
     * @return bool
     * @throws \yii\base\Exception
     * @throws \yii\db\Exception
     */
    public function createTicket()
    {
        $last_ticket_id = Yii::$app->db->createCommand("SELECT id FROM ticket ORDER BY id DESC LIMIT 1")->queryOne();
        $ticket = new Ticket();
        $ticket->id = $last_ticket_id['id']+1;
        $ticket->text = $this->text;
        $ticket->title = $this->title;
        $ticket->status = true;
        $ticket->author_id = Yii::$app->user->id;
        $ticket->last_comment_time = time();
        $ticket->secret_id = Yii::$app->security->generateRandomString();
        $i = 0;
        $ticket->save();
        foreach ($this->imageFiles as $file) {
            $this->image_path = $ticket->id . '-' . time().$i;
            $file->saveAs('uploads/' . $this->image_path . '.' . $file->extension);
            $image = new Images();
            $image->ticket_id = $ticket->id;
            $image->file_path = $this->image_path;
            $image->save();
            $i++;
        }

        return true;
    }
}
