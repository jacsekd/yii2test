<?php
namespace frontend\models;

use Yii;
use yii\base\Model;
use common\models\User;
use yii\db\IntegrityException;

/**
 * Signup form
 */
class EditProfileForm extends Model
{
    public $username;
    public $email;

    public $user_id;
    public $reg_time;
    public $update_time;
    public $last_login;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['username', 'trim'],
            ['username', 'required'],
            ['username', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This username has already been taken.', 'filter' => ['!=', 'id', $this->user_id]],
            ['username', 'string', 'min' => 2, 'max' => 255],

            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This email address has already been taken.', 'filter' => ['!=', 'id', $this->user_id]],

        ];
    }

    /**
     * Sets up the model's values with a users attributes with the given id
     *
     * @param integer $id
     */
    public function getData($id){
        $user = User::findOne($id);
        $this->user_id = $user->id;
        $this->username = $user->username;
        $this->email = $user->email;
        $this->reg_time = $user->created_at;
        $this->update_time = $user->updated_at;
        $this->last_login = $user->last_login;
    }

    /**
     * Changes the current user's data to the new ones
     *
     * @return bool
     */
    public function edit()
    {
        $user = User::findOne(Yii::$app->user->id);
        $user->username = $this->username;
        $user->email = $this->email;
        try {
            $user->save();
            return true;
        } catch(IntegrityException $e) {
            return false;
        }
    }


}
