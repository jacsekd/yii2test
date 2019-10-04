<?php

use yii\db\Migration;

class m190925_094600_create_view_usernames extends Migration
{
    public function safeUp()
    {
        $this->execute("CREATE VIEW usernames AS SELECT id, username FROM user");
        $this->execute("CREATE TRIGGER drop_admin AFTER UPDATE ON user FOR EACH ROW UPDATE ticket,user SET admin_id=NULL WHERE user.id=ticket.admin_id AND user.admin=0");
        $this->execute("CREATE TRIGGER delete_images AFTER DELETE ON ticket FOR EACH ROW DELETE FROM images WHERE ticket_id=OLD.id");
        $this->execute("CREATE TRIGGER set_new_last_comment_time AFTER DELETE ON comment FOR EACH ROW UPDATE ticket SET ticket.last_comment_time=(SELECT comment.created_at FROM comment LEFT JOIN ticket ON ticket_id=ticket.id WHERE ticket.id=OLD.ticket_id ORDER BY comment.created_at DESC LIMIT 1) WHERE ticket.id=OLD.ticket_id");
    }

    public function safeDown()
    {
        $this->execute("DROP TRIGGER set_new_last_comment_time");
        $this->execute("DROP TRIGGER delete_images");
        $this->execute("DROP TRIGGER drop_admin");
        $this->execute("DROP VIEW usernames");
    }
}
