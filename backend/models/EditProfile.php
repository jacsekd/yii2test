<?php
namespace backend\models;

use yii\base\model;
use common\models\User;
use yii\db\Query;
use yii\db\IntegrityException;

class EditProfile extends model
{

    public $username;
    public $email;
    public $admin;
    public $status;
    public $id;

    public $adminBool = false;
    public $statusBool = false;

    public function rules()
    {
        return [
            ['username', 'trim'],
            ['username', 'required'],
            ['username', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This username has already been taken.', 'filter' => ['!=', 'id', $this->id]],
            ['username', 'string', 'min' => 2, 'max' => 255],

            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This email address has already been taken.', 'filter' => ['!=', 'id', $this->id]],

            ['adminBool', 'boolean'],
            ['statusBool', 'boolean'],

        ];
    }

    public function getData(User $user){
        $this->id = $user->id;
        $this->username = $user->username;
        $this->email = $user->email;
        $this->admin = $user->admin;
        $this->status = $user->status;
        if ($this->admin == 1) {
            $this->adminBool = true;
        }
        if ($this->status == User::STATUS_ACTIVE) {
            $this->statusBool = true;
        }
    }

    public function saveProfile(){
        $user = User::findOne($this->id);
        $user->username = $this->username;
        $user->email = $this->email;
        $user->status = User::STATUS_INACTIVE + $this->statusBool;
        $user->admin = $this->adminBool;

        try {
            $user->save();
            return true;
        } catch(IntegrityException $e) {
            return false;
        }
    }

}
