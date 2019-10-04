<?php
namespace console\controllers;

use yii\console\Controller;
use console\models\CloseInactive;

class CloseInactiveController extends Controller
{

    public $seconds;
    public $minutes;
    public $hours;
    public $days;
    public $weeks;

    public function options($actionID)
    {
        return [
            'seconds',
            'minutes',
            'hours',
            'days',
            'weeks',
            ];
    }

    public function optionAliases()
    {
        return [
            's' => 'seconds',
            'm' => 'minutes',
            'h' => 'hours',
            'd' => 'days',
            'w' => 'weeks',
            ];
    }

    /**
     * Sets $time with the given options and give that number to the model->close
     * If it has given back more than 0 closed tickets, echo their title's
     *
     * @throws \yii\db\Exception
     */
    public function actionIndex()
    {
        $time = 0 + $this->seconds + $this->minutes * 60 + $this->hours * 3600 + $this->days * 86400 + $this->weeks * 86400 * 7;

        $model = new CloseInactive();
        if ($model->close($time)) {
            if ($model->i == 0) {
                echo 'No ticket has been closed.'.PHP_EOL;
            } else {
                foreach ($model->closedTickets as $ticket) {
                    echo $ticket.' has been closed.'.PHP_EOL;
                }
            }
        }
    }
}
