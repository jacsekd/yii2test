<?php

use yii\db\Migration;

class m190911_170900_create_user_table extends Migration
{
    public function up()
    {
        $this->addColumn('user', 'admin',$this->boolean());
    }

    public function down()
    {
        $this->dropColumn('user','admin');
    }
}
